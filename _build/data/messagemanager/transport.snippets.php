<?php
/**
 * snippets transport file for MessageManager extra
 *
 * Copyright 2014 by Bob Ray <http://bobsguides.com>
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
/* @var xPDOObject[] $snippets */


$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'MessageManager',
  'description' => 'MessageManager snippet',
  'properties' => 
  array (
  ),
), '', true, true);
$snippets[1]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/messagemanager.snippet.php'));

$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array (
  'id' => 2,
  'property_preprocess' => false,
  'name' => 'mmAjax',
  'description' => 'MessageManager ajax connector snippet',
), '', true, true);
$snippets[2]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/mmajax.snippet.php'));


$properties = include $sources['data'].'properties/properties.mmajax.snippet.php';
$snippets[2]->setProperties($properties);
unset($properties);

return $snippets;
