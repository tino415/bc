<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\DbTestCase;
use Codeception\Specify;
use app\models\MapDocumentTag;
use tests\codeception\fixtures\MapDocumentTagFixture;

class MapDocumentTagTest extends DbTestCase {

    use Specify;

    public function fixtures() {
        return [
            'map_document_tags' => MapDocumentTagFixture::className(),
        ];
    }

    public function testWeighting() {
        MapDocumentTag::calculateWeights();

        $map1 = MapDocumentTag::findOne(1);
        $map2 = MapDocumentTag::findOne(8);


        $this->specify('Check weights', function() use($map1, $map2) {
            expect('Weight should by 0.446', round($map1->weight, 3))->equals(0.446);
            expect('Weight should by 1.696', round($map2->weight, 3))->equals(1.696);
        });
    }

    public function testMultyWeighting() {
        MapDocumentTag::calculateWeights();
    }
}
