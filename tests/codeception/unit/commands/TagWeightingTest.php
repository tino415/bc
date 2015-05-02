<?php

namespace tests\codeception\unit\commands;

use Yii;
use yii\codeception\TestCase;
use Codeception\Specify;

class TagWeightingTest extends TestCase {

    use Specify;
    
    protected function setUp() {
        parent::setUp();
        $this->loadFixtures(['miny_documents']);
    }
}
