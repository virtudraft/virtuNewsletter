<?php
$xpdo_meta_map['vnewsCategoriesHasUsergroups']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'table' => 'categories_has_usergroups',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'category_id' => NULL,
    'usergroup_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'category_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'usergroup_id' => 
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
    'fk_modx_virtunewsletter_categories_has_modx_virtunewsletter_idx' => 
    array (
      'alias' => 'fk_modx_virtunewsletter_categories_has_modx_virtunewsletter_idx',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'usergroup_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'fk_modx_virtunewsletter_categories_has_modx_virtunewsletter_idx1' => 
    array (
      'alias' => 'fk_modx_virtunewsletter_categories_has_modx_virtunewsletter_idx1',
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
  ),
  'aggregates' => 
  array (
    'Categories' => 
    array (
      'class' => 'vnewsCategories',
      'local' => 'category_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Usergroups' => 
    array (
      'class' => 'vnewsUsergroups',
      'local' => 'usergroup_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
