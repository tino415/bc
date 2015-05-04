<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\TestCase;
use Codeception\Specify;
use app\models\MapDocumentTag;
use tests\codeception\fixtures\MapDocumentTagFixture;

class MapDocumentTagTest extends TestCase {

    use Specify;

    protected function setUp() {

        //$fixs = $this->createFixtures(['MapDocumentTagFixture']);
        //$this->loadFixtures($fixs);

        $fixs = $this->createFixtures(['tests\codeception\fixtures\MapDocumentTagFixture']);
        $this->loadFixtures($fixs);
        //$this->loadFixtures(['tests\codeception\fixtures\MapDocumentTagFixture']);
        //$this->loadFixtures(['MapDocumentTagFixture']);
    }

    public function testWeighting() {
        //$fixs = $this->createFixtures(['MapDocumentTagFixture']);
        //$this->loadFixtures($fixs);

        //$fixs = $this->createFixtures(['tests\codeception\fixtures\MapDocumentTagFixture']);
        //$this->loadFixtures($fixs);
        //$this->loadFixtures(['tests\codeception\fixtures\MapDocumentTagFixture']);
        //$this->loadFixtures(['MapDocumentTagFixture']);

        MapDocumentTag::calculateWeights();

        $map = MapDocumentTag::findOne(1);

        $this->specify('Check weights', function() use($map) {
            expect('Weight shoul by 0.25', round($map->weight, 3))->equals(0.259);
        });
    }
}
