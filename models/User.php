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
 *
 * @property MapUserTag[] $mapUserTags
 * @property Action[] $actions
 * @property Tag[] $tags
 * @property Query[] $queries
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{

    public $password = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['role_id'], 'integer'],
            [['email', 'password_hash'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['access_token', 'auth_key', 'username'], 'safe'],
        ];
    }

    public function scenarios() {
        return array_merge([
            'select' => ['name'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
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

    public function getRole() 
    {
        return $this->role_id;
    }

    public function validatePassword($password)
    {
        Yii::info("Validation pass ".$password);
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActions()
    {
        return $this->hashMany(Action::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQueries()
    {
        return $this->hashMany(Query::className(), ['user_id' => 'id']);
    }

    public function beforeSave($insert) {
        Yii::info("Before");
        if($this->password)
            Yii::info("New password");
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        return parent::beforeSave($insert);
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

    public static function recommendFor($where) {
        $users = User::find()->where($where)->all();

        $tags = [];

        foreach($users as $user) {
            $tags = $tags + Tag::getProfileTags($user->id);
        }

        return $tags;
    }
}
