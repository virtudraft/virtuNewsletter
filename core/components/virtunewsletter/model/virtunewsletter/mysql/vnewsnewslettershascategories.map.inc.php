<?php
$xpdo_meta_map['vnewsNewslettersHasCategories']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'table' => 'newsletters_has_categories',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'newsletter_id' => NULL,
    'category_id' => NULL,
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
    'fk_modx_virtunewsletter_newsletters_has_modx_virtunewslette_idx' => 
    array (
      'alias' => 'fk_modx_virtunewsletter_newsletters_has_modx_virtunewslette_idx',
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
    'fk_modx_virtunewsletter_newsletters_has_modx_virtunewslette_idx1' => 
    array (
      'alias' => 'fk_modx_virtunewsletter_newsletters_has_modx_virtunewslette_idx1',
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
