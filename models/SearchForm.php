<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class SearchForm extends Model
{
    public $phrase;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [];
    }
}
