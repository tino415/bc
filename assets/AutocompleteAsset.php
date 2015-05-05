<?php

namespace app\assets;

use yii\web\AssetBundle;

class AutocompleteAsset extends AssetBundle {

    public $sourcePath = '@vendor/filsh/jquery-autocomplete/src/';

    public $js = [
        'jquery.autocomplete.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
