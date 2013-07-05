<?php
$xpdo_meta_map['vnewsNewsletters']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'table' => 'newsletters',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'resource_id' => NULL,
    'subject' => NULL,
    'content' => NULL,
    'created_on' => NULL,
    'created_by' => NULL,
    'scheduled_for' => NULL,
    'is_recurring' => 0,
    'recurrence_range' => NULL,
    'recurrence_number' => NULL,
    'is_active' => 1,
  ),
  'fieldMeta' => 
  array (
    'resource_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'subject' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'content' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
    ),
    'created_on' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'created_by' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
    ),
    'scheduled_for' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'is_recurring' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'recurrence_range' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => true,
    ),
    'recurrence_number' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '2',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
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
  'indexes' => 
  array (
    'resource_id' => 
    array (
      'alias' => 'resource_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'resource_id' => 
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
    'vnewsNewslettersHasCategories' => 
    array (
      'class' => 'vnewsNewslettersHasCategories',
      'local' => 'id',
      'foreign' => 'newsletter_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'vnewsReports' => 
    array (
      'class' => 'vnewsReports',
      'local' => 'id',
      'foreign' => 'newsletter_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
