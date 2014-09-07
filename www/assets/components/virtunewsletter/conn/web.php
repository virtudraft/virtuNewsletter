<?php

/**
 * Ajax Connector
 *
 * @package virtunewsletter
 */
$validActions = array(
    'web/newsletters/read',
    'web/crons/queues/set',
    'web/crons/queues/process',
);
if (PHP_SAPI === "cli") {
    $args = $_SERVER['argv'];
    unset($args[0]);
    foreach ($args as $arg) {
        $params = @explode('=', $arg);
        $_REQUEST[$params[0]] = isset($params[1]) ? $params[1] : '';
    }
}
if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $validActions)) {
    @session_cache_limiter('public');
    define('MODX_REQP', false);
}

define('MODX_API_MODE', true);
// this goes to the www.domain.name/index.php
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

$virtunewsletterCorePath = $modx->getOption('virtunewsletter.core_path', null, $modx->getOption('core_path') . 'components/virtunewsletter/');
require_once $virtunewsletterCorePath . 'model/virtunewsletter.class.php';
$modx->virtunewsletter = new VirtuNewsletter($modx);

$modx->lexicon->load('virtunewsletter:web');

if (in_array($_REQUEST['action'], $validActions)) {
    $version = $modx->getVersionData();
    if (version_compare($version['full_version'], '2.1.1-pl') >= 0) {
        if ($modx->user->hasSessionContext($modx->context->get('key'))) {
            $_SERVER['HTTP_MODAUTH'] = $_SESSION["modx.{$modx->context->get('key')}.user.token"];
        } else {
            $_SESSION["modx.{$modx->context->get('key')}.user.token"] = 0;
            $_SERVER['HTTP_MODAUTH'] = 0;
        }
    } else {
        $_SERVER['HTTP_MODAUTH'] = $modx->site_id;
    }
    $_REQUEST['HTTP_MODAUTH'] = $_SERVER['HTTP_MODAUTH'];
}

// try this
// echo $modx->user->get('id');

/* handle request */
$connectorRequestClass = $modx->getOption('modConnectorRequest.class', null, 'modConnectorRequest');
$modx->config['modRequest.class'] = $connectorRequestClass;
$path = $modx->getOption('processorsPath', $modx->virtunewsletter->config, $virtunewsletterCorePath . 'processors/');
$modx->getRequest();
$modx->request->sanitizeRequest();
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));