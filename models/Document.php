<?php

namespace app\models;

use Yii;
use \yii\helpers\Url;
use \yii\helpers\BaseArrayHelper;
use \app\components\ActiveRecord;

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
 * @property Schema[] $scehmas
 * @property Tag[] $tags
 */
class Document extends ActiveRecord
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
            'id'           => Yii::t('app', 'ID'),
            'name'         => Yii::t('app', 'Name'),
            'interpret_id' => Yii::t('app', 'Interpret ID'),
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchemas() {
        Yii::info('Schemas');
        return $this->hasMany( Schema::className(), ['document_id' => 'id']);
    }

    public function getTagsFromAtts() {
        $doc = $this->nameTags;
        $inter = $this->interpret->nameTags;
        $res = [];

        foreach(array_keys($doc) + array_keys($inter) as $name)
            if(array_key_exists($name, $doc) && array_key_exists($inter)) 
                $res[$name] = $doc[$name] + $inter[$name];
            elseif(array_key_exists($name, $doc))
                $res[$name] = $doc[$name];
            elseif(array_key_exists($name, $inter))
                $res[$name] = $inter[$name];
    }

    public function saveTags($tags) {
        foreach($tags as $tag_name => $count) {
            $tag = new Tag;
            $tag->name = $tag_name;
            if($tag->validate()) {
                $tag->save();
                $map = new MapDocumentTag;
                $map->document_id = $this->id;
                $map->tag_id = $tag->getPrimaryKey();
                $map->count = $count;
                if($map->validate()) $map->save();
            }
        }
    }

    public function createTagsFromAtts() {
        $tags = $this->getTagsFromAtts();
        $this->saveTags($tags);
    }

    public static function recommend() {
        if(Yii::$app->user->isGuest) $tags = Tag::getProfileTags(Yii::$app->user->id);
        else $tags = Tag::getProfileTags(Yii::$app->user->id);
        return self::match($tags);
    }

    public static function match($tags, $exclude = false) {
        $tag_match = 'name LIKE(\''.
            implode(
                "') OR name LIKE('", 
                $tags
            ).
            '\')';
        if(!$exclude) $exclude = '';
        elseif(is_numeric($exclude)) $exclude = " AND document_id <> $exclude ";
        elseif(is_array($exclude))
            $exclude = " AND document_id NOT IN (".implode(', ', $exclude).") ";

        $document_ids = Yii::$app->db->createCommand("
            SELECT document_id FROM map_document_tag
            WHERE tag_id IN (SELECT id FROM tag WHERE $tag_match)
            $exclude
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
        $query_tags = array_count_values(Tag::escape($query));
        return self::match(array_keys($query_tags));
    }
}
