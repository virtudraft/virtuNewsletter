<?php
$xpdo_meta_map['vnewsUsergroups']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'extends' => 'modUserGroup',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'composites' => 
  array (
    'vnewsCategoriesHasUsergroups' => 
    array (
      'class' => 'vnewsCategoriesHasUsergroups',
      'local' => 'id',
      'foreign' => 'usergroup_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
