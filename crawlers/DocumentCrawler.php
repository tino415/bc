<?php

namespace app\crawlers\supermusic;

use app\models\Document;
use app\components\Crawler;
use app\models\Schema;

class DocumentCrawler {

    private $dom;

    private $xpath;

    private $XPATHS = [
        'content' => '//td[@class="piesen"]/text()',
        'chords' => '///td[@class="piesen"]//a[@class="sup"]',
    ];

    private $baseUrl = 
        'http://www.supermusic.sk/skupina.php?action=piesen&idpiesne=';

    private $chordBaseUrl =
        'http://www.supermusic.sk/akord.php?akord=';

    public function __construct() {
        $this->dom = new \DOMDocument;
    }

    public function getUrl($document) {
        return "$this->baseUrl$document->id";
    }


    private function parseContent() {
        return $this->xpath->query($this->XPATHS['content'])->wholeText;
    }

    private function parseChords($document) {
        foreach($this->xpath->query($this->XPATHS['chords']) as $chordLink) {
            $schema = new Schema();
            $schema->content = $chordLink->textContent;
            $schema->document_id = $document->id;
            if($schema->validate()) $schema->save();

            $chordLink
                ->setAttribute( 'href', "$this->chordBaseUrl$chordLink->textContent");
            $chordLink->setAttribute('target', '_blank');
            $chordLink->setAttribute('class', 'chord');
        }
    }

    public function runOne($document) {
        $this->dom->loadHTML($this->getUrl($document));
        $this->xpath = new \DOMXPath($this->dom);
        $this->parseChords($document);
        $document->content = $this->parseContent();
        $document->save();
    }

    public function runAll() {
        foreach(Document::find()->where(['content' => null])->each() as $document) {
            $this->runOne($document);
        }
    }
}
