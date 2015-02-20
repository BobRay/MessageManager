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
    var mm_body = $("body");
    var mm_new_message = $('#dlg_new_message');
    var toNameField = $('span#mm_recipient');
    var mmUsers = $('#mm_users');
    var mt = myTextarea;
    var ul = myUserList;
    var ddl = $('#mm_dropdown_list');
    var spinnerTarget = document.getElementsByTagName("body")[0];
    var mmSpinner = createSpinner();

    var pop = new Popup($("#popup_box"), mm_body);

    /* Display "No messages" if table is empty (except header row) */
    checkEmpty();

    $('#the-node').contextMenu({
        /* selector: 'li', */
        selector: 'tr',
        callback: function (key, options) {
            var id = getId($(this).attr('id')) || null;
            switch (key) {
                case 'markunread':
                    mmMarkUnread(id, mmLex('mm_no'));
                    break;
                case 'delete':
                    if (id === null) {
                        break;
                    }
                    promise7 = mmAjax(id, 'security/message/remove', {});
                    mmSpinner.spin(spinnerTarget);
                    promise7.done(function (data) {
                        mmSpinner.stop();
                        $('tr#' + id).remove();
                        $('tr#mm_message' + id).remove();
                        $('tr#mm_sender_id' + id).remove();
                        checkEmpty();
                        pop.setText(mmLex('mm_message_deleted'));
                        pop.load(1);
                    });
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
            $(myTable.append('<tr><td colspan="5">' + mmLex('mm_no_messages') + '</td></tr>'));
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
                        mmSpinner.spin(spinnerTarget);
                        var promise1 = mmAjax(null, 'security/user/getlist', {limit:0});
                        promise1.done(function (data) {
                            var results = data.data.results;
                            // var count = results.length;
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
                            mmSpinner.stop();
                            $('span.mm_user').on("click", function (e) {
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
                        mmSpinner.spin(spinnerTarget);
                        var promise = mmAjax(null, 'security/group/getlist');
                        promise.done(function(data) {
                            var results = data.data.results;
                            var r = [], j = 0;

                            for (var key = 0, size = results.length; key < size; key++) {
                                r[++j] = '<option value="' + results[key].id + '" class="mm_user">';
                                r[++j] = results[key].name;
                                r[++j] = '</option>';
                            }
                            $('#mm_dropdown_select').append(r.join(' '));


                            ddl.on("click", function (e) {
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

            });

            $('#mm_left_button').hide();
            $("#mm_button_send").unbind("click").click(function () {
                message = $.trim(mt.val());
                if (message.length == 0) {
                    pop.setText(mmLex("mm_empty_message"));
                    pop.load(20);
                } else {
                    subject = $('input#dlg_subject').val();
                    // console.log('SendSubject: ' + subject);
                    if (subject.length === 0) {
                        pop.setText(mmLex("mm_empty_subject"));
                        pop.load(20);
                        return false;
                    }

                    console.log('Type: ' + recipientType);
                    mmSpinner.spin(spinnerTarget);
                    switch(recipientType) {
                        case 'all':
                           promise4 = mmAjax(null, 'security/message/create', {'type':'all','subject': subject,'message': message});
                            break;

                        case 'user':
                            promise4 = mmAjax(null, 'security/message/create', {'type':'user','user':recipientId,'subject':subject,'message':message});
                            break;

                        case 'usergroup':
                            promise4 = mmAjax(null, 'security/message/create', {'type':'usergroup','group':groupId,'subject': subject,'message': message});
                            break;
                    }
                    promise4.done(function (data) {
                        clearDialog();
                        mmSpinner.stop();
                        pop.setText(mmLex('mm_message_sent'));
                        myDialog.dialog("close");
                        pop.load();
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
                    pop.setText(mmLex("mm_empty_message"));
                    pop.load(20);
                } else {
                    // alert('Clicked reply');
                    subject = $('input#dlg_subject').val();
                    if (subject.length === 0) {
                        pop.setText(mmLex('mm_empty_subject'));
                        pop.load(20);
                        return false;
                    }

                    mmSpinner.spin(spinnerTarget);
                    promise5 = mmAjax(id, action, {'subject': subject, 'message': message, 'user': recipientId});
                    promise5.done(function (data) {

                        clearDialog();
                        mmSpinner.stop();
                        pop.setText(mmLex('mm_message_sent'));
                        myDialog.dialog("close");
                        pop.load();
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
        mmSpinner.spin(spinnerTarget);
        $('input:checked').each(function () {
            id = $(this).val();
            promise6 = mmAjax(id, 'security/message/remove', {});
            $('tr#' + id).remove();
            $('tr#mm_message' + id).remove();
            $('tr#mm_sender_id' + id).remove();

        });
        /* Uncheck checkbox in header */
        $("#mm_check_all").prop("checked", false);

        promise6.done(function (data) {
            mmSpinner.stop();
            pop.setText(mmLex('mm_messages_deleted'));
            pop.load();

            /* Display "No messages" if table is empty (except header row) */
            checkEmpty();
        });
    });


    function mmAjax(id, action, dataIn) {
        dataIn = dataIn || {};
        dataIn['id'] = id;
        dataIn['action'] = action;

        /* Ajax call to action; calls MODX resource pseudo-connector */
        return $.ajax({
            type: "POST",
            url: "mm-ajax.html",
            data: dataIn,
            dataType: "json"

        }).done(function () {
            mmSpinner.stop();
        }).fail(function (jqXHR, textStatus) {
            mmSpinner.stop();
            pop.setText(action + ' failed on message' + id + ' ' + textStatus);
            pop.load(40);
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

        if (e.html() == mmLex('mm_no')) {
            e.html(mmLex('mm_yes'));
            e.toggleClass("Yes No");
            mmAjax(id, 'security/message/read', {})
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

        if (read.html() == mmLex('mm_yes')) {

            mmSpinner.spin(spinnerTarget);
            promise8 = mmAjax(id, 'security/message/unread', {});
            promise8.done(function (data) {
                mmSpinner.stop();
                read.toggleClass("Yes No");
                read.html(mmLex('mm_no'));
            });
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
        mmMarkRead(id, mmLex('mm_yes'));
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

    function createSpinner() {
        var opts = {
            lines: 17, // The number of lines to draw
            length: 17, // The length of each line
            width: 4, // The line thickness
            radius: 5, // The radius of the inner circle
            corners: 1, // Corner roundness (0..1)
            rotate: 0, // The rotation offset
            direction: 1, // 1: clockwise, -1: counterclockwise
            color: '#56A717', // #rgb or #rrggbb or array of colors
            speed: 0.6, // Rounds per second
            trail: 81, // Afterglow percentage
            shadow: false, // Whether to render a shadow
            hwaccel: false, // Whether to use hardware acceleration
            className: 'spinner', // The CSS class to assign to the spinner
            zIndex: 2e9, // The z-index (defaults to 2000000000)
            top: '50%', // Top position relative to parent
            left: '50%' // Left position relative to parent
        };
        return new Spinner(opts);
    }

     function Popup(popup,container) {
        var thisPopup = this,
            timer,
            counter = 2,
            countDown = $("#countDown").text(counter.toString());

        thisPopup.setText = function(text) {
            popup.find('p').html(text);
        };

        thisPopup.load = function(counter, fadeTime) {
            counter = counter || 2;
            fadeTime = fadeTime || 150;
            container.animate({
                "opacity": "1"
            },fadeTime, function() {
                popup.fadeIn(fadeTime);
            });

            container.off("click").on("click", function() {
                thisPopup.unload(fadeTime);
            });

            $('#popupBoxClose').off("click").on("click", function() {
                thisPopup.unload(fadeTime);
            });

            timer = setInterval(function() {
                counter--;
                if(counter < 0) {
                    thisPopup.unload(fadeTime);
                }
            }, 500);
        };

        thisPopup.unload = function(fadeTime) {

            clearInterval(timer);

            popup.fadeOut(fadeTime, function(){
                container.animate({
                    "opacity": "1"
                },fadeTime);
            });
        };
    }

});



