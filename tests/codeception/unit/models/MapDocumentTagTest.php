<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\TestCase;
use Codeception\Specify;
use \app\models\MapDocumentTag;
use tests\codeception\fixtures\MapDocumentTagFixture;

class MapDocumentTagTest extends TestCase {

    use Specify;

    public function testWeighting() {
        MapDocumentTag::calculateWeights();

        $map = MapDocumentTag::findOne(1);

        $this->specify('Check weights', function() use($map) {
            expect('Weight shoul by 0.25', round($map->weight, 3))->equals(0.259);
        });
    }
}
