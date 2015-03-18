<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Song extends Model
{
    public $name;
    public $body;

    public function rulse()
    {
        return [[['name', 'body'], 'required']];
    }
}
