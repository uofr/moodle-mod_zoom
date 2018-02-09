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
 * English strings for zoom.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Actions';
$string['allmeetings'] = 'View all meetings';
$string['apikey'] = 'Zoom API key';
$string['apikey_desc'] = '';
$string['apisecret'] = 'Zoom API secret';
$string['apisecret_desc'] = '';
$string['apiurl'] = 'Zoom API url';
$string['apiurl_desc'] = '';
$string['audio_both'] = 'Both';
$string['audio_telephony'] = 'Telephony only';
$string['audio_voip'] = 'Voip only';
$string['cachedef_zoomid'] = 'The zoom user id of the user';
$string['cachedef_sessions'] = 'Information from the zoom get user report request';
$string['connectionok'] = 'Connection working.';
$string['connectionstatus'] = 'Connection status';
$string['duration'] = 'Duration';
$string['endtime'] = 'End Time';
$string['err_duration_nonpositive'] = 'The duration must be positive.';
$string['err_duration_too_long'] = 'The duration cannot exceed 150 hours.';
$string['err_long_timeframe'] = 'Requested time frame too long, showing results of latest month in range.';
$string['err_password'] = 'Password may only contain the following characters: [a-z A-Z 0-9 @ - _ *]. Max of 10 characters.';
$string['err_start_time_past'] = 'The start date cannot be in the past.';
$string['errorwebservice'] = 'Zoom webservice error: {$a}.';
$string['export'] = 'Export';
$string['firstjoin'] = 'First able to join';
$string['firstjoin_desc'] = 'The earliest a user can join a scheduled meeting (minutes before start).';
$string['invalid_status'] = 'Status invalid, check the database.';
$string['join'] = 'Join';
$string['joinbeforehost'] = 'Join meeting before host';
$string['join_link'] = 'Join link';
$string['join_meeting'] = 'Join Meeting';
$string['jointime'] = 'Join Time';
$string['leavetime'] = 'Leave Time';
$string['list_sessions'] = 'List sessions';
$string['login_api'] = 'API';
$string['login_facebook'] = 'Facebook';
$string['login_google'] = 'Google';
$string['login_sso'] = 'Single sign-on';
$string['login_zoom'] = 'Zoom';
$string['logintypes'] = 'Login types';
$string['logintypesexplain'] = 'Select all login types for user email lookup. Hold CTRL key to select multiple fields. (Options are listed in the order that lookup will be attempted)';
$string['meeting_expired'] = 'Expired / Deleted';
$string['meeting_finished'] = 'Finished';
$string['meeting_not_started'] = 'Not started';
$string['meeting_expired_lng'] = 'This meeting has expired or been deleted';
$string['meeting_finished_lng'] = 'This meeting has finished';
$string['meeting_not_started_lng'] = 'This meeting has not started';
$string['meetingoptions'] = 'Meeting option';
$string['meetingoptions_help'] = '*Join before host* allows attendees to join the meeting before the host joins or when the host cannot attend the meeting.';
$string['meeting_started'] = 'In progress';
$string['meeting_started_lng'] = 'This meeting is in progress';
$string['meeting_time'] = 'Start Time';
$string['modulename'] = 'Zoom meeting';
$string['modulenameplural'] = 'Zoom Meetings';
$string['modulename_help'] = 'Zoom is a video and web conferencing platform that gives authorized users the ability to host online meetings.';
$string['newmeetings'] = 'New Meetings';
$string['noparticipants'] = 'No participants found for this session at this time.';
$string['nosessions'] = 'No sessions found for specified range.';
$string['nozooms'] = 'No meetings';
$string['off'] = 'Off';
$string['oldmeetings'] = 'Concluded Meetings';
$string['on'] = 'On';
$string['option_audio'] = 'Audio options';
$string['option_host_video'] = 'Host video';
$string['option_jbh'] = 'Enable join before host';
$string['option_participants_video'] = 'Participants video';
$string['participants'] = 'Participants';
$string['password'] = 'Password';
$string['passwordprotected'] = 'Password Protected';
$string['pluginadministration'] = 'Manage Zoom meeting';
$string['pluginname'] = 'Zoom meeting';
$string['recurringmeeting'] = 'Recurring';
$string['recurringmeeting_help'] = 'Has no end date';
$string['recurringmeetinglong'] = 'Recurring meeting (meeting with no end date or time)';
$string['report'] = 'Reports';
$string['requirepassword'] = 'Require meeting password';
$string['sessions'] = 'Sessions';
$string['start'] = 'Start';
$string['starthostjoins'] = 'Start video when host joins';
$string['start_meeting'] = 'Start Meeting';
$string['startpartjoins'] = 'Start video when participant joins';
$string['start_time'] = 'When';
$string['starttime'] = 'Start Time';
$string['status'] = 'Status';
$string['title'] = 'Title';
$string['topic'] = 'Topic';
$string['unavailable'] = 'Unable to join at this time';
$string['updatemeetings'] = 'Update meeting settings from Zoom';
$string['usepersonalmeeting'] = 'Use personal meeting ID {$a}';
$string['webinar'] = 'Webinar';
$string['webinar_help'] = 'This option is only available to pre-authorized Zoom accounts.';
$string['zoom:addinstance'] = 'Add a new Zoom meeting';
$string['zoomerr'] = 'An error occured with Zoom.'; // Generic error.
$string['zoomerr_apisettings_invalid'] = 'The Zoom API key and/or secret are invalid. Please ensure they are correct.';
$string['zoomerr_apisettings_missing'] = 'Please configure the Zoom API key and secret.';
$string['zoomerr_apiurl_404'] = 'The Zoom API url could not be found; please check the setting.';
$string['zoomerr_apiurl_error'] = 'The Zoom API could not be contacted; please check your server error log.';
$string['zoomerr_apiurl_unresolved'] = 'The Zoom API url could not be resolved; please check the setting.';
$string['zoomerr_meetingnotfound'] = 'This meeting has expired. You can <a href="{$a->recreate}">recreate it here</a> or <a href="{$a->delete}">delete it completely</a>.';
$string['zoomerr_meetingnotfound_info'] = 'This meeting has expired. Please contact the meeting host if you have questions.';
$string['zoomerr_usernotfound'] = '<h3 style="text-align: left">Zoom Account Configuration Required</h3>

<p style="text-align: left"><b>If you already have a Zoom account</b>, but it does not match your current UR Courses email address (<b>{$a->email}</b>), please contact <a href="mailto:IT.Support@uregina.ca">IT.Support@uregina.ca</a> and request that your UR Courses profile be updated to use the <b><i>uregina.ca</i></b> email address associated with your Zoom account.</p>
	
<p style="text-align: left; margin-top: 1em"><b>If you are using Zoom for the first time</b>, you must first create a Zoom account by visiting <a href="{$a->url}" target="_blank">{$a->url}</a> and sign up with your <b><i>uregina.ca</i></b> email address. Once you have a Zoom account that matches the email address used in your UR Courses profile (<b>{$a->email}</b>), you will be able to continue setting up your meeting.</p>

<p style="text-align: left; margin-top: 1em">If you intend to host Zoom meetings that will be longer than 40 minutes, contact <a href="mailto:IT.Support@uregina.ca">IT.Support@uregina.ca</a> and request that your Zoom account be upgraded to pro.</p>

<p style="text-align: left; margin-top: 1em">Additional information on using Zoom is available at <a href="https://urcourses.uregina.ca/guides/instructor/zoom" target="_blank">https://urcourses.uregina.ca/guides/instructor/zoom</a>.</p>';
$string['zoomurl'] = 'Zoom home page URL';
$string['zoomurl_desc'] = '';
$string['zoom:view'] = 'View Zoom meetings';
