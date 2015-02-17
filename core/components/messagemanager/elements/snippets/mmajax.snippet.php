<?php
/**
 * mmAjax snippet for MessageManager extra
 *
 * Copyright 2014 by Bob Ray <http://bobsguides.com>
 * Created on 01-29-2015
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
 * MessageManager ajax connector snippet
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package messagemanager
 **/
if (! function_exists('my_debug')) {
    function my_debug($msg, $modx, $clear = false) {
        /** @var $chunk modChunk */
        /** @var $modx modX */
        $content = '';
        $chunk = $modx->getObject('modChunk', array('name' => 'Debug'));
        if (! $clear) {
            $content = $chunk->getContent();
        }

        $chunk->setContent($content . "\n" .  $msg);
        $chunk->save();
    }
}

$validActions = array(
    'security/message/remove',
    'security/message/read',
    'security/message/unread',
    'security/message/create',
    'security/user/getlist',
    'security/group/getlist',
);

$validActions = $modx->getOption ('validActions', $scriptProperties, $validActions, true);

if (isset($_REQUEST) && !empty($_REQUEST)) {
    $action = $modx->getOption('action', $_REQUEST, '');
    if (! empty($action)) {
        unset($_REQUEST['action']);
    }

    /*my_debug('Action: ' . $action, $modx);
    $id = $modx->getOption('id', $_REQUEST, null);
    my_debug('Id: ' . $id, $modx);
    my_debug("REQUEST: " . print_r($_REQUEST, true), $modx);
    my_debug("SP: " . print_r($scriptProperties, true), $modx);*/
    if (! in_array($action, $validActions)) {
        my_debug('Invalid Action: ' . $action, $modx);
        $retVal = array(
            'success'       => false,
            'error_message' => 'Invalid Action',
        );
        return $modx->toJSON($retVal);

    }
    $props = $modx::sanitize($_REQUEST, $modx->sanitizePatterns);


    /* @var $response modProcessorResponse */
    $response =  $modx->runProcessor($action, $props);

    if ($response->isError()) {
        if ($response->hasFieldErrors()) {
            $fieldErrors = $response->getAllErrors();
            $errorMessage = implode("\n", $fieldErrors);
        } else {
            $errorMessage = 'An error occurred: ' . $response->getMessage();
        }
        $retVal = array(
            'success' => false,
            'error_message' => $errorMessage,
        );
    } else {
        $retVal = array(
            'success' => true,
        );
        if (isset($response->response)) {
            $retVal['data'] = $modx->fromJSON($response->response);
        }
    }

} else {
    $retVal =  array(
        'success' => false,
        'error_message' => 'Empty Request',
    );
}

return $modx->toJSON($retVal);