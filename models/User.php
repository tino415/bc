<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

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
    public function getTags() {
        return $this->hasMany(Tag::className(), ['user_id' => 'id']);
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

    public function getRecommendDocuments($exclude = false) {
        $userTagWeights = (new Query)
            ->select(['tag.id', 'tag.name', new Expression('LOG(COUNT(*)) AS weight')])
            ->from('view')
            ->innerJoin('tag', new Expression('tag.id = view.tag_id'))
            ->where(['user_id' => $this->id])
            ->groupBy(['tag.id', 'tag.name'])
            ->having(new Expression('LOG(COUNT(*)) > 0'));

        $query = Document::find()
            ->innerJoin('map_document_tag map',
                new Expression('map.document_id = document.id'))
            ->innerJoin('tag',
                new Expression('map.tag_id = tag.id'))
            ->innerJoin(['user_tag' => $userTagWeights],
                new Expression('user_tag.id = tag.id'))
            ->where(new Expression('(user_tag.weight * map.weight) > 0'))
            ->orderBy(new Expression('(user_tag.weight * map.weight) DESC'));
        if($exclude) $query->andWhere(['<>', 'document.id', $exclude]);
        return $query;
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

    public function getTimeAwareRecommendDocuments($exclude = false) {
        $view_count = View::find()->where(['user_id' => $this->id])->count();
        $per_cluster_top = 50 / Yii::$app->params['long_term_groups'];
        $cluster_size = floor(
            $view_count / Yii::$app->params['long_term_groups']
        );

        $tags = [];
        Yii::info("Counting clusters\n");

        for($i=0; $i<$view_count; $i+=$cluster_size) {
            $querySlice = (new Query)->select('*')
                ->from('view')
                ->limit("$cluster_size")
                ->offset("$i")
                ->orderBy('id');

            $slice_tags = (new Query)->select('tag_id')
                ->from(['cluster' => $querySlice])
                ->groupBy('tag_id')
                ->limit($per_cluster_top)
                ->orderBy(new Expression('COUNT(*)'))
                ->all();

            for($j=0; $j<$per_cluster_top; $j++) {
                if(array_key_exists($slice_tags[$j]["tag_id"], $tags))
                    $tags[$slice_tags[$j]['tag_id']] += log10($j + 1);
                else $tags[$slice_tags[$j]['tag_id']] = log10($j + 1);
            }
        }

        $case = "CASE map.tag_id \n";
        foreach($tags as $tag_id => $weight) {
            $case .= "WHEN $tag_id THEN $weight\n";
        }
        $case .= "END\n";

        $query = Document::find()
            ->innerJoin('map_document_tag map',
                new Expression('map.document_id = document.id'))
            ->where(['map.tag_id' => array_keys($tags)])
            ->limit(50)
            ->orderBy(new Expression('SUM(map.weight * '.$case.')'))
            ->groupBy('document.id');

        if($exclude)
            $query->andWhere(['<>', 'document.id', $exclude]);

        return $query;
    }

    public function getSessionTags() {
        return (Session::getSession()) ? Session::getSession()->tags : [];
    }

    public function getTopTags() {
        return Tag::getTop()
            ->where(['user_id' => $this->id]);
    }

    public function getTagWeights() {
        return Tag::getTop()
            ->addSelect(new Expression('LOG(COUNT(*))+1 AS weight'))
            ->where(['user_id' => $this->id]);
    }

    public function getTagCounts($tags) {
        return (new Query())->select(['tag_id', new Expression('COUNT(*) AS count')])
            ->from('view')
            ->where(['tag_id' => $tags, 'user_id' => $this->id])
            ->groupBy('tag_id');
    }

    public static function recommendFor($ids) {
        $expression = new Expression(
            '((COUNT(DISTINCT user_id) / '.
            count($ids).'.00) * '.
            'LOG(COUNT(*) + 1))'
        );
        return (new Query())
            ->select(['tag_id', 'weight' => $expression])
            ->from('view')
            ->where(['user_id' => $ids])
            ->orderBy($expression)
            ->groupBy('tag_id');
    }
}
