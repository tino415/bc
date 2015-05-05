<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\MapDocumentTag;

class MapdocumenttagController extends Controller {

    public function actionWeight() {
        echo "Calculating weights\n";
        MapDocumentTag::calculateWeights();
        echo "................done\n";
        return 0;
    }

}
