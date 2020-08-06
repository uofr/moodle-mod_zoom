<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Internal library of functions for module zoom
 *
 * All the zoom specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/zoom/lib.php');
require_once($CFG->dirroot.'/mod/zoom/classes/webservice.php');

// Constants.
// Audio options.
define('ZOOM_AUDIO_TELEPHONY', 'telephony');
define('ZOOM_AUDIO_VOIP', 'voip');
define('ZOOM_AUDIO_BOTH', 'both');
// Meeting types.
define('ZOOM_INSTANT_MEETING', 1);
define('ZOOM_SCHEDULED_MEETING', 2);
define('ZOOM_RECURRING_MEETING', 3);
define('ZOOM_SCHEDULED_WEBINAR', 5);
define('ZOOM_RECURRING_WEBINAR', 6);
// Number of meetings per page from zoom's get user report.
define('ZOOM_DEFAULT_RECORDS_PER_CALL', 30);
define('ZOOM_MAX_RECORDS_PER_CALL', 300);
// User types. Numerical values from Zoom API.
define('ZOOM_USER_TYPE_BASIC', 1);
define('ZOOM_USER_TYPE_PRO', 2);
define('ZOOM_USER_TYPE_CORP', 3);

//Added for Creating New Users
define('ZOOM_USER_DOMAIN', 'uregina.ca');

/**
 * Entry not found on Zoom.
 */
class zoom_not_found_exception extends moodle_exception {
    // Web service response.
    public $response = null;
    /**
     * @param string $response  Web service response
     */
    public function __construct($response) {
        $this->response = $response;
        parent::__construct('errorwebservice_notfound', 'mod_zoom', '', $response);
    }
}

/**
 * Couldn't succeed within the allowed number of retries.
 */
class zoom_api_retry_failed_exception extends moodle_exception {
    // Web service response.
    public $response = null;
    /**
     * @param string $response  Web service response
     */
    public function __construct($response) {
        $this->response = $response;
        $a = new stdClass();
        $a->response = $response;
        $a->maxretries = mod_zoom_webservice::MAX_RETRIES;
        parent::__construct('zoomerr_maxretries', 'mod_zoom', '', $a);
    }
}


/**
 * Terminate the current script with a fatal error.
 *
 * Adapted from core_renderer's fatal_error() method. Needed because throwing errors with HTML links in them will convert links
 * to text using htmlentities. See MDL-66161 - Reflected XSS possible from some fatal error messages.
 *
 * So need custom error handler for fatal Zoom errors that have links to help people.
 *
 * @param string $errorcode The name of the string from error.php to print
 * @param string $module name of module
 * @param string $link The url where the user will be prompted to continue. If no url is provided the user will be directed to the
 *                     site index page.
 * @param mixed $a Extra words and phrases that might be required in the error string
 */
function zoom_fatal_error($errorcode, $module='', $continuelink='', $a=null) {
    global $CFG, $COURSE, $OUTPUT, $PAGE;

    $output = '';
    $obbuffer = '';

    // Assumes that function is run before output is generated.
    if ($OUTPUT->has_started()) {
        // If not then have to default to standard error.
        throw new moodle_exception($errorcode, $module, $continuelink, $a);
    }

    $PAGE->set_heading($COURSE->fullname);
    $output .= $OUTPUT->header();

    // Output message without messing with HTML content of error.
    $message = '<p class="errormessage">' . get_string($errorcode, $module, $a) . '</p>';

    $output .= $OUTPUT->box($message, 'errorbox alert alert-danger', null, array('data-rel' => 'fatalerror'));

    if ($CFG->debugdeveloper) {
        if (!empty($debuginfo)) {
            $debuginfo = s($debuginfo); // Removes all nasty JS.
            $debuginfo = str_replace("\n", '<br />', $debuginfo); // Keep newlines.
            $output .= $OUTPUT->notification('<strong>Debug info:</strong> '.$debuginfo, 'notifytiny');
        }
        if (!empty($backtrace)) {
            $output .= $OUTPUT->notification('<strong>Stack trace:</strong> '.format_backtrace($backtrace), 'notifytiny');
        }
        if ($obbuffer !== '' ) {
            $output .= $OUTPUT->notification('<strong>Output buffer:</strong> '.s($obbuffer), 'notifytiny');
        }
    }

    if (!empty($continuelink)) {
        $output .= $OUTPUT->continue_button($continuelink);
    }

    $output .= $OUTPUT->footer();

    // Padding to encourage IE to display our error page, rather than its own.
    $output .= str_repeat(' ', 512);

    echo $output;

    exit(1); // General error code.
}

/**
 * Get course/cm/zoom objects from url parameters, and check for login/permissions.
 *
 * @return array Array of ($course, $cm, $zoom)
 */
function zoom_get_instance_setup() {
    global $DB;

    $id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
    $n  = optional_param('n', 0, PARAM_INT);  // ... zoom instance ID - it should be named as the first character of the module.

    if ($id) {
        $cm         = get_coursemodule_from_id('zoom', $id, 0, false, MUST_EXIST);
        $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
        $zoom  = $DB->get_record('zoom', array('id' => $cm->instance), '*', MUST_EXIST);
    } else if ($n) {
        $zoom  = $DB->get_record('zoom', array('id' => $n), '*', MUST_EXIST);
        $course     = $DB->get_record('course', array('id' => $zoom->course), '*', MUST_EXIST);
        $cm         = get_coursemodule_from_instance('zoom', $zoom->id, $course->id, false, MUST_EXIST);
    } else {
        print_error(get_string('zoomerr_id_missing', 'zoom'));
    }

    require_login($course, true, $cm);

    $context = context_module::instance($cm->id);
    require_capability('mod/zoom:view', $context);

    return array($course, $cm, $zoom);
}

/**
 * Retrieves information for a meeting.
 *
 * @param int $meetingid
 * @param bool $webinar
 * @param string $hostid the host's uuid
 * @return array information about the meeting
 */
function zoom_get_sessions_for_display($meetingid, $webinar, $hostid) {
    require_once(__DIR__.'/../../lib/moodlelib.php');
    global $DB;
    $service = new mod_zoom_webservice();
    $sessions = array();
    $format = get_string('strftimedatetimeshort', 'langconfig');

    $instances = $DB->get_records('zoom_meeting_details', array('meeting_id' => $meetingid));

    foreach ($instances as $instance) {
        // The meeting uuid, not the participant's uuid.
        $uuid = $instance->uuid;
        $participantlist = zoom_get_participants_report($instance->id);
        $sessions[$uuid]['participants'] = $participantlist;
        $sessions[$uuid]['count'] = count($participantlist);
        $sessions[$uuid]['topic'] = $instance->topic;
        $sessions[$uuid]['duration'] = $instance->duration;
        $sessions[$uuid]['starttime'] = userdate($instance->start_time, $format);
        $sessions[$uuid]['endtime'] = userdate($instance->start_time + $instance->duration * 60, $format);
    }
    return $sessions;
}

/**
 * Determine if a zoom meeting is in progress, is available, and/or is finished.
 *
 * @param stdClass $zoom
 * @return array Array of booleans: [in progress, available, finished].
 */
function zoom_get_state($zoom) {
    $config = get_config('mod_zoom');
    $now = time();

    $firstavailable = $zoom->start_time - ($config->firstabletojoin * 60);
    $lastavailable = $zoom->start_time + $zoom->duration;
    $inprogress = ($firstavailable <= $now && $now <= $lastavailable);

    $available = $zoom->recurring || $inprogress;

    $finished = !$zoom->recurring && $now > $lastavailable;

    return array($inprogress, $available, $finished);
}

/**
 * Get the Zoom id of the currently logged-in user.
 *
 * @param boolean $required If true, will error if the user doesn't have a Zoom account.
 * @return string
 */
function zoom_get_user_id($required = true) {
    global $USER;

    $cache = cache::make('mod_zoom', 'zoomid');
    if (!($zoomuserid = $cache->get($USER->id))) {
        $zoomuserid = false;
        $service = new mod_zoom_webservice();
        try {
            $zoomuser = $service->get_user($USER->email);
            if ($zoomuser !== false) {
                $zoomuserid = $zoomuser->id;
            }
        } catch (moodle_exception $error) {
            if ($required) {
                throw $error;
            } else {
                $zoomuserid = $zoomuser->id;
            }
        }
        $cache->set($USER->id, $zoomuserid);
    }

    return $zoomuserid;
}

function zoom_get_user_zoomemail($user,$service) {

    $config = get_config('mod_zoom');
	
	$emailchk = explode('@',$user->email);
	
	if (strpos($emailchk[0],'.')===false) {
		$zoom_email = strtolower($user->firstname.'.'.$user->lastname.'@'.ZOOM_USER_DOMAIN);
	} else {
		$zoom_email = strtolower($user->email);
	}
	
	//try user with first.last@ZOOM_USER_DOMAIN
    $zoomuser = $service->get_user($zoom_email);
	
	if ($zoomuser === false) {
		//try titlcase
		$zoom_email = ucfirst($user->firstname).'.'.ucfirst($user->lastname).'@'.ZOOM_USER_DOMAIN;
		$zoomuser = $service->get_user($zoom_email);
	}
	
    if ($zoomuser === false) {

        //check if zoom account is under user name instead
        $alias = zoom_email_alias($user);

        //check if alias emails are connected to zoom account
        $zoomuser = $service->get_user(strtolower($alias));

        if ($zoomuser === false) {
			
           return false;

        }

    }
	
	return $zoomuser;
}

/**
 * Check if the error indicates that a meeting is gone.
 *
 * @param string $error
 * @return bool
 */
function zoom_is_meeting_gone_error($error) {
    // If the meeting's owner/user cannot be found, we consider the meeting to be gone.
    return strpos($error, 'not found') !== false || zoom_is_user_not_found_error($error);
}

/**
 * Check if the error indicates that a user is not found or does not belong to the current account.
 *
 * @param string $error
 * @return bool
 */
function zoom_is_user_not_found_error($error) {
    return strpos($error, 'not exist') !== false || strpos($error, 'not belong to this account') !== false
        || strpos($error, 'not found on this account') !== false;
}

/**
 * Return the string parameter for zoomerr_meetingnotfound.
 *
 * @param string $cmid
 * @return stdClass
 */
function zoom_meetingnotfound_param($cmid) {
    // Provide links to recreate and delete.
    $recreate = new moodle_url('/mod/zoom/recreate.php', array('id' => $cmid, 'sesskey' => sesskey()));
    $delete = new moodle_url('/course/mod.php', array('delete' => $cmid, 'sesskey' => sesskey()));

    // Convert links to strings and pass as error parameter.
    $param = new stdClass();
    $param->recreate = $recreate->out();
    $param->delete = $delete->out();

    return $param;
}

/**
 * Get the data of each user for the participants report.
 * @param string $detailsid The meeting ID that you want to get the participants report for.
 * @return array The user data as an array of records (array of arrays).
 */
function zoom_get_participants_report($detailsid) {
    global $DB;
    $service = new mod_zoom_webservice();
    $sql = 'SELECT zmp.id,
                   zmp.name,
                   zmp.userid,
                   zmp.user_email,
                   zmp.join_time,
                   zmp.leave_time,
                   zmp.duration,
                   zmp.uuid
              FROM {zoom_meeting_participants} zmp
             WHERE zmp.detailsid = :detailsid
    ';
    $params = [
        'detailsid' => $detailsid
    ];
    $participants = $DB->get_records_sql($sql, $params);
    return $participants;
}

//Added for new co-host feature
/**
 * Get all instructors for a course
 * @param string $detailsid The meeting ID that you want to get the participants report for.
 * @return array The user data as an array of records (array of arrays).
 */
function zoom_get_course_instructors($courseid) {
    global $DB;

    $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    $teachers = get_role_users($role->id, $context);

    $teachersmenu = array();
    if ($teachers) {
        foreach ($teachers as $teacher) {
            $teacherarray=new stdClass;
            $teacherarray->email = $teacher->email;
            $teacherarray->name = fullname($teacher);
            $teachersmenu[] = $teacherarray;
        }
    } 

    return $teachersmenu;
}

//Added for new co-host feature
/**
 * Get all instructors for a course
 * @param string $detailsid The meeting ID that you want to get the participants report for.
 * @return array The user data as an array of records (array of arrays).
 */
function zoom_get_alternative_hosts($zoomid,$service) {
    global $DB;
  
    $zoom  = $DB->get_record('zoom', array('id' => $zoomid), '*', MUST_EXIST);
    $cohosts = explode(",", $zoom->alternative_hosts);
    $users = [];
    foreach($cohosts as $cohost){

        if($cohost != ""){
            $user = zoom_get_user_info(trim($cohost));
            $usertemp=new stdClass;
            
			$usertemp->name = fullname($user);
			
            $usertemp->email = zoom_get_user_zoomemail($user,$service);
            
            $users[] = $usertemp;
        }
    }
    if(empty($users))
        return false;
    else
        return $users;

}

/**
* Get user from db *this forces that all alternative hosts must be in moodle instance
* @param int $email of user
* @param user object
*/
function zoom_get_user_info($email){

    global $DB;

    $user = $DB->get_record('user', array('email' => $email), '*', MUST_EXIST);

	$emailchk = explode('@',$email);
	
	if (!$user&&strpos($emailchk[0],'.')===false) {
		//check by username?
		$user = $DB->get_record('user', array('username' => $emailpcs[0]), '*', MUST_EXIST);
	}
	
    return $user;
}

/**
* Get user from db *this forces that all alternative hosts must be in moodle instance
* @param int $email of user
* @param user object
*/
function zoom_get_user($id){

    global $DB;

    $user = $DB->get_record('user', array('id' => $id), '*', MUST_EXIST);
    return $user;
}


//Added for account creation checks
/**
* Get role of user
* @param int $email of user
* @param user object
*/
function zoom_get_user_role($id){

    global $COURSE;

    $rolestr = array();
    $context = context_course::instance($COURSE->id);
    $roles = get_user_roles($context, $id);
    foreach ($roles as $role) {
        $rolestr[] = role_get_name($role, $context);
    }
    
    return $rolestr;
}


/**
* Check if email is in same domain
* @param int $email of user
* @param user object
*/
function zoom_email_check($email){

    $split = explode('@',$email);
    
    if(ZOOM_USER_DOMAIN == $split[1])
        return true;
    else
        return false;
}
/**
* Check if user has any alias emails connected to account
* @param int $email of user
* @param user object
*/
function zoom_email_alias($user){

    //pulll username from db
    //$user = zoom_get_user_info($email);
    //append email to it
    $alias = $user->username.'@'.ZOOM_USER_DOMAIN;
    //test if it is their zoom account
    return $alias;
}
