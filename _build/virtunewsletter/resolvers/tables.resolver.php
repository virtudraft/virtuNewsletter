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
 * Resolve creating db tables
 *
 * @package virtunewsletter
 * @subpackage build
 */
if ($modx = & $object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $php_ver_comp = version_compare(phpversion(), '5.3.0');
            if ($php_ver_comp < 0) {
                return '<h1>FATAL ERROR: Setup cannot continue.</h1><p>Wrong PHP version! You\'re using PHP version ' . phpversion() . ', and virtuNewsletter requires version 5.3.0 or higher.</p>';
            }
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
            }
            break;
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('core_path') . 'components/virtunewsletter/model/';
            if ($modx->addPackage('virtunewsletter', $modelPath, $modx->config[modX::OPT_TABLE_PREFIX] . 'virtunewsletter_')) {
                if ($modx->getDebug()) {
                    $modx->log(modX::LOG_LEVEL_WARN, 'package was added in resolver xPDOTransport::ACTION_UPGRADE');
                }
                $manager = $modx->getManager();

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
