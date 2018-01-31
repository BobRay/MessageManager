<?php
/**
 * MessageManager snippet for MessageManager extra
 *
 * Copyright 2015-2018 by Bob Ray <http://bobsguides.com>
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

/** Properties

 * @property &recipient_options textfield -- Options for sending messages; these options show up in the dropdown list when sending a new message; you must include at least one option; Default: user,usergroup,all.
 *
 * @property &redirect_to textfield -- Id of Resource to redirect to if user is not logged in (e.g. the Login page); If this is not set, MessageManager will redirect to the Login page if its pagetitle is Login, or the site_start page if not; Default: (empty)..
 *
 * @property &allowed_groups textfield -- Comma-separated list of User Group names or IDs that are allowed to access MessageManager; if empty all groups are allowed; Default: (empty)..
 */
 
$version = '?v=1.0.1-rc'; 

$assets_path = $modx->getOption('mm.assets_path', NULL, $modx->getOption('assets_path') .
    'components/messagemanager/');
$assets_url = $modx->getOption('mm.assets_url', NULL, $modx->getOption('assets_url') .
    'components/messagemanager/');

/* Load lexicon string */
$language = $modx->getOption('language', $scriptProperties, $modx->getOption('culture_key'));
$language = empty($language) ? 'en' : $language;
$modx->lexicon->load($language . ':messagemanager:default');
$lex = $modx->lexicon->getFileTopic($language, 'messagemanager', 'default');
$jsonLex = $modx->toJSON($lex);
$aaLex = file_get_contents($assets_path . 'js/aalexicon.txt');
$aaLex = str_replace('[[+mm_lexicon]]', $jsonLex, $aaLex);
$modx->regClientStartupScript($aaLex);

/* Load JQuery */


$modx->regClientStartupScript('//ajax.googleapis.com/ajax/libs/jquery/1.12.1/jquery.min.js"');    
$modx->regClientCSS('//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css');
$modx->regClientStartupScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js');

/* Load local JS and CSS */

$modx->regClientStartupScript($assets_url . 'js/context-menu.js');
$modx->regClientStartupScript($assets_url . 'js/spin-min.js');
// $modx->regClientStartupScript($assets_url . 'js/jquery-ui.min.js');
// $modx->regClientCSS($assets_url . 'css/jquery/jquery-ui.min.css');
$cssFile = $modx->getOption('cssFile', $scriptProperties, 'messagemanager.css') . $version;
$path = $assets_url . 'css/' . $cssFile;
$modx->regClientCSS($path);

/* load MessageManager JS from chunk */
$contentType = $modx->getObject('modContentType', array('mime_type' => 'text/html'));

if (!$contentType) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[MessageManager] could not get content type with mime_type: "text/html"');
} else {
    $fields = array(
        'html_file_extension' => $contentType->get('file_extensions'),
    );
    $jsChunk = $modx->getOption('jsChunk', $scriptProperties, 'mmAjaxJs', true);
    $modx->regClientStartupScript($modx->getChunk($jsChunk, $fields));
}


/* Forward to redirect_to resource if user is not logged in */
if ( (!$modx->user->hasSessionContext($modx->context->get('key')))) {
    $redirectToId = $modx->getOption('redirect_to', $scriptProperties, null, true);
    if ($redirectToId === null) {
        /* Try to find the Login page */
        $query = $modx->newQuery('modResource', array(
            'pagetitle' => 'Login',
        ));
        $query->select('id');
        $redirectToId =  $modx->getValue($query->prepare());
        /* Use site_start as default */
        $redirectToId = empty($redirectToId)
            ? $modx->getOption('site_start', null)
            : $redirectToId;
    }
    $url = $modx->makeUrl($redirectToId, "", "", "full");
    $modx->sendRedirect($url);
}

$allowedGroups = $modx->getOption('allowed_groups', $scriptProperties, '', true);

if (!empty($allowedGroups)) {
    $allowedGroups = explode(',', $allowedGroups);
    if (! $modx->user->isMember($allowedGroups)) {
        return $modx->lexicon('mm_unauthorized');
    }
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

$rOptions = 'user,usergroup,all';

$recipientOptions = $modx->getOption('recipient_options', $scriptProperties, $rOptions, true);
$optionArray = explode(',', $recipientOptions);
$optionArray = array_map('trim', $optionArray);
$finalOptions = "\n" . ' <option value = "0" > Select One </option > ';
foreach($optionArray as $opt) {
    $finalOptions .= "\n    " . '<option value="' . $opt . '">' .
        $modx->lexicon('mm_' . $opt) . '</option>';
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
    $fields['mm.read'] = $fields['mm.read']
        ? $modx->lexicon('mm_yes')
        : $modx->lexicon('mm_no');

    $inner .= $modx->getChunk($tpl, $fields);
}

$output = str_replace('[[+messages]]', $inner, $output);

return $output;