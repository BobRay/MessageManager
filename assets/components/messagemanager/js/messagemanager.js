/** 
 * JS file for MessageManager extra
 * 
 * Copyright 2015 by Bob Ray <http://bobsguides.com>
 * Created on 01-27-2015
 *
 * MessageManager is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * MessageManager is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * MessageManager; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 * @package messagemanager
 */




/* *** Context Menu *** */
$(function () {
    var myTable = $('table#the-node');
    var myTextarea = $('#myTextarea');
    var myUserList = $('#mm_userlist');
    var ajaxLoader = $('#ajax_loader');
    var mm_body = $("body");

    /* Display "No messages" if table is empty (except header row) */
    if (myTable.find("tr").length == 1) {
         $(myTable.append('<tr><td colspan="5">No messages</td></tr>'));
    }

    var messageText = '';

    $('#the-node').contextMenu({
        /* selector: 'li', */
        selector: 'tr',
        callback: function (key, options) {
            var id = getId($(this).attr('id')) || null;
            // var m = "clicked: " + key + " on " + getId($(this).attr('id'));
            // window.console && console.log(m) || alert(m);
            switch (key) {
                case 'markunread':
                    mmMarkUnread(id, 'No');
                    break;
                case 'delete':
                    mmAjax(id, 'security/message/remove', null, null, null, true);
                    $('tr#' + id).remove();
                    $('tr#mm_message' + id).remove();
                    $('tr#mm_sender_id' + id).remove();
                    break;
                case 'reply':
                    mmReply(id);
                    break;
                case 'newmessage':
                    mmReply(id, true);
                    break;
            }
        },
       items: {

           "markunread": {name: "Mark Unread", icon: "markunread"},
           "delete": {name: "Delete", icon: "delete"},
           "reply": {name: "Reply", icon: "reply"},
           "newmessage": {name: "New Message", icon: "newmessage"},
           "sep1": "---------",
           "quit": {name: "Cancel", icon: "cancel"}
       }
   });

    /*function mmNewMessage(id) {
        mmReply(id, true)
    }*/

    function mmReply(id, newMessage) {
        newMessage = newMessage || null;
        if (id === null) {
            if (newMessage == null) {
                return;
            }
        }
        // var senderId = $('#mm_sender' + id).html();
        // var mt = $('#myTextarea');
        var mt = myTextarea;
        var ul = myUserList;
        ul.hide();
        var action = 'security/message/create';
        var recipient = $('#mm_sender' + id).html();
        /*if (id !== null) {
            // var originalMessageText = $("#mm_message" + id).find('td:first').html();
        }*/
        var subject = '';
        if (id !== null) {

            subject = $.trim($('#mm_subject' + id).html().replace(/<span[^>]*>.*<\/span>/, ""));
            var replyPrefix = "[re:] ";
            if (subject.indexOf(replyPrefix) == -1) {
                subject = replyPrefix + subject;
            }
        }
        if (newMessage == null) {
            $("#dlg_subject").val(subject);
        }
        console.log('Subject: X' + subject + 'X');
        var message = '';
        var myDialog = $("#myDialog").dialog({
            autoOpen: false,
            maxWidth: 500,
            maxHeight: 400,
            width: 500,
            height: 400,
            modal: true,
            draggable: false,
            buttons: {
                "Quote": {
                    text: "Quote",
                    id: "mm_left_button",
                    class: 'mm_left_button',
                    click: function() {
                        var originalMessageText = $("#mm_message" + id).find('td:first').html();
                        console.log('Original: ' + originalMessageText);
                        originalMessageText = originalMessageText.replace(/&lt;/g, '<');
                        originalMessageText = originalMessageText.replace(/&gt;/g, '>');
                        // console.log('Original after replace: ' + originalMessageText);
                        mt.val("<< " + originalMessageText + " >>");
                    }
                },

                "Cancel": function () {
                    mt.val('');
                    ul.val('');
                    $(this).dialog("close");
                },
                "Send": {
                    id: 'mm_button_send',
                    text: "Send"
                }
            }
         });

        if (newMessage !== null) { /* New Message */
            myDialog.dialog('option', 'title', 'New Message');
            mt.hide();

            // ajaxLoader.show();
            mm_body.addClass("loading");
            // ajaxLoader.position("option", "position", {my: "center", at: "center", of: window});

            $.getJSON("http://localhost/addons/mm-ajax.html", {
                // , 'usergroup': 'group1'
                'id': id, 'action': 'security/user/getlist'}, function(json_data) {


                if (json_data.success) {
                    // var data = jQuery.parseJSON(json_data.data);

                    var results = json_data.data.results;
                    var count = results.length;
                    // alert (count);
                    var r = [], j = -1;
                    r[++j] = '<div id="mm_users"><h3>Select Recipient</h3>';
                    for (var key = 0, size = results.length; key < size; key++) {
                        r[++j] = '<span id="' + results[key].id + '" class="mm_user">';
                        r[++j] = empty(results[key].fullname)
                            ? results[key].username
                            : results[key].fullname  ;
                        r[++j] = '</span>';
                    }
                    r[++j] = '</div>';
                    // $('#myDialog').dialog.append(r.join(' '));
                    ul.html(r.join(' '));


                    $('span.mm_user').on("click", function(e) {
                        ul.hide();
                        /*var userId = e.target.id;*/
                        // alert(userId);
                        recipient = e.target.id;
                        mt.show();


                    });
                    mm_body.removeClass("loading");
                    // ajaxLoader.hide();
                    ul.show();


                } else {
                    mm_body.removeClass("loading");
                    // ajaxLoader.hide();
                    alert('getList Failed');
                }

            });



            $('#mm_left_button').hide();
            /*var ajaxRequest = $.ajax({
            type: "POST",
            url: "http://localhost/addons/mm-ajax.html",
            data: {
                'id': id, 'action': 'security/user/getlist'}
        });

        ajaxRequest.done(function (msg) {
            // alert(action + ' succeeded on message ' + id);
        });

        ajaxRequest.fail(function (jqXHR, textStatus) {
            alert(action + ' failed on message ' + id + ' ' + textStatus);
        });
*/



            $("#mm_button_send").unbind("click").click(function () {
                message = $.trim(mt.val());
                if (message.length == 0) {
                    alert("Can't send an empty message");
                } else {
                    // alert('Clicked new message');
                    /* Ajax here */
                    mmAjax(id, action, subject, message, recipient);
                    mt.val('');
                    myDialog.dialog("close");
                }
            });
        } else { /* Reply */
            ul.hide();
            mt.show();
            $("#mm_button_send").unbind("click").click(function () {
                message = $.trim(mt.val());
                if (message.length == 0) {
                    alert("Can't send an empty message");
                } else {
                    // alert('Clicked reply');
                    mmAjax(id, action, subject, message);
                    myTextarea.val('');
                    myDialog.dialog("close");

                }

            });
        }

        myDialog.dialog("open");

    }


    /* onClick function for subject field */
    $(".mm_subject").on("click", function() {
        var id = getId(this.id);
        var subject = $('#mm_message' + id);
        if (subject.is(":visible")) {
            mmCloseSubject(subject, id);
        } else {
            mmOpenSubject(subject, id);
        }
    });

    $("#mm_submit_delete").on("click", function (e) {

        e.preventDefault();
        e.stopPropagation();
        $('input:checked').each(function () {
            id = $(this).val();
            mmAjax(id, 'security/message/remove', null, null, null, true);
            $('tr#' + id).remove();
            $('tr#mm_message' + id).remove();
            $('tr#mm_sender_id' + id).remove();

        });
        /* Uncheck checkbox in header */
        $("#mm_check_all").prop("checked", false);

        /* Display "No messages" if table is empty (except header row) */
        if (myTable.find("tr").length == 1) {
            $(myTable.append('<tr><td colspan="5">No messages</td></tr>'));
        }

    });


    function mmAjax(id, action, subject, message, recipient, hideLoader) {
        hideLoader = hideLoader || null;
        message = message || null;
        subject = subject || null;
        recipient = recipient || null;
        /* Ajax call to action; calls MODX resource pseudo-connector */
        /*ajaxLoader.show();*/
        if (hideLoader === null) {
            mm_body.addClass("loading");
        }
        var ajaxRequest = $.ajax({
            type: "POST",
            url: "http://localhost/addons/mm-ajax.html",
            data: {
                'id': id, 'action': action, 'subject': subject, 'message': message,
                'recipient' : recipient},
            dataType: "json" //to parse string into JSON object
        });

        ajaxRequest.done(function (data) {
            mm_body.removeClass("loading");
            if (! data.success) {
                alert(data.error_message);
            }

            // alert(action + ' succeeded on message ' + id);
        });

        ajaxRequest.fail(function (jqXHR, textStatus) {
            mm_body.removeClass("loading");
            ajaxLoader.hide();
            alert(action + ' failed on message ' + id + ' ' + textStatus);
        });
    }


    /* Pulls ID out of selector ID like 'mm_message12' */
    function getId(s) {
        if (s === undefined) {
            return null;
        }
        var number = s.match(/\d+$/);
        number = parseInt(number, 10);
        return number
    }

    /* Mark message read in DB and on screen */

    function mmMarkRead(id, message) {
        var e = $('#mm_read' + id);

        if (e.html() == 'No') {
            e.html('Yes');
            e.toggleClass("Yes No");
            mmAjax(id, 'security/message/read', null, null, null, true)
        }
    }

    /* Mark message unread in DB and on screen;
     hide message if visible; change cursor to +
     */
    function mmMarkUnread(id, message) {
        var e = $('#mm_read' + id);
        if (e.html() == 'Yes') {
            e.toggleClass("Yes No");
            e.html('No');
            mmAjax(id, 'security/message/unread', null, null, null, true);
        }
        messageId = $('#mm_message' + id);
        if (! messageId.is(':hidden')) {
            mmCloseSubject(messageId, id);
        }

    }

    /* Show message if hidden; mark read in DB and on screen;
     change down arrow to up arrow; change cursor to -
     */

    function mmOpenSubject(msg, id) {
        msg.show();
        msg.attr("style", "display:table-row");
        var td = $('.mm_message');
        td.attr('style', 'display:table-cell');
        td.attr('colspan', "5");
        mmMarkRead(id, 'Yes');
        $('#mm_expand' + id).html('\u25B4');
        $('#mm_subject' + id).toggleClass("zoomin zoomout");
    }

    /* Hide message if visible; change up arrow to down arrow */
    function mmCloseSubject(msg, id) {
        msg.attr('colspan', 5);
        msg.hide();
        $('#mm_expand' + id).html('\u25BE');
        $('#mm_subject' + id).toggleClass("zoomin zoomout");
    }

    $(function () {
        $("#mm_check_all").change(function () {
            if (this.checked) {
                $('.mm_box').prop("checked", true);
            } else {
                $('.mm_box').prop("checked", false);
            }
        });
    });

    function empty(data) {
        if (typeof(data) == 'number' || typeof(data) == 'boolean') {
            return false;
        }
        if (typeof(data) == 'undefined' || data === null) {
            return true;
        }
        if (typeof(data.length) != 'undefined') {
            return data.length == 0;
        }
        var count = 0;
        for (var i in data) {
            if (data.hasOwnProperty(i)) {
                count++;
            }
        }
        return count == 0;
    }


});



