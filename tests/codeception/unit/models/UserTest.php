<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\DbTestCase;
use app\models\User;
use Codeception\Specify;
use app\models\Tag;
use app\models\TagWeight;
use tests\codeception\fixtures\ViewFixture;
use yii\db\Expression;
use yii\db\Query;

class UserTest extends DbTestCase
{
    use Specify;

    public function fixtures() {
        return [
            ViewFixture::className()
        ];
    }

    public function testGroup() {
        $this->loadFixtures($this->getFixtures());
        $ids = [1,2];
        $tags = User::recommendFor($ids)->limit(2)->all();
        $this->specify('test', function() use($tags) {
            expect('Tag one', round($tags[0]['weight'], 3))->equals(0.477);
            expect('Tag two', round($tags[1]['weight'], 3))->equals(0.602);
        });
    }
}
