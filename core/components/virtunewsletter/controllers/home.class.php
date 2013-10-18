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
 * @subpackage controller
 */
class VirtuNewsletterHomeManagerController extends VirtuNewsletterManagerController {

    public function process(array $scriptProperties = array()) {

    }

    public function getPageTitle() {
        return $this->modx->lexicon('virtunewsletter');
    }

    public function loadCustomCssJs() {
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'ux/CheckColumn.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.recurrences.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/window.newsletter.test.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.subscribers.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/combo.recurrence.range.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.categories.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/combo.usergroups.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.usergroups.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.newsletterconfiguration.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.reports.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/window.category.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.category.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.newslettercontent.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/combo.categories.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/tree.newsletters.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/window.newsletter.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.subscribers.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.newsletters.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.home.js');
        $this->addLastJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/sections/index.js');
    }

    public function getTemplateFile() {
        return $this->virtunewsletter->config['templatesPath'] . 'home.tpl';
    }

}