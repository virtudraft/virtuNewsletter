<?php
$xpdo_meta_map['CategoriesHasUsergroups']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'table' => 'categories_has_usergroups',
  'extends' => 'xPDOObject',
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
      'index' => 'pk',
    ),
    'usergroup_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
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
        'category_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'usergroup_id' => 
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
      'class' => 'Categories',
      'local' => 'category_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Usergroups' => 
    array (
      'class' => 'Usergroups',
      'local' => 'usergroup_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
