<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;

class TestController extends Controller {

    public function actionTest() {
        $tags = User::findOne(2)->longTermTAgs;
        foreach($tags as $tag) {
            echo "<p>$tag</p>";
        }
    }
}
