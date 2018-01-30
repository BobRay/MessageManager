<?php
/**
 * en properties topic lexicon file for MessageManager extra
 *
 * Copyright 2014-2018 Bob Ray <https://bobsguides.com>
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
 * en properties topic lexicon strings
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package messagemanager
 **/




/* Used in properties.mmajax.snippet.php */

$_lang['mm_exclude_groups_desc'] = 'Comma-separated list of User Group IDs or names that can not be sent bulk messages; if empty all user groups can be sent messages; default: empty';

$_lang['mm_user_group_desc'] = 'User Group ID or name that has opted to receive messages; if empty, all users can be sent a message; default: empty.';

/* Used in properties.messagemanager.snippet.php */
$_lang['cssFile_desc'] = 'CSS file to use for Message Manager; default: messagemanager.css';
$_lang['jsChunk_desc'] = 'Chunk containing Message Manager JS; default: mmAjaxJS';
$_lang['language_desc'] = 'Language to use in Message Manager; default: en';
$_lang['outerTpl_desc'] = 'Outer Tpl chunk for Message Manager;default: MessageOuterTpl';
$_lang['tpl_desc'] = 'Tpl chunk for individual messages; default: MessageTpl';
$_lang['redirect_to_desc'] = 'Id of Resource to redirect to if user is not logged in (e.g. the Login page); If this is not set, MessageManager will redirect to the Login page if its pagetitle is Login, or the site_start page if not.';
$_lang['allowed_groups_desc'] = 'Comma-separated list of User Group names or IDs that are allowed to access MessageManager; if empty all groups are allowed.';
$_lang['mm_recipient_options_desc'] = 'Options for sending messages; these options show up in the dropdown list when sending a new message; you must include at least one option; the captions for the three options are lexicon strings in the default.inc.php file; default: user,usergroup,all.';