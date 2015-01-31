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

$validActions = array(
    'security/message/delete',
    'security/message/read',
    'security/message/unread',
    'security/message/create'
);

if (isset($_POST) && !empty($_POST)) {
    $action = $modx->getOption('action', $_POST, '');
    $id = $modx->getOption('id', $_POST, null);
    if (! in_array($action, $validActions)) {
        return $modx->error->failure('Not Authorized');
    }
    if ($id == null) {
        return $modx->error->failure('Param not set');
    }
    return $modx->runProcessor($action, array('id' => $id));

}