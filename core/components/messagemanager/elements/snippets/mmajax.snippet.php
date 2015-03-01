<?php
/** 
 * mmAjax snippet for MessageManager extra
 *
 * Copyright 2015 by Bob Ray <http://bobsguides.com>
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

/** Properties

 * @property &exclude_groups textfield -- Comma-separated list of User Group IDs or names that can not be sent bulk messages; if empty all user groups can be sent messages; Default: empty.
 *
 * @property &user_group textfield -- User Group ID or name that has opted to receive messages; if empty, all users can be sent a message; Default: empty.
 */

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

if (!function_exists('getGroupId')) {
    function getGroupId($group) {
        global $modx;
        if (! is_numeric(substr($group, 0, 1))) {
            $key = 'name';
        } else {
            $key = 'id';
        }
        $query = $modx->newQuery('modUserGroup', array(
            $key => $group,
        ));
        $query->select('id');
        return $modx->getValue($query->prepare());
    }

}

$validActions =  'security/message/remove,security/message/read,security/message/unread,security/message/create,security/user/getlist,security/group/getlist';


$validActions = $modx->getOption ('validActions', $scriptProperties, $validActions, true);
$validActions = explode(',', $validActions);
$validActions = array_map('trim', $validActions);

if (isset($_REQUEST) && !empty($_REQUEST)) {
    $action = $modx->getOption('action', $_REQUEST, '');
    if (! empty($action)) {
        unset($_REQUEST['action']);
    }

    if (! in_array($action, $validActions, true)) {
        my_debug('Invalid Action: ' . $action, $modx);
        $retVal = array(
            'success'       => false,
            'error_message' => 'Invalid Action',
        );
        return $modx->toJSON($retVal);

    }
    $props = $modx::sanitize($_REQUEST, $modx->sanitizePatterns);

    $userGroup = $modx->getOption('user_group', $scriptProperties, '', true);
    $userGroup = getGroupId($userGroup);

    if ($action === 'security/user/getlist') {
        if (!empty($userGroup)) {
            $props['usergroup'] = $userGroup;
        }
        unset($userGroup);
    }

    if ($action === 'security/message/create') {
        if (!empty($userGroup)) {
            /* See if user has opted-in */
            $recipientId = $modx->getOption('user', $_REQUEST, 0);
            $query = $modx->newQuery('modUserGroupMember', array(
                'member' => $recipientId,
                'user_group' => $userGroup,
            ));
            $query->select('member');
            $member = $modx->getValue($query->prepare());

            if (empty($member)) {
                /* Not a member, bypass processor */
                $retVal = array(
                    'success' => true,
                );
                return $modx->toJSON($retVal);
            }

        }
    }

    if ($action === 'security/group/getlist') {
        $exGroups = $modx->getOption('exclude_groups', $scriptProperties, '', true);
        if (!empty($exGroups)) {
            $temp = array();
            $exGroups = explode(',', $exGroups);
            foreach($exGroups as $exGroup) {
                $v = getGroupId($exGroup);
                if (! empty($v)) {
                    $temp[] = $v;
                }
            }
            if (!empty($temp)) {
                $temp = implode(',', $temp);
                $props['exclude'] = $temp;
            }
        }
        unset($exGroups, $v, $temp);
    }


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
            $retVal['data'] =& $modx->fromJSON($response->response);
            if ($action == 'security/user/getlist') {
                $users =& $retVal['data']['results'];
                foreach ($users as &$user) {
                    unset($user['class_key'], $user['remote_key'], $user['remote_data'],
                        $user['hash_class'], $user['session_stale'], $user['email'], $user['cls'],
                        $user['sudo'], $user['active'], $user['blocked'], $user['primary_group']);
                }
            }
        }
    }

} else {
    $retVal =  array(
        'success' => false,
        'error_message' => 'Empty Request',
    );
}

return $modx->toJSON($retVal);