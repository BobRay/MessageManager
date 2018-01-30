<?php
/** 
 * mmAjax snippet for MessageManager extra
 *
 * Copyright 2015-2018 by Bob Ray <http://bobsguides.com>
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

if (!$modx->hasPermission('messages')) {
    $retVal = array(
        'success'       => false,
        'error_message' => $modx->lexicon('permission_denied'),
    );

    return $modx->toJSON($retVal);
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
        $retVal = array(
            'success'       => false,
            'error_message' => 'Invalid Action',
        );
        return $modx->toJSON($retVal);

    }
    $props = $modx::sanitize($_REQUEST, $modx->sanitizePatterns);

    $userGroup = $modx->getOption('user_group', $scriptProperties, '', true);
    $userGroup = getGroupId($userGroup);
    $type = $modx->getOption('type', $_REQUEST, '', true);

    if ($action === 'security/user/getlist') {
        if (!empty($userGroup)) {
            $props['usergroup'] = $userGroup;
        }
        unset($userGroup);
    }

    if ($action === 'security/message/create') {
        if ((!empty($userGroup)) && $type == 'all' ) {
            /* Bypass processor to send only to user group */

            $c = $modx->newQuery('modUserGroupMember');
            $c->where(array(
                 'user_group' => $userGroup,
            ));

            $members = $modx->getCollection('modUserGroupMember', $c);
            $subject = $modx->getOption('subject', $_REQUEST, 'No Subject');
            $messagetext = $modx->getOption('message', $_REQUEST, 'No Message');
            $sender = $modx->user->get('id');

            foreach ($members as $member) {
                /** @var $member modUserGroupMember */
                $message = $modx->newObject('modUserMessage');
                $message->set('recipient', $member->get('member'));
                $message->set('sender', $sender);
                $message->set('subject', $subject);
                $message->set('message', $messagetext);
                $message->set('date_sent', time());
                $message->set('private', false);
                @$message->save();

            }
            $retVal = array(
                'success' => true,
            );

            return $modx->toJSON($retVal);

        }
    }

    if ($action === 'security/group/getlist') {
        $exGroups = $modx->getOption('exclude_groups', $scriptProperties, '', true);
        $temp = array();

        if (!empty($userGroup)) {
            $grps = $modx->getCollection('modUserGroup');

            foreach($grps as $grp) {
                /** @var $grp modUserGroup */
                if ($grp->get('id') == $userGroup) {
                    continue;
                }
                $temp[] = $grp->get('id');
            }
            if (!empty($temp)) {
                $props['exclude'] = implode(',', $temp);
            }

        } elseif (!empty($exGroups)) {
            $exGroups = explode(',', $exGroups);
            foreach($exGroups as $exGroup) {
                $v = getGroupId($exGroup);
                if (! empty($v)) {
                    $temp[] = $v;
                }
            }
            if (!empty($temp)) {
                $props['exclude'] = implode(',', $temp);
            }
        }
        unset($exGroups, $exGroup, $grps, $grp, $v, $temp);
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