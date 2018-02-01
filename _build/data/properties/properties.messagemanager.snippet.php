<?php
/**
 * Properties file for MessageManager snippet
 *
 * Copyright 2015-2018 by Bob Ray <https://bobsguides.com>
 * Created on 02-28-2015
 *
 * @package messagemanager
 * @subpackage build
 */




$properties = array (
  'allowed_groups' => 
  array (
    'name' => 'allowed_groups',
    'desc' => 'allowed_groups_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'messagemanager:properties',
    'area' => '',
  ),
  'cssFile' => 
  array (
    'name' => 'cssFile',
    'desc' => 'cssFile_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'messagemanager.css',
    'lexicon' => 'messagemanager:properties',
    'area' => '',
  ),
  'jsChunk' => 
  array (
    'name' => 'jsChunk',
    'desc' => 'jsChunk_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'mmAjaxJS',
    'lexicon' => 'messagemanager:properties',
    'area' => '',
  ),
  'language' => 
  array (
    'name' => 'language',
    'desc' => 'language_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'en',
    'lexicon' => 'messagemanager:properties',
    'area' => '',
  ),
  'outerTpl' => 
  array (
    'name' => 'outerTpl',
    'desc' => 'outerTpl_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'MessageOuterTpl',
    'lexicon' => 'messagemanager:properties',
    'area' => '',
  ),
  'recipient_options' => 
  array (
    'name' => 'recipient_options',
    'desc' => 'mm_recipient_options_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'user,usergroup,all',
    'lexicon' => 'messagemanager:properties',
    'area' => '',
  ),
  'redirect_to' => 
  array (
    'name' => 'redirect_to',
    'desc' => 'redirect_to_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => 'messagemanager:properties',
    'area' => '',
  ),
  'tpl' => 
  array (
    'name' => 'tpl',
    'desc' => 'tpl_desc',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'MessageTpl',
    'lexicon' => 'messagemanager:properties',
    'area' => '',
  ),
);

return $properties;

