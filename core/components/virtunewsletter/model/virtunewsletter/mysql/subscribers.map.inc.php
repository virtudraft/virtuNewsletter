<?php
$xpdo_meta_map['Subscribers']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'table' => 'subscribers',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'user_id' => 0,
    'email' => NULL,
    'name' => NULL,
    'is_active' => 1,
  ),
  'fieldMeta' => 
  array (
    'user_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'email' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'is_active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
    ),
  ),
  'composites' => 
  array (
    'Reports' => 
    array (
      'class' => 'Reports',
      'local' => 'id',
      'foreign' => 'newsletter_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Users' => 
    array (
      'class' => 'Users',
      'local' => 'user_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
