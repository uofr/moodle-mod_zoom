YUI.add("moodle-mod_zoom-cohost",function(e,t){var n={},r;M.mod_zoom=M.mod_zoom||{},r=M.mod_zoom.cohost={},r.createTag=function(t){var n=e.Node.create('<div class="tag" >'+t+"</div>");const r=e.Node.create('<i class="material-icons" data-item = "'+t+'"> close </i>');return n.append(r),n},r.clearTags=function(e){e.all(".tag").each(function(e){e.remove()})},r.addTags=function(t,n){M.mod_zoom.cohost.clearTags(t);var r=e.one("#id_ac-input");e.Array.each(n,function(e){t.insert(M.mod_zoom.cohost.createTag(e.name),r)})},r.addEmails=function(t){var n=e.one("#id_cohostid");n.set("value","");var r="";e.Array.each(t,function(e){e!="0"&&e!=0&&(r+=e.email+",")}),r!=""&&n.set("value",r)},r.addNewEmail=function(t){var n=e.one("#id_newcohost");inputvalue=n.get("value"),t=t.trim(),inputvalue==""?n.set("value",t):n.set("value",inputvalue+","+t)},r.deleteEmail=function(t){var n=e.one("#id_newcohost");selected=n.get("value").split(/\s*,\s*/),t=t.trim(),n.set("value",""),e.Array.each(selected,function(e){e!==""&&e!==t&&n.set("value",t+",")})},r.init=function(t,n){if(!n)var r=t;else var r=n;var i=[],s=[];const o=e.one(".tag-container");var u=o.one(".col-md-3"),a=[];u.removeClass("col-md-3"),e.one("#id_newcohost").set("value",""),M.mod_zoom.cohost.addTags(o,r),M.mod_zoom.cohost.addEmails(r),e.Array.each(t,function(e){i.push(e.name)}),e.one("body").on("click",function(t){if(t.target.get("tagName")==="I"){var n=t.target.getAttribute("data-item"),i=!0;e.Array.each(a,function(e){e===n&&(i=!1)}),i&&a.push(n),temp=[],e.Array.each(r,function(e){e.name!==n?temp.push(e):e.email==0&&M.mod_zoom.cohost.deleteEmail(e.name)}),r=temp,M.mod_zoom.cohost.addTags(o,r),M.mod_zoom.cohost.addEmails(r)}}),YUI().use("autocomplete","autocomplete-filters","autocomplete-highlighters",function(e){var n=e.one("#id_ac-input");n.set("value",""),n.plug(e.Plugin.AutoComplete,{allowTrailingDelimiter:!0,minQueryLength:0,queryDelay:0,queryDelimiter:",",source:i,resultHighlighter:"startsWith",resultFilters:["startsWith",function(t,r){var i=n.get("value").split(/\s*,\s*/);return i=e.Array.hash(i),e.Array.filter(r,function(e){return!i.hasOwnProperty(e.text)})}]}),n.on("focus",function(){n.ac.sendRequest(""),n.set("value","")}),o.on("click",function(){n.ac.sendRequest(""),n.set("value",""),n.focus()}),n.on("keyup",function(e){e.keyCode==32&&(newtag=e.target.get("value"),r.push({email:0,name:newtag}),M.mod_zoom.cohost.addTags(o,r),M.mod_zoom.cohost.addEmails(r),M.mod_zoom.cohost.addNewEmail(newtag),n.set("value",""))}),n.ac.after("select",function(){s=n.get("value").split(/\s*,\s*/);var i=s.length-2;value=s[i];if(value!=""){var u=!0;e.Array.each(r,function(e){e.name===value&&(u=!1)}),u&&(teacheremail="0",e.Array.each(t,function(e){e.name===value&&(teacheremail=e.email)}),temp=[],e.Array.each(a,function(e){e.name!==value&&temp.push(value)}),a=temp,r.push({email:teacheremail,name:value}),M.mod_zoom.cohost.addTags(o,r),M.mod_zoom.cohost.addEmails(r),n.set("value",""))}n.set("value","")})})}},"@VERSION@",{requires:["base","node","event"]});
