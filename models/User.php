<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property integer $role_id
 * @property string $access_token
 * @property string $auth_key
 * @property string $username
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['access_level', 'role_id'], 'integer'],
            [['email', 'password'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['access_token', 'auth_key', 'username'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Password',
            'role_id' => 'Role ID',
            'access_token' => 'Access token',
            'auth_key' => 'Authentisation key',
            'username' => 'User name',
        ];
    }

    /*
     * Identity interface implementation
     */

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    // End interface

    public function validatePassword($password)
    {
        Yii::info("Validation pass ".$password);
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->where(['OR', ['email' => $username], ['username' => $username]])
            ->one();
    }
}
