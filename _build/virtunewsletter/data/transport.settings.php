<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2016 by goldsky <goldsky@virtudraft.com>
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

//$settings['virtunewsletter.subscribe_confirmation_tpl'] = $modx->newObject('modSystemSetting');
//$settings['virtunewsletter.subscribe_confirmation_tpl']->fromArray(array(
//    'key' => 'virtunewsletter.subscribe_confirmation_tpl',
//    'value' => '',
//    'xtype' => 'textfield',
//    'namespace' => 'virtunewsletter',
//    'area' => 'Email',
//        ), '', true, true);
//
//$settings['virtunewsletter.unsubscribe_confirmation_tpl'] = $modx->newObject('modSystemSetting');
//$settings['virtunewsletter.unsubscribe_confirmation_tpl']->fromArray(array(
//    'key' => 'virtunewsletter.unsubscribe_confirmation_tpl',
//    'value' => '',
//    'xtype' => 'textfield',
//    'namespace' => 'virtunewsletter',
//    'area' => 'Email',
//        ), '', true, true);
//
//$settings['virtunewsletter.subscribe_succeeded_tpl'] = $modx->newObject('modSystemSetting');
//$settings['virtunewsletter.subscribe_succeeded_tpl']->fromArray(array(
//    'key' => 'virtunewsletter.subscribe_succeeded_tpl',
//    'value' => '',
//    'xtype' => 'textfield',
//    'namespace' => 'virtunewsletter',
//    'area' => 'Email',
//        ), '', true, true);
//
//$settings['virtunewsletter.unsubscribe_succeeded_tpl'] = $modx->newObject('modSystemSetting');
//$settings['virtunewsletter.unsubscribe_succeeded_tpl']->fromArray(array(
//    'key' => 'virtunewsletter.unsubscribe_succeeded_tpl',
//    'value' => '',
//    'xtype' => 'textfield',
//    'namespace' => 'virtunewsletter',
//    'area' => 'Email',
//        ), '', true, true);

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

$settings['virtunewsletter.email_provider'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.email_provider']->fromArray(array(
    'key' => 'virtunewsletter.email_provider',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.mandrill.api_key'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.mandrill.api_key']->fromArray(array(
    'key' => 'virtunewsletter.mandrill.api_key',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.mailgun.api_key'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.mailgun.api_key']->fromArray(array(
    'key' => 'virtunewsletter.mailgun.api_key',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.mailgun.endpoint'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.mailgun.endpoint']->fromArray(array(
    'key' => 'virtunewsletter.mailgun.endpoint',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.email_from_name'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.email_from_name']->fromArray(array(
    'key' => 'virtunewsletter.email_from_name',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.email_reply_to'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.email_reply_to']->fromArray(array(
    'key' => 'virtunewsletter.email_reply_to',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.email_bcc_address'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.email_bcc_address']->fromArray(array(
    'key' => 'virtunewsletter.email_bcc_address',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.sync_include_inactive_users'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.sync_include_inactive_users']->fromArray(array(
    'key' => 'virtunewsletter.sync_include_inactive_users',
    'value' => 1,
    'xtype' => 'combo-boolean',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.sync_default_activation'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.sync_default_activation']->fromArray(array(
    'key' => 'virtunewsletter.sync_default_activation',
    'value' => 0,
    'xtype' => 'numberfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Email',
        ), '', true, true);

$settings['virtunewsletter.cronreport.enabled'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.cronreport.enabled']->fromArray(array(
    'key' => 'virtunewsletter.cronreport.enabled',
    'value' => 0,
    'xtype' => 'combo-boolean',
    'namespace' => 'virtunewsletter',
    'area' => 'Cron Job',
        ), '', true, true);

$settings['virtunewsletter.cronreport.itemTpl'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.cronreport.itemTpl']->fromArray(array(
    'key' => 'virtunewsletter.cronreport.itemTpl',
    'value' => 'cronreport.item',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Cron Job',
        ), '', true, true);

$settings['virtunewsletter.cronreport.wrapperTpl'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.cronreport.wrapperTpl']->fromArray(array(
    'key' => 'virtunewsletter.cronreport.wrapperTpl',
    'value' => 'cronreport.wrapper',
    'xtype' => 'textfield',
    'namespace' => 'virtunewsletter',
    'area' => 'Cron Job',
        ), '', true, true);

$settings['virtunewsletter.cronreport.getItems'] = $modx->newObject('modSystemSetting');
$settings['virtunewsletter.cronreport.getItems']->fromArray(array(
    'key' => 'virtunewsletter.cronreport.getItems',
    'value' => 0,
    'xtype' => 'combo-boolean',
    'namespace' => 'virtunewsletter',
    'area' => 'Cron Job',
        ), '', true, true);

return $settings;
