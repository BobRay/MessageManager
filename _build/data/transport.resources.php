<?php
/**
 * resources transport file for MessageManager extra
 *
 * Copyright 2015-2017 Bob Ray <https://bobsguides.com>
 * Created on 01-26-2015
 *
 * @package messagemanager
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $resources */


$resources = array();

$resources[1] = $modx->newObject('modResource');
$resources[1]->fromArray(array (
  'id' => 1,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'Message Manager',
  'longtitle' => '',
  'description' => '',
  'alias' => 'message-manager',
  'link_attributes' => '',
  'published' => true,
  'isfolder' => true,
  'introtext' => '',
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 76,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 1,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'class_key' => 'modDocument',
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[1]->setContent(file_get_contents($sources['data'].'resources/message_manager.content.html'));

$resources[2] = $modx->newObject('modResource');
$resources[2]->fromArray(array (
  'id' => 2,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'mmAjax',
  'longtitle' => '',
  'description' => '',
  'alias' => 'mm-ajax',
  'link_attributes' => '',
  'published' => true,
  'isfolder' => false,
  'introtext' => '',
  'richtext' => false,
  'template' => 0,
  'menuindex' => 0,
  'searchable' => true,
  'cacheable' => true,
  'createdby' => 1,
  'editedby' => 1,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => '',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => true,
  'class_key' => 'modDocument',
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[2]->setContent(file_get_contents($sources['data'].'resources/mmajax.content.html'));

return $resources;
