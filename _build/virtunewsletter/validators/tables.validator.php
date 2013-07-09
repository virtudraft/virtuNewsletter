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
 * Validates before action.
 *
 * @package virtunewsletter
 * @subpackage build
 */
if ($modx = & $object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            if ($modx->getDebug()) {
                $modx->log(modX::LOG_LEVEL_WARN, 'validator xPDOTransport::ACTION_INSTALL');
                $modelPath = $modx->getOption('core_path') . 'components/virtunewsletter/model/';
                if ($modx->addPackage('virtunewsletter', $modelPath, 'modx_virtunewsletter_')) {
                    $modx->log(modX::LOG_LEVEL_WARN, 'package was added in validator xPDOTransport::ACTION_INSTALL');
                }
            }
            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            if ($modx->getDebug()) {
                $modx->log(modX::LOG_LEVEL_WARN, 'validator xPDOTransport::ACTION_UNINSTALL');
            }
            $modelPath = $modx->getOption('core_path') . 'components/virtunewsletter/model/';
            if ($modx->addPackage('virtunewsletter', $modelPath, 'modx_virtunewsletter_')) {
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
            break;
    }
}
return true;