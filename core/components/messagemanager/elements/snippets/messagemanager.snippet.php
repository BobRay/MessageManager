<?php
/**
 * MessageManager snippet for MessageManager extra
 *
 * Copyright 2015 by Bob Ray <http://bobsguides.com>
 * Created on 01-26-2015
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
 *
 * @package messagemanager
 */

/**
 * Description
 * -----------
 * MessageManager snippet
 *
 * Usage
 * -----
 * [[!MessageManager]]
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package messagemanager
 **/



if (! defined('MODX_CORE_PATH')) {
    include 'C:\xampp\htdocs\addons\assets\mycomponents\instantiatemodx\instantiatemodx.php';
}

$cssFile = $modx->getOption('cssFile', $scriptProperties, 'messagemanager.css');
$jsFile = $modx->getOption('jsFile', $scriptProperties, 'messagemanager.js?' . 'v=' . time());
$assets_url = $modx->getOption('mm.assets_url', NULL, $modx->getOption('assets_url') .
    '/assets/components/messagemanager');
$path = $assets_url . 'css/' . $cssFile;
$modx->regClientCSS($path);
$modx->regClientStartupScript('//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"');
$path = $assets_url . 'js/' . $jsFile;
$modx->regClientStartupScript($path);

// $modx->regClientStartupScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.js');

$modx->user =& $modx->getObject('modUser', 1);
/* do nothing if user is not logged in */
if (!$modx->user->hasSessionContext('web')) {
    // return '';
}



// echo $modx->user->get('username');
/* process form */

if (isset($_POST['submit'])) {
    echo print_r($_POST, true);
    if (isset($_POST['messages']) && (!empty($_POST['messages']))) {
        foreach ($_POST['messages'] as $messageId) {
            $msg = $modx->getObject('modUserMessage', (int) $messageId);
            if ($msg) {
               // $msg->remove();
                echo "removing message " . $messageId;
            }
        }
    }
}

/* Display messages */
$tpl = $modx->getOption('tpl', $scriptProperties, 'messageTpl');
$outerTpl = $modx->getOption('outerTpl', $scriptProperties, 'messageOuterTpl');
$uid = $modx->user->get('id');
$c = $modx->newQuery('modUserMessage');
$c->sortby('date_sent', 'DESC');
$c->where(
   array('recipient' => $uid
   )
);
$messages = $modx->getCollection('modUserMessage', $c);
$count = !empty($messages) ? count($messages) : 'no';
$modx->setPlaceholder('messageCount', $count);
$modx->setPlaceholder('message_count', count($messages));
if (empty($messages)) {
    return 'No Messages';
}

$output = $modx->getChunk($outerTpl);

foreach ($messages as $message) {
    /** @var $message xPDOObject */
    $fields = $message->toArray ('mm.', true);
    $query = $modx->newQuery('modUser', array(
        'id' => $fields['mm.sender'],
    ));
    $query->select('username');
    $username = $modx->getValue($query->prepare());
    // $fields['mm.date_sent'] = strftime("%b %d, %Y at %I:%M %p", strtotime($fields['mm.date_sent']));
    $fields['mm.sender_id'] = $fields['mm.sender'];
    $fields['mm.sender'] = $username;
    $fields['mm.class'] = $fields['mm.read']? 'read' : 'unread';
    $fields['mm.read_indicator'] = $fields['mm.read'] ? 'Yes' : 'No';
    $fields['mm.read'] = $fields['mm.read'] ? 'Yes' : 'No';

    $inner .= $modx->getChunk($tpl, $fields);
}

$output = str_replace('[[+messages]]', $inner, $output);

    // echo $output;

return $output;