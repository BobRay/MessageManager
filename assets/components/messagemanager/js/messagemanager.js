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
    var messageText = '';
    $('#the-node').contextMenu({
        /* selector: 'li', */
        selector: 'tr',
        callback: function (key, options) {
            var id = getId($(this).attr('id'));
            // var m = "clicked: " + key + " on " + getId($(this).attr('id'));
            // window.console && console.log(m) || alert(m);
            switch (key) {
            case 'markunread':
                mmMarkUnread(id, 'No');
                break;
            case 'delete':
                alert('Deleting');
                break;
            case 'reply':
                mmReply(id);
                break;
            }
        },
       items: {

           "markunread": {name: "Mark Unread", icon: "markunread"},
           "delete": {name: "Delete", icon: "delete"},
           "reply": {name: "Reply", icon: "reply"},
           "newmessage": {name: "New Message", icon: "newmessage", disabled: true},
           "sep1": "---------",
           "quit": {name: "Cancel", icon: "cancel"}
       }
   });

    function mmReply(id, newMessage) {
        var senderId = $('#mm_sender' + id).html();
        // alert('Sender: ' + senderId);
        var messageText = $("#mm_message" + id).find('td:first').html();
        // alert(messageText);
        $("#myDialog").dialog({
            autoOpen: true,
            maxWidth: 600,
            maxHeight: 500,
            width: 600,
            height: 500,
            modal: true,
            draggable: false,
            buttons: {
                "Quote": {
                    text: "Quote",
                    id: "mm_left_button",
                    class: 'mm_left_button',
                    click: function() {
                        $("#myTextarea").html("<< " + messageText + " >>");
                    }
                },

                "Cancel": function () {
                    $(this).dialog("close");
                },
                "Send": {
                    text: "Send",
                    click: function() {
                        alert('Message Sent');
                        $(this).dialog("close");
                    }

                }
            }
         });

        $("#emailSubmit").on("click", function () {
            alert('Clicked');
            $("#myDialog").dialog("close");
        });
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
            // alert(id);
            // mmAjax(id, 'security/message/delete');
            $('tr#' + id).remove();

        });

        // alert('Delete Clicked');
    });


    function mmAjax(id, action) {
        /* Ajax call to action; calls MODX resource pseudo-connector */
        var ajaxRequest = $.ajax({
            type: "POST",
            url: "http://localhost/addons/mm-ajax.html",
            data: {'action': action, 'id': id}
        });

        ajaxRequest.done(function (msg) {
            // alert(action + ' succeeded on message ' + id);
        });

        ajaxRequest.fail(function (jqXHR, textStatus) {
            alert(action + ' failed on message ' + id + ' ' + textStatus);
        });
    }


    /* Pulls ID out of selector ID like 'mm_message12' */
    function getId(s) {
        var number = s.match(/\d+$/);
        number = parseInt(number, 10);
        return number
    }

    /* Mark message read in DB and on screen */

    function mmMarkRead(id, message) {
        var e = $('#mm_read' + id);

        if (e.html() == 'No') {
            e.html('Yes');
            e.toggleClass("Yes No")
            mmAjax(id, 'security/message/read')
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
            mmAjax(id, 'security/message/unread');
        }
        e = $('#mm_message' + id);
        mmCloseSubject(e, id);
    }

    /* Show message if hidden; mark read in DB and on screen;
     change down arrow to up arrow; change cursor to -
     */

    function mmOpenSubject(e, id) {


        e.show();
        e.attr("style", "display:table-row");
        var td = $('.mm_message');
        td.attr('style', 'display:table-cell');
        td.attr('colspan', "5");
        mmMarkRead(id, 'Yes');
        $('#mm_expand' + id).html('\u25B4');
        $('#mm_subject' + id).toggleClass("zoomin zoomout");
    }

    /* Hide message if visible; change up arrow to down arrow */
    function mmCloseSubject(e, id) {
        e.attr('colspan', 5);
        e.hide();
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


});



