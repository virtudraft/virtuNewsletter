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
 * Validates before action.
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
                $modx->log(modX::LOG_LEVEL_WARN, 'validator xPDOTransport::ACTION_INSTALL');
                $modelPath = $modx->getOption('core_path') . 'components/virtunewsletter/model/';
                if ($modx->addPackage('virtunewsletter', $modelPath, $modx->config[modX::OPT_TABLE_PREFIX] . 'virtunewsletter_')) {
                    $modx->log(modX::LOG_LEVEL_WARN, 'package was added in validator xPDOTransport::ACTION_INSTALL');
                }
            }
            $tablePrefix = $modx->getOption('virtunewsletter.table_prefix', null, $modx->config[modX::OPT_TABLE_PREFIX] . 'virtunewsletter_');
            $modx->addExtensionPackage('virtunewsletter', '[[++core_path]]components/virtunewsletter/model/', array('tablePrefix' => $tablePrefix));
            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            if ($modx->getDebug()) {
                $modx->log(modX::LOG_LEVEL_WARN, 'validator xPDOTransport::ACTION_UNINSTALL');
            }
            if (!empty($options['delete_data'])) {
                $modelPath = $modx->getOption('core_path') . 'components/virtunewsletter/model/';
                if ($modx->addPackage('virtunewsletter', $modelPath, $modx->config[modX::OPT_TABLE_PREFIX] . 'virtunewsletter_')) {
                    if ($modx->getDebug()) {
                        $modx->log(modX::LOG_LEVEL_WARN, 'package was added in validator xPDOTransport::ACTION_UNINSTALL');
                    }
                    $manager = $modx->getManager();
                    $manager->removeObjectContainer('vnewsCategories');
                    $manager->removeObjectContainer('vnewsCategoriesHasUsergroups');
                    $manager->removeObjectContainer('vnewsNewsletters');
                    $manager->removeObjectContainer('vnewsNewslettersHasCategories');
                    $manager->removeObjectContainer('vnewsReports');
                    $manager->removeObjectContainer('vnewsSubscribers');
                    $manager->removeObjectContainer('vnewsSubscribersHasCategories');
                }
            }

            $tablePrefix = $modx->getOption('virtunewsletter.table_prefix', null, $modx->config[modX::OPT_TABLE_PREFIX] . 'virtunewsletter_');
            $modx->removeExtensionPackage('virtunewsletter');
            break;
    }
}
return true;
