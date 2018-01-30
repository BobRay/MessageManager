<?php
/**
 * MessageAlert snippet for MessageManager extra
 *
 * Copyright 2015-2018 by Bob Ray <http://bobsguides.com>
 * Created on 03-05-2015
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
 * Show current user how many messages are available
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package messagemanager
 **/

$language = $modx->getOption('language', $scriptProperties,
    $modx->getOption('culture_key'));
$language = empty($language) ? 'en' : $language;
$modx->lexicon->load($language . ':messagemanager:default');

$count = (int) $modx->getCount('modUserMessage',
    array('recipient' => $modx->user->get('id')));

$unreadCount = (int) $modx->getCount('modUserMessage',
    array(
        'recipient' => $modx->user->get('id'),
        'read'      => false,
    )
);
$unread = $modx->lexicon('mm_unread');
$unreadCount = '(' . $unreadCount . ' ' . $unread . ')';
$noun = $modx->lexicon('mm_messages');

switch ($count) {
    case 0:
        $count = $modx->lexicon('mm_count_no');
        $unreadCount = '';
        break;
    case 1:
        $noun = $modx->lexicon('mm_message');
        break;
    default:
        break;
}


return "{$count} {$noun} {$unreadCount}";