<?php
$xpdo_meta_map['Categories']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'table' => 'categories',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'description' => NULL,
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
  ),
  'composites' => 
  array (
    'CategoriesHasUsergroups' => 
    array (
      'class' => 'CategoriesHasUsergroups',
      'local' => 'id',
      'foreign' => 'category_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'NewslettersHasCategories' => 
    array (
      'class' => 'NewslettersHasCategories',
      'local' => 'id',
      'foreign' => 'category_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
