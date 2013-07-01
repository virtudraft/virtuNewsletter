<?php

$newsId = $modx->getOption('newsid', $scriptProperties);
if (intval($newsId) < 1) {
    return;
}

$newsletter = $this->modx->virtunewsletter->getNewsletter($newsId);

return $newsletter['content'];