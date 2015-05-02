<?php

namespace app\components;

use Yii;
use yii\console\Controller;

abstract class SMParserController extends Controller {

    protected $document = null;

    protected function escapeNodes($content, $x_path) {
        $content = str_replace("<3", "&lt;3", $content);
        @$this->document->loadHTML($content);
        $xpath = new \DOMXPath($this->document);
        return $xpath->query($x_path);
    }
}
