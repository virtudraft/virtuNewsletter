<?php
$xpdo_meta_map['vnewsReports']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'table' => 'reports',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'newsletter_id' => NULL,
    'subscriber_id' => NULL,
    'status' => NULL,
    'status_logged_on' => NULL,
  ),
  'fieldMeta' => 
  array (
    'newsletter_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'subscriber_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'status' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => false,
    ),
    'status_logged_on' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'fk_modx_virtunewsletter_reports_modx_virtunewsletter_newsle_idx' => 
    array (
      'alias' => 'fk_modx_virtunewsletter_reports_modx_virtunewsletter_newsle_idx',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'newsletter_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'fk_modx_virtunewsletter_reports_modx_virtunewsletter_subscr_idx' => 
    array (
      'alias' => 'fk_modx_virtunewsletter_reports_modx_virtunewsletter_subscr_idx',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'subscriber_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Newsletters' => 
    array (
      'class' => 'vnewsNewsletters',
      'local' => 'newsletter_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Subscribers' => 
    array (
      'class' => 'vnewsSubscribers',
      'local' => 'subscriber_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
