<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2023 by goldsky <goldsky@virtudraft.com>
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
 * Resolve creating db tables
 *
 * @package virtunewsletter
 * @subpackage build
 */
$php_ver_comp = version_compare(phpversion(), '5.3.0');
if ($php_ver_comp < 0) {
    return '<h1>FATAL ERROR: Setup cannot continue.</h1><p>Wrong PHP version! You\'re using PHP version ' . phpversion() . ', and virtuNewsletter requires version 5.3.0 or higher.</p>';
}

if ($modx = & $object->xpdo) {
    $c = $modx->newQuery('transport.modTransportPackage');
    $c->where(array(
        'workspace' => 1,
        "(SELECT
            `signature`
          FROM {$modx->getTableName('modTransportPackage')} AS `latestPackage`
          WHERE `latestPackage`.`package_name` = `modTransportPackage`.`package_name`
          ORDER BY
             `latestPackage`.`version_major` DESC,
             `latestPackage`.`version_minor` DESC,
             `latestPackage`.`version_patch` DESC,
             IF(`release` = '' OR `release` = 'ga' OR `release` = 'pl','z',`release`) DESC,
             `latestPackage`.`release_index` DESC
          LIMIT 1,1) = `modTransportPackage`.`signature`",
    ));
    $c->where(array(
        'modTransportPackage.signature:LIKE' => '%virtunewsletter%',
        'OR:modTransportPackage.package_name:LIKE' => '%virtunewsletter%',
        'installed:IS NOT' => null
    ));
    $oldPackage = $modx->getObject('transport.modTransportPackage', $c);

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            if ($modx->getDebug()) {
                $modx->log(modX::LOG_LEVEL_WARN, 'resolver xPDOTransport::ACTION_INSTALL');
            }
            $modelPath = $modx->getOption('core_path') . 'components/virtunewsletter/model/';
            if ($modx->addPackage('virtunewsletter', $modelPath, $modx->config[modX::OPT_TABLE_PREFIX] . 'virtunewsletter_')) {
                if ($modx->getDebug()) {
                    $modx->log(modX::LOG_LEVEL_WARN, 'package was added in resolver xPDOTransport::ACTION_INSTALL');
                }
                $manager = $modx->getManager();
                $manager->createObjectContainer('vnewsCategories');
                $manager->createObjectContainer('vnewsCategoriesHasUsergroups');
                $manager->createObjectContainer('vnewsNewsletters');
                $manager->createObjectContainer('vnewsNewslettersHasCategories');
                $manager->createObjectContainer('vnewsReports');
                $manager->createObjectContainer('vnewsSubscribers');
                $manager->createObjectContainer('vnewsSubscribersHasCategories');
                $manager->createObjectContainer('vnewsTemplates');
            }
            $menu = $modx->getObject('modMenu', array (
                'text' => 'virtunewsletter',
            ));
            if ($menu && ($menu->get('action') !== 'index' || $menu->get('namespace') !== 'virtunewsletter')) {
                $menu->set('action', 'index');
                $menu->set('namespace', 'virtunewsletter');
                $menu->save();
            }

            break;
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('core_path') . 'components/virtunewsletter/model/';
            if ($modx->addPackage('virtunewsletter', $modelPath, $modx->config[modX::OPT_TABLE_PREFIX] . 'virtunewsletter_')) {
                if ($modx->getDebug()) {
                    $modx->log(modX::LOG_LEVEL_WARN, 'package was added in resolver xPDOTransport::ACTION_UPGRADE');
                }
                $manager = $modx->getManager();

                if ($oldPackage) {

                    if ($oldPackage->compareVersion('1.6.0-beta1', '>')) {
                        $manager->addIndex('vnewsCategoriesHasUsergroups', 'fk_modx_virtunewsletter_categories_has_modx_virtunewsletter_idx');
                        $manager->addIndex('vnewsCategoriesHasUsergroups', 'fk_modx_virtunewsletter_categories_has_modx_virtunewsletter_idx1');

                        $manager->addField('vnewsNewsletters', 'parent_id', array('after' => 'id'));
                        $manager->addIndex('vnewsNewsletters', 'parent_id');

                        $manager->addIndex('vnewsNewslettersHasCategories', 'fk_modx_virtunewsletter_newsletters_has_modx_virtunewslette_idx');
                        $manager->addIndex('vnewsNewslettersHasCategories', 'fk_modx_virtunewsletter_newsletters_has_modx_virtunewslette_idx1');

                        // managing existing recurrences
                        $defaultVirtuNewsletterCorePath = $modx->getOption('core_path') . 'components/virtunewsletter/';
                        $virtuNewsletterCorePath = $modx->getOption('virtunewsletter.core_path', null, $defaultVirtuNewsletterCorePath);
                        $virtuNewsletter = $modx->getService('virtunewsletter', 'VirtuNewsletter', $virtuNewsletterCorePath . 'model/');
                        if ($virtuNewsletter instanceof VirtuNewsletter) {
                            $newsletters = $modx->getCollection('vnewsNewsletters', array(
                                'is_recurring' => 1,
                                'is_active' => 1
                            ));
                            if ($newsletters) {
                                foreach ($newsletters as $newsletter) {
                                    $virtuNewsletter->createNextRecurrence($newsletter->get('id'));
                                }
                            }
                        }

                        $manager->removeField('vnewsReports', 'current_occurrence_time');
                        $manager->removeField('vnewsReports', 'next_occurrence_time');
                        $manager->addIndex('vnewsReports', 'fk_modx_virtunewsletter_reports_modx_virtunewsletter_newsle_idx');
                        $manager->addIndex('vnewsReports', 'fk_modx_virtunewsletter_reports_modx_virtunewsletter_subscr_idx');

                        $manager->removeIndex('vnewsSubscribers', 'user_id');
                        $manager->addIndex('vnewsSubscribers', 'fk_modx_virtunewsletter_subscribers_modx_virtunewsletter_us_idx');

                        $manager->addIndex('vnewsSubscribersHasCategories', 'fk_modx_virtunewsletter_subscribers_has_modx_virtunewslette_idx');
                        $manager->addIndex('vnewsSubscribersHasCategories', 'fk_modx_virtunewsletter_subscribers_has_modx_virtunewslette_idx1');
                    }
                    if ($oldPackage->compareVersion('1.6.0-beta2', '>')) {
                        $manager->alterField('vnewsNewsletters', 'scheduled_for');
                        $manager->createObjectContainer('vnewsTemplates');
                    }
                    if ($oldPackage->compareVersion('2.0.0-beta1', '>')) {
                        $menu = $modx->getObject('modMenu', array (
                            'text' => 'virtunewsletter',
                        ));
                        if ($menu && ($menu->get('action') !== 'index' || $menu->get('namespace') !== 'virtunewsletter')) {
                            $menu->set('action', 'index');
                            $menu->set('namespace', 'virtunewsletter');
                            $menu->save();
                        }
//                        $manager->addField('vnewsCategoriesHasUsergroups', 'id', array('first' => true));
//                        $manager->addIndex('vnewsCategoriesHasUsergroups', 'id');
                        $modx->exec("ALTER TABLE {$modx->getTableName('vnewsCategoriesHasUsergroups')} DROP PRIMARY KEY;");
                        $modx->exec("ALTER TABLE {$modx->getTableName('vnewsCategoriesHasUsergroups')} ADD COLUMN `id` INT(10) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);");
                        $manager->alterField('vnewsCategoriesHasUsergroups', 'category_id');
                        $manager->alterField('vnewsCategoriesHasUsergroups', 'usergroup_id');

//                        $manager->addField('vnewsNewslettersHasCategories', 'id', array('first' => true));
//                        $manager->addIndex('vnewsNewslettersHasCategories', 'id');
                        $modx->exec("ALTER TABLE {$modx->getTableName('vnewsNewslettersHasCategories')} DROP PRIMARY KEY;");
                        $modx->exec("ALTER TABLE {$modx->getTableName('vnewsNewslettersHasCategories')} ADD COLUMN `id` INT(10) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);");
                        $manager->alterField('vnewsNewslettersHasCategories', 'newsletter_id');
                        $manager->alterField('vnewsNewslettersHasCategories', 'category_id');

//                        $manager->addField('vnewsReports', 'id', array('first' => true));
//                        $manager->addIndex('vnewsReports', 'id');
                        $modx->exec("ALTER TABLE {$modx->getTableName('vnewsReports')} DROP PRIMARY KEY;");
                        $modx->exec("ALTER TABLE {$modx->getTableName('vnewsReports')} ADD COLUMN `id` INT(10) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);");
                        $manager->alterField('vnewsReports', 'newsletter_id');
                        $manager->alterField('vnewsReports', 'subscriber_id');

//                        $manager->addField('vnewsSubscribersHasCategories', 'id', array('first' => true));
//                        $manager->addIndex('vnewsSubscribersHasCategories', 'id');
                        $modx->exec("ALTER TABLE {$modx->getTableName('vnewsSubscribersHasCategories')} DROP PRIMARY KEY;");
                        $modx->exec("ALTER TABLE {$modx->getTableName('vnewsSubscribersHasCategories')} ADD COLUMN `id` INT(10) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);");
                        $manager->alterField('vnewsSubscribersHasCategories', 'subscriber_id');
                        $manager->alterField('vnewsSubscribersHasCategories', 'category_id');
                        $manager->addField('vnewsSubscribersHasCategories', 'subscribed_on', array('after' => 'category_id'));
                        $manager->addField('vnewsSubscribersHasCategories', 'unsubscribed_on', array('after' => 'subscribed_on'));
                    }
                    if ($oldPackage->compareVersion('2.1.0-pl', '>')) {
                        $manager->addField('vnewsSubscribers', 'email_provider', array('after' => 'name'));
                    }
                    if ($oldPackage->compareVersion('2.4.0-pl', '>')) {
                        $manager->addField('vnewsNewsletters', 'stopped_at', array('after' => 'scheduled_for'));
                        $manager->addField('vnewsNewsletters', 'is_paused', array('after' => 'is_active'));
                    }
                    $multithreadSetting = $modx->getObject('modSystemSetting', array('key' => 'virtunewsletter.send_multithreaded '));
                    if ($multithreadSetting) {
                        $multithreadSetting->set('key', 'virtunewsletter.send_multithreaded');
                        $multithreadSetting->save();
                    }
                }
            }
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            if ($modx->getDebug()) {
                $modx->log(modX::LOG_LEVEL_WARN, 'resolver xPDOTransport::ACTION_UNINSTALL');
                $modelPath = $modx->getOption('core_path') . 'components/virtunewsletter/model/';
                if ($modx->addPackage('virtunewsletter', $modelPath, $modx->config[modX::OPT_TABLE_PREFIX] . 'virtunewsletter_')) {
                    $modx->log(modX::LOG_LEVEL_WARN, 'package was added in resolver xPDOTransport::ACTION_UNINSTALL');
                }
            }
            break;
    }
}

return true;
