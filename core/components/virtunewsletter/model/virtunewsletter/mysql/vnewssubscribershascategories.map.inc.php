<?php
$xpdo_meta_map['vnewsSubscribersHasCategories']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'table' => 'subscribers_has_categories',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'subscriber_id' => NULL,
    'category_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'subscriber_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'category_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
    'fk_modx_virtunewsletter_subscribers_has_modx_virtunewslette_idx' => 
    array (
      'alias' => 'fk_modx_virtunewsletter_subscribers_has_modx_virtunewslette_idx',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'category_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'fk_modx_virtunewsletter_subscribers_has_modx_virtunewslette_idx1' => 
    array (
      'alias' => 'fk_modx_virtunewsletter_subscribers_has_modx_virtunewslette_idx1',
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
    'Subscribers' => 
    array (
      'class' => 'vnewsSubscribers',
      'local' => 'subscriber_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Categories' => 
    array (
      'class' => 'vnewsCategories',
      'local' => 'category_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
