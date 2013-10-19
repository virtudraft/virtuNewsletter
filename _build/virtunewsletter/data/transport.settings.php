<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013 by goldsky <goldsky@virtudraft.com>
 *
 * This file is part of virtuNewsletter, a newsletter system for MODX
 * Revolution.
 *
 * virtuNewsletter is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation version 3,
 *
 * virtuNewsletter is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * virtuNewsletter; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * virtuNewsletter build script
 *
 * @package virtunewsletter
 * @subpackage build
 */
$settings['virtunewsletter.core_path'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.core_path']->fromArray(array(
    'key' => 'virtunewsletter.core_path',
    'value' => '{core_path}components/virtunewsletter/',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'URL',
        ), '', true, true);

$settings['virtunewsletter.assets_url'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.assets_url']->fromArray(array(
    'key' => 'virtunewsletter.assets_url',
    'value' => '{assets_url}components/virtunewsletter/',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'URL',
        ), '', true, true);

$settings['virtunewsletter.email_debug'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.email_debug']->fromArray(array(
    'key' => 'virtunewsletter.email_debug',
    'value' => 0,
    'xtype' => 'combo-boolean',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.email_limit'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.email_limit']->fromArray(array(
    'key' => 'virtunewsletter.email_limit',
    'value' => 50,
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.email_prefix'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.email_prefix']->fromArray(array(
    'key' => 'virtunewsletter.email_prefix',
    'value' => 'virtuNewsletter.email.',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.email_sender'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.email_sender']->fromArray(array(
    'key' => 'virtunewsletter.email_sender',
    'value' => 'no-reply@example.com',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.readerpage'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.readerpage']->fromArray(array(
    'key' => 'virtunewsletter.readerpage',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'URL',
        ), '', true, true);

$settings['virtunewsletter.subscribe_confirmation_tpl'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.subscribe_confirmation_tpl']->fromArray(array(
    'key' => 'virtunewsletter.subscribe_confirmation_tpl',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.unsubscribe_confirmation_tpl'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.unsubscribe_confirmation_tpl']->fromArray(array(
    'key' => 'virtunewsletter.unsubscribe_confirmation_tpl',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.subscribe_succeeded_tpl'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.subscribe_succeeded_tpl']->fromArray(array(
    'key' => 'virtunewsletter.subscribe_succeeded_tpl',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.unsubscribe_succeeded_tpl'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.unsubscribe_succeeded_tpl']->fromArray(array(
    'key' => 'virtunewsletter.unsubscribe_succeeded_tpl',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.usergroups'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.usergroups']->fromArray(array(
    'key' => 'virtunewsletter.usergroups',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Access',
        ), '', true, true);

$settings['virtunewsletter.use_csstoinlinestyles'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.use_csstoinlinestyles']->fromArray(array(
    'key' => 'virtunewsletter.use_csstoinlinestyles',
    'value' => 0,
    'xtype' => 'combo-boolean',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$extensionPackages = $modx->getObject('modSystemSetting', array(
    'key' => 'extension_packages'
        ));
if ($extensionPackages) {
    $value = $extensionPackages->get('value');
    $valueArray = json_decode($value, TRUE);
    if (!isset($valueArray['virtunewsletter'])) {
        $valueArray['virtunewsletter'] = array(
            'path' => '[[++core_path]]components/virtunewsletter/model/'
        );
        $value = json_encode($valueArray);
        $settings['extension_packages']->set('value', $value);
    }
} else {
    $valueArray = array(
        'virtunewsletter' => array(
            'path' => '[[++core_path]]components/virtunewsletter/model/'
        )
    );
    $value = json_encode($valueArray);
    $settings['extension_packages'] = $modx->newObject('modSystemSetting');
    $settings['extension_packages']->fromArray(array(
        'key' => 'extension_packages',
        'value' => $value,
        'xtype' => 'textfield',
        'namespace' => 'core',
        'area' => 'system',
            ), '', true, true);
}

return $settings;
