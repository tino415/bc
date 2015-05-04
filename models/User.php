<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\db\Expression;

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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => Yii::t('app', 'ID'),
            'email'        => Yii::t('app', 'Email'),
            'role_id'      => Yii::t('app', 'Role ID'),
            'access_token' => Yii::t('app', 'Access token'),
            'auth_key'     => Yii::t('app', 'Authentisation key'),
            'username'     => Yii::t('app', 'User name'),
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

    public function getShortTermTags() {
        return Tag::find()
            ->distinct(true)
            ->joinWith('views')
            ->where(['view.user_id' => $this->id])
            ->limit(50)
            ->orderBy('id')
            ->all();
    }

    public function getLongTermTags() {
        $view_count = View::find()->where(['user_id' => $this->id])->count();
        $per_cluster_top = 50 / Yii::$app->params['long_term_groups'];
        $cluster_size = floor(
            $view_count / Yii::$app->params['long_term_groups']
        );

        $tags = [];
        Yii::info("Counting clusters\n");
        for($i=0; $i<$view_count; $i+=$cluster_size)
            $querySlice = (new Query)->select('*')
                ->from('view')
                ->limit($cluster_size)
                ->offset($i)
                ->orderBy('id');

            $topOfSlice = (new Query)->select('tag_id')
                ->from(['cluster' => $querySlice])
                ->groupBy('tag_id')
                ->orderBy(new Expression('COUNT(*)'));

            $tags = Tag::find()
                ->where(['id' => $topOfSlice])
                ->limit($per_cluster_top)
                ->all();

            //$tags = $tags + 
            //  Tag::findBySql("
            //      SELECT * FROM tag WHERE id IN (
            //          SELECT tag_id FROM (
            //              SELECT * FROM view LIMIT :limit OFFSET :offset
            //          ) AS cluster
            //          GROUP BY tag_id
            //          ORDER BY COUNT(*)
            //      )
            //      LIMIT :limit_per
            //  ", [
            //      ':limit' => $cluster_size,
            //      ':offset' => $i,
            //      ':limit_per' => $per_cluster_top,
            //  ])->all();
        return $tags;
    }

    public function getSessionTags() {
        return (Session::getSession()) ? Session::getSession()->tags : [];
    }

    public function getRecommendTags() {
        return array_merge(
            $this->shortTermTags,
            $this->sessionTags,
            $this->longTermTags
        );
    }

    public function getTopTags($count) {
        return Tag::getTop($count, $this->id);
    }

    public function getTagCounts($tags) {
        return (new Query())->select(['tag_id', new Expression('COUNT(*) AS count')])
            ->indexBy('tag_id')
            ->from('view')
            ->where(['tag_id' => $tags, 'user_id' => $this->id])
            ->groupBy('tag_id')
            ->all();
    }

    /**
     * Get tags for multiple users
     */
    public static function recommendFor($where) {
        $users = User::find()->where($where)->all();

        $tags = [];

        foreach($users as $user) {
            $tags = $tags + $user->recommendTags;
        }

        return $tags;
    }
}
