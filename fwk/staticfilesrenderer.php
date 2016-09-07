<?php
namespace fwk;

/**
* This class is used to deliver static files while keeping the structure of the fwk
* @author Pablo Bossi
*/
class StaticFilesRenderer extends Controller
{
  public function render($params) {
    try {
      $fileName = strtok($params['viewFile'], '?');
      $file = new \finfo(FILEINFO_MIME);
      $mimeType = $file->file($fileName);
      $this->response->setResponseCode("200");
      $this->response->setHeader("Content-Type", $mimeType);
      $this->response->setBody(file_get_contents($fileName));
    } catch (Exception $ex) {
      $this->response->setResponseCode("404");
      $this->response->setHeader("Content-Type", "text/html; charset=utf-8");
      $this->response->setBody('Requested file ['.strtok($params['viewFile'], '?').'] does not exist. Referer: '.$_SERVER['HTTP_REFERER']);
    }
  }
}
