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
    var mm_new_message = $('#dlg_new_message');
    var toNameField = $('span#mm_recipient');
    var mmUsers = $('#mm_users');
    var mt = myTextarea;
    var ul = myUserList;
    var ddl = $('#mm_dropdown_list');

    /* Display "No messages" if table is empty (except header row) */
    checkEmpty();

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
                    if (id === null) {
                        break;
                    }
                    mmAjax(id, 'security/message/remove', {}, true);
                    $('tr#' + id).remove();
                    $('tr#mm_message' + id).remove();
                    $('tr#mm_sender_id' + id).remove();
                    checkEmpty();
                    break;
                case 'reply':
                    mmReply(id);
                    break;
                case 'newmessage':
                    mmReply(null, true);
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

    function clearDialog() {
        mt.val('');
        ul.val('');
        ul.hide();
        mt.hide();
        ddl.hide();
        mm_new_message.hide();
        toNameField.html('');
        toNameField.hide();
        mmUsers.val('');
        mmUsers.hide();

    }

    function checkEmpty() {
        if (myTable.find("tr").length == 1) {
            $(myTable.append('<tr><td colspan="5">No messages</td></tr>'));
        }
    }

    function mmReply(id, newMessage) {
        newMessage = newMessage || null;
        if (id === null) {
            if (newMessage == null) {
                return false;
            }
        }
        var recipientId = null;
        var recipientType = null;
        /*var toNameField = $('span#mm_recipient');
        var mmUsers = $('#mm_users');
        var mt = myTextarea;
        var ul = myUserList;
        var ddl = $('#mm_dropdown_list');*/
        ul.hide();
        toNameField.hide();
        var action = 'security/message/create';
        var dlgSubjectObj = $("#dlg_subject");
        var subject = '';
        if (id !== null) {
            subject = $.trim($('#mm_subject' + id).html().replace(/<span[^>]*>.*<\/span>/, ""));
            var replyPrefix = "[re:] ";
            if (subject.indexOf(replyPrefix) == -1) {
                subject = replyPrefix + subject;
            }
        }
        dlgSubjectObj.val(subject);
        console.log('ID: ' + id);
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
                    clearDialog();
                    $(this).dialog("close");
                },
                "Send": {
                    id: 'mm_button_send',
                    text: "Send"
                }
            }
         });

        if (newMessage !== null) { /* New Message */
            document.forms['dlg_form'].reset();


            var selectTypeOptions = $("#dlg_select_type");

            var groupId = null;
            myDialog.dialog('option', 'title', 'New Message');
            mm_new_message.show();
            mt.hide();
            ddl.hide();
            // selectTypeOptions.find('option:first').attr('selected', 'selected');
            // selectTypeOptions[0].selectedIndex = 0;
                       // selectTypeOptions.prepend('<option selected="selected" value="0"> Select Type </option>');
            selectTypeOptions.change(function () {
                var selection = this.value;
                console.log("Selection: " + selection);
                console.debug(this);
                if (selection == 0) {
                    return false;
                }
                $("#dlg_recipient_type").html(selection);
                recipientType = selection;

                switch(selection) {
                    case 'user':
                        ddl.hide();

                        mm_body.addClass("loading");
                        var promise1 = mmAjax(null, 'security/user/getlist', {limit:0});
                        promise1.done(function (data) {
                            // mm_body.removeClass("loading");
                            var results = data.data.results;
                            var count = results.length;
                            var r = [], j = 0;
                            r[++j] = '<div id="mm_users"><h3>Select Recipient</h3>';
                            for (var key = 0, size = results.length; key < size; key++) {
                                r[++j] = '<span id="' + results[key].id +
                                         '" class="mm_user">';

                                r[++j] = empty(results[key].fullname)
                                    ? results[key].username
                                    : results[key].fullname;
                                r[++j] = '</span>';
                            }
                            r[++j] = '</div>';

                            mmUsers.html(r.join(' '));
                            mmUsers.show();

                            $('span.mm_user').on("click", function (e) {
                                //ul.hide();
                                // var userId = e.target.id;
                                // alert(userId);
                                recipientId = e.target.id;
                                var recipientName = $(this).html();
                                console.log('Recipient: ' + recipientName);
                                if (recipientId == undefined) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    return;
                                }
                                toNameField.html(recipientName);
                                toNameField.show();
                                console.log('Recipient ID: ' + recipientId);
                                mmUsers.hide();
                                mt.show();


                            });
                            // console.debug(data.data.results);

                        });


                        break;
                    case 'usergroup':
                        mmUsers.hide();
                        toNameField.hide();
                        mm_body.addClass("loading");
                        var promise = mmAjax(null, 'security/group/getlist');
                        promise.done(function(data) {
                            // var data = jQuery.parseJSON(json_data.data);

                            var results = data.data.results;
                            // console.debug(results);
                            var count = results.length;
                            var r = [], j = 0;
                            // r[++j] = '<div id="mm_users"><h3>Select Recipient</h3>';
                            for (var key = 0, size = results.length; key < size; key++) {
                                r[++j] = '<option value="' + results[key].id + '" class="mm_user">';
                                r[++j] = results[key].name;
                                r[++j] = '</option>';
                            }
                            // r[++j] = '</div>';
                            // $('#myDialog').dialog.append(r.join(' '));
                            // ul.html(r.join(' '));
                            $('#mm_dropdown_select').append(r.join(' '));


                            ddl.on("click", function (e) {
                                //ul.hide();
                                // var userId = e.target.id;
                                // alert(userId);
                                groupId = e.target.value;

                                if (groupId == 0) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    return;
                                }

                                console.log('Group ID: ' + groupId);
                                mt.show();


                            });
                            // console.debug(data.data.results);
                            ddl.show();
                        });

                        break;
                    case 'all':
                        mt.show();
                        break;
                    default:
                        break;
                }
                    // alert( "Selected: " + selection);                                                                    // $("div").text(str);
            });
            // ddl[0].selectedIndex = 0;
            // $('select option:first-child').attr("selected", "selected");
           //  $('#mm_dropdown_list option:first-child').attr("selected", "selected");
           /* $('#periodSelect option').each(function () {
                this.removeAttribute('selected');
            });
*/


            $('#mm_left_button').hide();


            $("#mm_button_send").unbind("click").click(function () {
                message = $.trim(mt.val());
                if (message.length == 0) {
                    alert("Can't send an empty message");
                } else {
                    subject = $('input#dlg_subject').val();
                    // console.log('SendSubject: ' + subject);
                    if (subject.length === 0) {
                        alert('Please enter a subject');
                        return false;
                    }

                    console.log('Type: ' + recipientType);
                    mm_body.addClass('loading');
                    switch(recipientType) {
                        case 'all':
                           promise4 = mmAjax(null, 'security/message/create', {'type':'all','subject': subject,'message': message});
                            // alert('All');
                            break;
                        case 'user':
                            promise4 = mmAjax(null, 'security/message/create', {'type':'user','user':recipientId,'subject':subject,'message':message});

                            break;
                        case 'usergroup':
                            promise4 = mmAjax(null, 'security/message/create', {'type':'usergroup','group':groupId,'subject': subject,'message': message});
                            // alert('User Group');
                            break;
                    }
                    promise4.done(function (data) {
                        clearDialog();
                        mm_body.removeClass('loading');
                        // $.alert('Message Sent', 'MessageManager');
                        myDialog.dialog("close");
                    });
                }
            });
        } else { /* Reply */
            ul.hide();
            mt.show();
            recipientId = $('#mm_sender' + id).html();
            $("#mm_button_send").unbind("click").click(function () {
                message = $.trim(mt.val());
                if (message.length == 0) {
                    alert("Can't send an empty message");
                } else {
                    // alert('Clicked reply');
                    subject = $('input#dlg_subject').val();
                    if (subject.length === 0) {
                        alert('Please enter a subject');
                        return false;
                    }
                    // console.log('SendSubject: ' + subject);
                    mm_body.addClass("loading");
                    promise5 = mmAjax(id, action, {'subject': subject, 'message': message, 'user': recipientId});
                    promise5.done(function (data) {

                        clearDialog();
                        myDialog.dialog("close");
                        mm_body.removeClass("loading");
                        // $.alert('Message Sent', 'MessageManager');
                    });
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
        mm_body.addClass("loading");
        $('input:checked').each(function () {
            id = $(this).val();
            promise6 = mmAjax(id, 'security/message/remove', {}, true);
            $('tr#' + id).remove();
            $('tr#mm_message' + id).remove();
            $('tr#mm_sender_id' + id).remove();

        });
        /* Uncheck checkbox in header */
        $("#mm_check_all").prop("checked", false);

        promise6.done(function (data) {
            mm_body.removeClass("loading");
            /* Display "No messages" if table is empty (except header row) */
            checkEmpty();
        });
    });


    function mmAjax(id, action, dataIn, hideLoader) {
        var retVal = false;
        dataIn = dataIn || {};
        dataIn['id'] = id;
        dataIn['action'] = action;
        hideLoader = hideLoader || null;

        if (hideLoader === null) {
            // mm_body.addClass("loading");
        }

        /* Ajax call to action; calls MODX resource pseudo-connector */
        return $.ajax({
            type: "POST",
            url: "mm-ajax.html",
            data: dataIn,
            dataType: "json"

        }).done(function () {
            mm_body.removeClass("loading");
        }).fail(function (jqXHR, textStatus) {
            mm_body.removeClass("loading");
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
            mmAjax(id, 'security/message/read', {}, true)
        }
    }

    /* Mark message unread in DB and on screen;
     hide message if visible; change cursor to +
     */
    function mmMarkUnread(id, message) {
        var read = $('#mm_read' + id);
        if (read.html() === undefined) {
            return false;
        }
        console.log("READ.html: " + read.html());

        if (read.html() == 'Yes') {
            read.toggleClass("Yes No");
            read.html('No');
            mmAjax(id, 'security/message/unread', {}, true);
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

    $.extend({ alert: function (message, title) {
        $("<div></div>").dialog( {
            buttons: { "Ok": function () { $(this).dialog("close"); } },
            close: function (event, ui) { $(this).remove(); },
            resizable: false,
            title: title,
            modal: true
        }).text(message);
    }});

});



