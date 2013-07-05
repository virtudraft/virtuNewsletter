<?php

if (!isset($_GET['site_id'])) {
    die();
}
if ($_GET['site_id'] !== $modx->site_id) {
    die();
}

$modx->virtunewsletter->setQueues();