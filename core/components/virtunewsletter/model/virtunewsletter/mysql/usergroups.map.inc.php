<?php
$xpdo_meta_map['Usergroups']= array (
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
    'CategoriesHasUsergroups' => 
    array (
      'class' => 'CategoriesHasUsergroups',
      'local' => 'id',
      'foreign' => 'usergroup_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
