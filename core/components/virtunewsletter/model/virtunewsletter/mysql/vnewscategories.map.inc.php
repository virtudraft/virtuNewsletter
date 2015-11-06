<?php
$xpdo_meta_map['vnewsCategories']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'table' => 'categories',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'description' => NULL,
    'sort_index' => 0,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'sort_index' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
      'default' => 0,
    ),
  ),
  'composites' => 
  array (
    'CategoriesHasUsergroups' => 
    array (
      'class' => 'vnewsCategoriesHasUsergroups',
      'local' => 'id',
      'foreign' => 'category_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'NewslettersHasCategories' => 
    array (
      'class' => 'vnewsNewslettersHasCategories',
      'local' => 'id',
      'foreign' => 'category_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'SubscribersHasCategories' => 
    array (
      'class' => 'vnewsSubscribersHasCategories',
      'local' => 'id',
      'foreign' => 'category_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
