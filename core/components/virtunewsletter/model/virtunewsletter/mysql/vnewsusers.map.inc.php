<?php
$xpdo_meta_map['vnewsUsers']= array (
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
    'vnewsSubscribers' => 
    array (
      'class' => 'vnewsSubscribers',
      'local' => 'id',
      'foreign' => 'user_id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
);
