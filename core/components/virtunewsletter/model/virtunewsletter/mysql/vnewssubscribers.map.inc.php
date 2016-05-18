<?php
$xpdo_meta_map['vnewsSubscribers']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'table' => 'subscribers',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'user_id' => 0,
    'email' => NULL,
    'name' => NULL,
    'email_provider' => NULL,
    'is_active' => 1,
    'hash' => NULL,
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
    'email_provider' => 
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
    'hash' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'fk_modx_virtunewsletter_subscribers_modx_virtunewsletter_us_idx' => 
    array (
      'alias' => 'fk_modx_virtunewsletter_subscribers_modx_virtunewsletter_us_idx',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'user_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Reports' => 
    array (
      'class' => 'vnewsReports',
      'local' => 'id',
      'foreign' => 'subscriber_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'SubscribersHasCategories' => 
    array (
      'class' => 'vnewsSubscribersHasCategories',
      'local' => 'id',
      'foreign' => 'subscriber_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Users' => 
    array (
      'class' => 'vnewsUsers',
      'local' => 'user_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
