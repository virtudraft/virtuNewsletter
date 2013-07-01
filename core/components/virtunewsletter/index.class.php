<?php

require_once dirname(__FILE__) . '/model/virtunewsletter.class.php';

abstract class VirtuNewsletterManagerController extends modExtraManagerController {

    /** @var VirtuNewsletter $virtunewsletter */
    public $virtunewsletter;

    public function initialize() {
        $this->virtunewsletter = new VirtuNewsletter($this->modx);

        $this->addCss($this->virtunewsletter->config['cssUrl'] . 'mgr.css');
        $this->addJavascript($this->virtunewsletter->config['jsUrl'] . 'mgr/virtunewsletter.js');
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

}

class IndexManagerController extends VirtuNewsletterManagerController {

    public static function getDefaultController() {
        return 'home';
    }

}