<?php

namespace app\widgets\models;

use Yii;
use yii\base\model;

/**
 * From for user registration
 */
class UserRegisterForm extends Model {

    public $username;
    public $email;
    public $password;
    public $password_repeat;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password', 'password_repeat', 'email'], 'required'],
            
            [['username', 'email'], 'filter', 'filter' => 'trim'],

            [['username', 'email'], 'unique', 
                'targetClass' => 'app\models\User', 
                'message' => 'Login already taken'
            ],

            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'email'],

            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password']
        ];
    }

    public function attributeLabels() {
        return [
            'username'        => Yii::t('app', 'User Name'),
            'password'        => Yii::t('app', 'Password'),
            'email'           => Yii::t('app', 'Email'),
            'password_repeat' => Yii::t('app', 'Password Again'),
        ];
    }

    /**
     * Register user from model
     */
    public function register() {
        if($this->validate()) {
            $user = new User;
            $user->username = $this->username;
            $user->email = $this->email;
            $user->password = $this->password;
            $user->role_id = 2;
            $user->save();
            return Yii::$app->user->login($user, 0);
        } else return false;
    }
}
