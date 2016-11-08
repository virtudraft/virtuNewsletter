<?php
$xpdo_meta_map['vnewsNewsletters']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'table' => 'newsletters',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'parent_id' => 0,
    'resource_id' => NULL,
    'subject' => NULL,
    'content' => NULL,
    'created_on' => NULL,
    'created_by' => NULL,
    'scheduled_for' => NULL,
    'stopped_at' => 0,
    'is_recurring' => 0,
    'recurrence_range' => NULL,
    'recurrence_number' => NULL,
    'is_active' => 1,
    'is_paused' => 0,
  ),
  'fieldMeta' => 
  array (
    'parent_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
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
      'null' => true,
    ),
    'stopped_at' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
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
    'is_paused' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
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
    'parent_id' => 
    array (
      'alias' => 'parent_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'parent_id' => 
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
    'Children' => 
    array (
      'class' => 'vnewsNewsletters',
      'local' => 'id',
      'foreign' => 'parent_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'NewslettersHasCategories' => 
    array (
      'class' => 'vnewsNewslettersHasCategories',
      'local' => 'id',
      'foreign' => 'newsletter_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Reports' => 
    array (
      'class' => 'vnewsReports',
      'local' => 'id',
      'foreign' => 'newsletter_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Parent' => 
    array (
      'class' => 'vnewsNewsletters',
      'local' => 'parent_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
