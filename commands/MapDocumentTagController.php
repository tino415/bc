<?php

namespace app\commans;

use Yii;
use yii\console\Controller;
use app\models\MapDocumentTag;

class MapsDocumentTagController extends Controller {

    public function actionWeight() {
        echo "Calculating weights\n";
        MapDocumentTag::calculateWeights();
        echo "................done\n";
        return 0;
    }

}
