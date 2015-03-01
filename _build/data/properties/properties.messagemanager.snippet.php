<?php
/**
 * Properties file for MessageManager snippet
 *
 * Copyright 2014 by Bob Ray <http://bobsguides.com>
 * Created on 02-28-2015
 *
 * @package messagemanager
 * @subpackage build
 */




$properties = array (
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
);

return $properties;

