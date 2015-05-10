<?php

namespace app\components;

use Yii;
use yii\console\Controller;

abstract class SMParserController extends Controller {

    protected $lowcaps = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G',
        'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S', 'T', 'U',
        'V', 'W', 'X', 'Y', 'Z'
    ];

    protected $capitals = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G',
        'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S', 'T', 'U',
        'V', 'W', 'X', 'Y', 'Z', '*', 'Ž',
        'Ť', 'Č'
    ];

    protected $base_link;

    protected $document = null;

    protected function escapeNodes($content, $x_path) {
        $content = str_replace("<3", "&lt;3", $content);
        @$this->document->loadHTML($content);
        $xpath = new \DOMXPath($this->document);
        return $xpath->query($x_path);
    }

    protected function generateLinks(array $capitals) {
        $urls = [];
        foreach($capitals as $capital) {
            foreach($this->lowcaps as $lowcap) {
                $url = $this->base_link.$capital;
                $url .= ($lowcap) ? $lowcap : '';
                $url .= ($lowcap) ? $lowcap : '';
                $urls[] = $url;
            }
        }
        return $urls;
    }
}
