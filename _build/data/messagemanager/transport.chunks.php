<?php
/**
 * chunks transport file for MessageManager extra
 *
 * Copyright 2015 by Bob Ray <https://bobsguides.com>
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
/* @var xPDOObject[] $chunks */


$chunks = array();

$chunks[1] = $modx->newObject('modChunk');
$chunks[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'MessageOuterTpl',
  'description' => 'Outer Tpl for message display',
  'properties' => NULL,
), '', true, true);
$chunks[1]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/messageoutertpl.chunk.html'));

$chunks[2] = $modx->newObject('modChunk');
$chunks[2]->fromArray(array (
  'id' => 2,
  'property_preprocess' => false,
  'name' => 'MessageTpl',
  'description' => 'Tpl for individual messages',
  'properties' => 
  array (
  ),
), '', true, true);
$chunks[2]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/messagetpl.chunk.html'));

$chunks[3] = $modx->newObject('modChunk');
$chunks[3]->fromArray(array (
  'id' => 3,
  'property_preprocess' => false,
  'name' => 'mmAjaxJs',
  'description' => 'JS for Ajax resource',
), '', true, true);
$chunks[3]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/mmajaxjs.chunk.html'));


$properties = include $sources['data'].'properties/properties.mmajaxjs.chunk.php';
$chunks[3]->setProperties($properties);
unset($properties);

return $chunks;
