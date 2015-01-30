<?php
/**
 * MessageManager controller for MessageManager extra
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


$validActions = array(
    'security/message/delete',
    'security/message/read',
    'security/message/unread',
);
if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $validActions)) {
    @session_cache_limiter('public');
    define('MODX_REQP', false);
}
define('MODX_API_MODE', true);
// this goes to the www.domain.name/index.php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';
$mypackageCorePath = $modx->getOption('mypackage.core_path', NULL, $modx->getOption('core_path') . 'components/mypackage/');
require_once $mypackageCorePath . 'model/mypackage.class.php';
$modx->mypackage = new MyPackage($modx);
$modx->lexicon->load('mypackage:web');
if (in_array($_REQUEST['action'], $validActions)) {
    $version = $modx->getVersionData();
    if (version_compare($version['full_version'], '2.1.1-pl') >= 0) {
        if ($modx->user->hasSessionContext($modx->context->get('key'))) {
            $_SERVER['HTTP_MODAUTH'] = $_SESSION["modx.{$modx->context->get('key')}.user.token"];
        } else {
            $_SESSION["modx.{$modx->context->get('key')}.user.token"] = 0;
            $_SERVER['HTTP_MODAUTH'] = 0;
        }
    } else {
        $_SERVER['HTTP_MODAUTH'] = $modx->site_id;
    }
    $_REQUEST['HTTP_MODAUTH'] = $_SERVER['HTTP_MODAUTH'];
}
// try this
// echo $modx->user->get('id');
/* handle request */
$connectorRequestClass = $modx->getOption('modConnectorRequest.class', NULL, 'modConnectorRequest');
$modx->config['modRequest.class'] = $connectorRequestClass;
$connectorResponseClass = $modx->getOption('modConnectorResponse.class', NULL, 'modConnectorResponse');
$modx->config['modResponse.class'] = $connectorResponseClass;
// $path = $modx->getOption('processorsPath', $modx->mypackage->config, $mypackageCorePath . 'processors/');
$path = $modx->getOption('processors_path');
$modx->getRequest();
$modx->request->sanitizeRequest();
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location'        => '',
));