<?php

namespace tests\codecetion\unit\crawlers;

use Yii;
use yii\codeception\DbTestCase;
use Codeception\Specify;
use app\crawlers\InterpretExplorerCrawler;
use app\models\Interpret;
use tests\codeception\fixtures\InterpretFixture;

class InterpretExplorerCrawlerTest extends DbTestCase {

    use Specify;

    public function fixtures() {
        return [
            'interprets' => InterpretFixture::className(),
        ];
    }

    public function testRun() {
        $this->loadFixtures($this->getFixtures());

        $crawler = new InterpretExplorerCrawler();
        $crawler->run(
            Yii::getAlias('@tests').'/codeception/data/interpreti.html');
        $this->specify('There should by som interprets in database',
        function() {
            expect('Interpret', Interpret::find()->where(['name' => 'Baloji'])
                ->exists())->true();
        });
    }
}
