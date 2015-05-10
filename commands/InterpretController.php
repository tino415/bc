<?php

namespace app\commands;

use Yii;
use app\components\SMParserController;
use app\models\Interpret;
use app\crawlers\InterpretExplorerCrawler;

define(
    'INTERPRET_XPATH',
    '//td[a/@class="interpretzoznam"]/a'
);

class InterpretController extends SMParserController {

    protected $base_link = 'http://www.supermusic.sk/skupiny.php?od=';
    
    public function actionExploreone() {
        $links = $this->generateLinks(['A']);
        $crawler = new InterpretExplorerCrawler();
        $crawler->run($links[0]);
    }

    public function actionExplore(
        $capitals = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,*,Ž,Ť,Č'
    )
    {
        $capitals = explode(',', $capitals);

        echo "Prepareing crawler\n";
        $crawler = new InterpretExplorerCrawler;
        $crawler->prepare();

        echo "Generating links\n";
        $urls = $this->generateLinks($capitals);
        $crawler->runMultiple($urls, false);

        return 0;
    }
}
