<?php

namespace tests\codeception\unit\commands;

use Yii;
use yii\codeception\TestCase;
use Codeception\Specify;
use app\models\Tag;
use app\models\Document;

class TaggingTest extends TestCase {

    use Specify;
    
    public function testTagGeneration() {
        $model = Document::findOne(1);
        
        $tags = $model->getTagsFromAtts();
        
        $this->specify('There should by certain tags', function() use ($tags) {
            expect('There should by 4 tags', count($tags))->equals(4);
            expect('Should contain one twice', $tags['one'])->equals(3);
            expect('Once interprets', $tags['interprets'])->equals(1);
            expect('Once interpret', $tags['interpret'])->equals(1);
        });
    }

    public function testTagging() {
    }
}
