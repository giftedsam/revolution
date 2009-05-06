<?php
/**
 * @package modx
 * @subpackage mysql
 */
$xpdo_meta_map['modNamespace']= array (
  'package' => 'modx',
  'table' => 'namespaces',
  'fields' => 
  array (
    'name' => '',
    'path' => '',
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '40',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'pk',
    ),
    'path' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'default' => '',
    ),
  ),
  'composites' => 
  array (
    'modLexiconTopic' => 
    array (
      'class' => 'modLexiconTopic',
      'local' => 'name',
      'foreign' => 'namespace',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'modLexiconEntry' => 
    array (
      'class' => 'modLexiconEntry',
      'local' => 'name',
      'foreign' => 'namespace',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'modSystemSetting' => 
    array (
      'class' => 'modSystemSetting',
      'local' => 'name',
      'foreign' => 'namespace',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'modContextSetting' => 
    array (
      'class' => 'modContextSetting',
      'local' => 'name',
      'foreign' => 'namespace',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'modUserSetting' => 
    array (
      'class' => 'modUserSetting',
      'local' => 'name',
      'foreign' => 'namespace',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'modAction' => 
    array (
      'class' => 'modAction',
      'local' => 'name',
      'foreign' => 'namespace',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
if (XPDO_PHP4_MODE) $xpdo_meta_map['modNamespace']['composites']= array_merge($xpdo_meta_map['modNamespace']['composites'], array_change_key_case($xpdo_meta_map['modNamespace']['composites']));
$xpdo_meta_map['modnamespace']= & $xpdo_meta_map['modNamespace'];