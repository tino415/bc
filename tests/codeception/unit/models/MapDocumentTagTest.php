<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\DbTestCase;
use Codeception\Specify;
use app\models\MapDocumentTag;
use tests\codeception\fixtures\MapDocumentTagFixture;
use tests\codeception\fixtures\DocumentFixture;

class MapDocumentTagTest extends DbTestCase {

    use Specify;

    public function fixtures() {
        return [
            'map_document_tags' => MapDocumentTagFixture::className(),
            'document' => DocumentFixture::className(),
        ];
    }

    public function testWeighting() {
        $this->loadFixtures($this->getFixtures());

        MapDocumentTag::calculateWeights();

        $map1 = MapDocumentTag::findOne(1);
        $map2 = MapDocumentTag::findOne(8);
        exit(print_r($map2));

        $this->specify('Check weights', function() use($map1, $map2) {
            expect('Weight should by 0.893', round($map1->weight, 3))->equals(0.893);
            expect('Weight should by 1.607', round($map2->weight, 3))->equals(1.607);
        });
    }

    public function testMultyWeighting() {
        MapDocumentTag::calculateWeights();
    }
}
