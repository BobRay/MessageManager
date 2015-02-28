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
$language = $modx->getOption('language', $scriptProperties, $modx->getOption('culture_key'));
$language = empty($language) ? 'en' : $language;
$modx->lexicon->load($language . ':messagemanager:default');
$lex = $modx->lexicon->getFileTopic($language, 'messagemanager', 'default');
$jsonLex = $modx->toJSON($lex);


$cssFile = $modx->getOption('cssFile', $scriptProperties, 'messagemanager.css');
$jsFile = $modx->getOption('jsFile', $scriptProperties, 'messagemanager.js' . '?v=' . time());
$assets_url = $modx->getOption('mm.assets_url', NULL, $modx->getOption('assets_url') .
    'components/messagemanager/');
$assets_path = $modx->getOption('mm.assets_path', NULL, $modx->getOption('assets_path') .
    'components/messagemanager/');
$aaLex = file_get_contents($assets_path . 'js/aalexicon.txt');
$aaLex = str_replace('[[+mm_lexicon]]', $jsonLex, $aaLex);
$modx->regClientStartupScript($aaLex);
$path = $assets_url . 'css/' . $cssFile;
$modx->regClientCSS($path);
$modx->regClientStartupScript('//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"');
$modx->regClientStartupScript($assets_url . 'js/jquery-ui.min.js');
$modx->regClientStartupScript($assets_url . 'js/context-menu.js');
$modx->regClientStartupScript($assets_url . 'js/spin-min.js');

$modx->regClientCSS($assets_url . 'css/jquery/jquery-ui.min.css');
$modx->regClientCSS($assets_url . 'css/jquery/jquery-ui.theme.css');

$path = $assets_url . 'js/' . $jsFile;
$modx->regClientStartupScript($path);

/* do nothing if user is not logged in or member of admin group*/
if ( (!$modx->user->hasSessionContext('web')) && (! $modx->user->isMember('Administrator'))) {
  return '';
}

/* Display messages */
$tpl = $modx->getOption('tpl', $scriptProperties, 'messageTpl');
$outerTpl = $modx->getOption('outerTpl', $scriptProperties, 'messageOuterTpl');
$uid = $modx->user->get('id');
// echo "<p>UserId: " . $uid;
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

$rOptions = 'user:User,usergroup:User Group,all:All Users';

$recipientOptions = $modx->getOption('recipientOptions', $scriptProperties, $rOptions, true);
$optionArray = explode(',', $recipientOptions);
$finalOptions = "\n" . ' <option value = "0" > Select One </option > ';
foreach( $optionArray as $opt) {
    $couple = explode(':', $opt);
    $finalOptions .= "\n    " . '<option value="' . $couple[0] . '">' . $couple[1] . '</option>';
}


$output = $modx->getChunk($outerTpl);
$output = str_replace('[[+recipient_options]]', $finalOptions, $output);

foreach ($messages as $message) {
    /** @var $message xPDOObject */
    $fields = $message->toArray ('mm.', true);
    $query = $modx->newQuery('modUser', array(
        'id' => $fields['mm.sender'],
    ));
    $query->select('username');
    $username = $modx->getValue($query->prepare());
    $fields['mm.sender_id'] = $fields['mm.sender'];
    $fields['mm.sender'] = $username;
    $fields['mm.class'] = $fields['mm.read']? 'read' : 'unread';
    $fields['mm.read_indicator'] = $fields['mm.read'] ? 'Yes' : 'No';
    $fields['mm.read'] = $fields['mm.read'] ? $modx->lexicon('mm_yes') : $modx->lexicon('mm_no');

    $inner .= $modx->getChunk($tpl, $fields);
}

$output = str_replace('[[+messages]]', $inner, $output);

return $output;