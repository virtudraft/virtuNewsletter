<?php
$xpdo_meta_map['Users']= array (
  'package' => 'virtunewsletter',
  'version' => '1.1',
  'extends' => 'modUser',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'composites' => 
  array (
    'Subscribers' => 
    array (
      'class' => 'Subscribers',
      'local' => 'id',
      'foreign' => 'user_id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
);
