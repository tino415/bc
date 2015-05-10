<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\DbTestCase;
use Codeception\Specify;
use app\models\Document;
use tests\codeception\fixtures\DocumentFixture;
use tests\codeception\fixtures\TagFixture;
use tests\codeception\fixtures\similar\MapDocumentTagFixture;

class DocumentSimiliarTest extends DbTestCase {
    
    use Specify;

    public function fixtures() {
        return [
            'map' => MapDocumentTagFixture::className(),
        ];
    }

    public function testSimilar() {
        $this->loadFixtures($this->getFixtures());

        $results = [];

        $document_one = Document::findOne(1);
        $results[1] = $document_one->getSimiliar()->one()->id;

        $document_two = Document::findOne(2);
        $results[2] = $document_two->getSimiliar()->one()->id;

        $document_three = Document::findOne(3);
        $results[3] = $document_three->getSimiliar()->one()->id;

        $this->specify('Check if select right document',
        function() use($results) {
            expect('Shold return document 3', $results[1])->equals(2);
            expect('Result for document 2', $results[2])->equals(3);
            expect('Rsult fro document3', $results[3])->equals(2);
        });
    }
}
