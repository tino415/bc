<?php

namespace app\models;

use Yii;
use \yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "tag".
 *
 * @property integer $id
 * @property string $name
 *
 * @property View[] $views
 * @property Document[] $documents
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'unique'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViews()
    {
        return $this->hasMany(View::className(), ['tag_id' => 'id']);
    }

    public function getDocuments() {
        return $this->hasMany(Document::className(), ['id' => 'document_id'])
            ->viaTable( MapDocumentTag::tableName(), ['tag_id' => 'id']);
    }

    public static function getProfileTags($user_id = false) {
        $query = View::find();
        if($user_id && !Tag::find()->where(['user_id' => $user_id])->limit(1)->count())
            $user_id = false;
        if($user_id) $query->where(['user_id' => $user_id]);
        $query->groupBy('view.tag_id')->limit(50)->orderBy('id');

        $ids = [];
        foreach($query->all() as $view) $ids[] = $view->tag_id;

        return Tag::find()->where(['id' => $ids])->all();
    }

    public static function escape($string, $stop_words = true) {
        $string = preg_replace(
            '/[!?#*<>\[\]\(\)@$%^&{}\'"\`\\-\\\\ \t\n\.;:,_=]+/',
            ' ',
            $string
        );
        $string = trim(mb_strtolower($string, 'UTF-8'));

        $pieces = preg_split('/[ \(\(]/', $string);
        $result = [];
        foreach($pieces as $piece) {
            if( !empty($piece) && (
                !$stop_words || (
                !array_key_exists($piece, Yii::$app->params['stopwords'])
                )
                )
            )
            $result[] = $piece;
        }
        unset($pieces);
        return $result;
    }

    private $_viewCount = false;

    public function getViewCount() {
        if(!$this->_viewCount)
            $this->_viewCount = View::find()->where(['tag_id' => $this->id])->count();
        return $this->_viewCount;
    }

    public static function getTop() {
        return (new Query)->select(['tag.id AS id', 'tag.name AS name',
                new Expression('COUNT(*) AS count')]
            )
            ->from('view')
            ->innerJoin('tag', new Expression('tag.id = view.tag_id'))
            ->groupBy(['tag.id', 'tag.name'])
            ->orderBy(new Expression('COUNT(*) DESC'));
    }

    public function __toString() {
        return $this->name;
    }
}
