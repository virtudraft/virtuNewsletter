<?php
$xpdo_meta_map['NewslettersHasCategories']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'table' => 'newsletters_has_categories',
  'extends' => 'xPDOObject',
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
      'index' => 'pk',
    ),
    'category_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'newsletter_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'category_id' => 
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
      'class' => 'Newsletters',
      'local' => 'newsletter_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Categories' => 
    array (
      'class' => 'Categories',
      'local' => 'category_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
