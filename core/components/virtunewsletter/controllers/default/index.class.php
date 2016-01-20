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
 * @subpackage controller
 */
require_once dirname(dirname(dirname(__FILE__))) . '/model/virtunewsletter.class.php';

class VirtunewsletterIndexManagerController extends modExtraManagerController {

    /** @var VirtuNewsletter $virtunewsletter */
    public $virtunewsletter;

    public function initialize() {
        $this->virtunewsletter = new VirtuNewsletter($this->modx);
        $version = str_replace(' ', '', $this->virtunewsletter->config['version']);
        $isCssCompressed = $this->modx->getOption('compress_css');
        $withVersion = $isCssCompressed? '' : '?v=' . $version;
        $this->addCss($this->virtunewsletter->config['cssUrl'] . 'mgr.css' . $withVersion);
        $isJsCompressed = $this->modx->getOption('compress_js');
        $withVersion = $isJsCompressed? '' : '?v=' . $version;
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/virtunewsletter.js' . $withVersion);
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            VirtuNewsletter.config = ' . $this->modx->toJSON($this->virtunewsletter->config) . ';
        });
        </script>');
        return parent::initialize();
    }

    public function getLanguageTopics() {
        return array('virtunewsletter:cmp');
    }

    public function checkPermissions() {
        return true;
    }

    public function process(array $scriptProperties = array()) {

    }

    public function getPageTitle() {
        return $this->modx->lexicon('virtunewsletter');
    }

    public function loadCustomCssJs() {
        $version = str_replace(' ', '', $this->virtunewsletter->config['version']);
        $isJsCompressed = $this->modx->getOption('compress_js');
        $withVersion = $isJsCompressed? '' : '?v=' . $version;
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/combo.resources.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.templates.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'ux/CheckColumn.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'ux/fileuploadfield/FileUploadField.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/window.importcsv.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/window.updatecategory.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.subscribers.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.recurrences.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/window.newsletter.test.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/combo.recurrence.range.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.categories.local.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.categories.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/combo.usergroups.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.usergroups.local.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.newsletterconfiguration.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.reports.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.category.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.newslettercontent.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/combo.categories.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.subscribers.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.newsletters.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.newsletters.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.dashboardsubscribers.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.dashboardnewsletters.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.dashboard.js' . $withVersion);
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.home.js' . $withVersion);
        $this->addLastJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/sections/index.js' . $withVersion);
    }

    public function getTemplateFile() {
        return $this->virtunewsletter->config['templatesPath'] . 'index.tpl';
    }

}