<?php

namespace app\models;


use Yii;
use Yii\base\Model;

class UserMergeRecomendationForm extends Model {

    public $users;

    public function rules() {
        return [
            [['users'], 'safe'],
            ['users', 'validateUsers'],
        ];
    }

    public function validateUsers() {
        foreach($this->users as $user_id) {
            if(!User::find($user_id)->exists())
                return false;
        }
        return true;
    }

    public function attributeLabels() {
        return [
            'users' => 'Users',
        ];
    }
}
