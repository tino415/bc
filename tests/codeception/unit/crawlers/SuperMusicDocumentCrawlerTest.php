<?php

namespace tests\codeception\unit\crawlers;

use Yii;
use yii\codeception\DbTestCase;
use yii\db\Query;
use Codeception\Specify;
use app\models\Document;
use app\models\Interpret;
use tests\codeception\fixtures\InterpretFixture;
use tests\codeception\fixtures\DocumentTypeFixture;
use app\crawlers\DocumentExplorerCrawler;

class SuperMusicDocumentCrawlerTest extends DbTestCase {

    use Specify;

    public function fixtures() {
        return [
            'interprets' => InterpretFixture::className(),
            'document_types' => DocumentTypeFixture::className(),
        ];
    }

    public function testRun() {
        $this->loadFixtures($this->getFixtures());
        Yii::$app->db->createCommand()->truncateTable('document');
        
        $crawler = new DocumentExplorerCrawler();
        $crawler->run(Yii::getAlias('@tests').'/codeception/data/piesne.html');

        $glen_hansard = Interpret::findOne(20196);

        $this->specify('There should by new documents in database',
        function() use($glen_hansard) {
            expect('Two docs on page for Glen Hansard', 
                $glen_hansard->getDocuments()->count())->equals(2);
        });
    }
}
