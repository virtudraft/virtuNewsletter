<?php

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('virtunewsletter.core_path', null, $modx->getOption('core_path') . 'components/virtunewsletter/');
require_once $corePath . 'model/virtunewsletter.class.php';
$modx->virtunewsletter = new VirtuNewsletter($modx);

$modx->lexicon->load('virtunewsletter:cmp');

/* handle request */
$path = $modx->getOption('processorsPath', $modx->virtunewsletter->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));