<?php

class VirtuNewsletterHomeManagerController extends VirtuNewsletterManagerController {

    public function process(array $scriptProperties = array()) {

    }

    public function getPageTitle() {
        return $this->modx->lexicon('virtunewsletter');
    }

    public function loadCustomCssJs() {
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'ux/CheckColumn.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/grid.subscribers.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/combo.recurrenceunit.js');
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
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.newsletter.js');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/widgets/panel.home.js');
        $this->addLastJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/sections/index.js');
    }

    public function getTemplateFile() {
        return $this->virtunewsletter->config['templatesPath'] . 'home.tpl';
    }

}