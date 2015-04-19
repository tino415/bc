<?php

namespace app\models;

use Yii;
use \yii\helpers\Url;
use \yii\helpers\BaseArrayHelper;

/**
 * This is the model class for table "document".
 *
 * @property integer $id
 * @property string $name
 * @property string $link
 * @property integer $interpret_id
 *
 * @property Interpret $interpret
 * @property MapDocumentTag[] $mapDocumentTag
 * @property Action[] $actions
 * @property Tag[] $tags
 */
class Document extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'document';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'interpret_id', 'type_id'], 'required'],
            [['interpret_id', 'type_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'interpret_id' => 'Interpret ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActions()
    {
        return $this->hashMany(Action::className(), ['document_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterpret()
    {
        return $this->hasOne(Interpret::className(), ['id' => 'interpret_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags() {
        return $this->hasMany( Tag::className(), ['id' => 'tag_id'])
            ->viaTable( MapDocumentTag::tableName(), ['document_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMapDocumentTags() {
        return $this->hasMany( MapDocumentTag::className(), ['document_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType() {
        return $this->hasOne( DocumentType::className(), ['id' => 'type_id']);
    }

    public static function escapeTags($string, $stop_words = true) {
        $string = preg_replace(
            '/[!?#*<>\[\]\(\)@$%^&{}\'"\`\/\\-\\\\ \t\n\.;:,_=]+/',
            ' ',
            $string
        );
        $string = trim(mb_strtolower($string, 'UTF-8'));

        $pieces = preg_split('/[ \(\(]/', $string);
        $result = [];
        foreach($pieces as $piece) 
            if( !$stop_words || (
                !array_key_exists($piece, Yii::$app->params['stopwords']) &&
                strlen($piece) > Yii::$app->params['min_tag_length']
                )
            )
                $result[] = $piece;
        unset($pieces);
        return $result;
    }

    public static function match($tags) {
        $tag_match = 'name LIKE(\''.
            implode(
                "%') OR name LIKE('", 
                $tags
            ).
            '%\')';

        $document_ids = Yii::$app->db->createCommand("
            SELECT document_id FROM map_document_tag
            WHERE tag_id IN (SELECT id FROM tag WHERE $tag_match)
            GROUP BY document_id
            ORDER BY SUM(weight) DESC
            LIMIT 50
        ")->queryAll();
        $ids = [];
        foreach($document_ids as $did) $ids[] = $did['document_id'];
        unset($document_ids);

        return Document::find()->where(['id' => $ids])->all();
    }

    public static function search($query, $limit = 50)
    {
        $query_tags = array_count_values(self::escapeTags($query));
        return self::match(array_keys($query_tags));
    }
}
