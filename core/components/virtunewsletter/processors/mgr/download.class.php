<?php

class VirtuNewsletterFileDownloadProcessor extends modProcessor
{

    public function checkPermissions()
    {
        return $this->modx->hasPermission('file_view');
    }

    public function getLanguageTopics()
    {
        return array('file');
    }

    public function process()
    {
        $file = $this->getProperty('file', false);
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($file));
            readfile($file);
            exit;
        }

        return $this->success();
    }

}

return 'VirtuNewsletterFileDownloadProcessor';
