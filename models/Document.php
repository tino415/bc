<?php

namespace app\models;

use Yii;
use \yii\helpers\Url;
use \yii\helpers\BaseArrayHelper;
use \yii\db\Expression;
use \yii\db\Query;
use \app\components\ActiveRecord;

/** * This is the model class for table "document".
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
        $dname = mb_strtolower($this->name, 'UTF-8');
        $iname = mb_strtolower($this->interpret->name, 'UTF-8');
        $res = [];

        $vals = $doc + $inter + 
            [$dname => ''] + 
            [$iname => ''] + 
            [$this->type->name => ''];

        foreach(array_keys($vals) as $name) {
            $IN = ($name == $iname);
            $DN = ($name == $dname);
            $IT = (array_key_exists($name, $inter));
            $DT = (array_key_exists($name, $doc));

            $count = 
                ($DT ? $doc[$name] : 0) + 
                ($IT ? $inter[$name] : 0);

            $count = (!$count) ? 1 : $count;

            $res[] = [
                'name' => $name,
                'count' => $count,
                'type' => self::getTagType($IN, $DN, $IT, $DT),
            ];

        }

        return $res;
    }

    public function saveTags($tags) {
        foreach($tags as $tag_val) {
            $tag = new Tag;
            $tag->name = (string)$tag_val['name'];
            try {
                if($tag->validate()) $tag->save();
                else $tag = Tag::find()->where(['name' => $tag->name])->one();
            } catch(\Exception $e) {
                Yii::error("$e\n");
                $tag = Tag::find()->where(['name' => $tag->name])->one();
            }


            $map = new MapDocumentTag;
            $map->document_id = $this->id;
            $map->tag_id = $tag->getPrimaryKey();
            $map->count = $tag_val['count'];
            $map->type_id = $tag_val['type'];
            try {
                if($map->validate()) $map->save();
                else Yii::info('Error saving map'. print_r($map->errors, 1));
            } catch(\Exception $e) {
                Yii::error("$e\n");
            }
        }
    }

    public function createTagsFromAtts() {
        $tags = $this->getTagsFromAtts();
        $this->saveTags($tags);
    }

    public static function recommend() {
        if(Yii::$app->params['time_aware_recommendation']) {
            if(Yii::$app->user->isGuest) $tags = Tag::getProfileTags(
                Yii::$app->params['anonymousUserId']
            );
            else $tags = Tag::getProfileTags(Yii::$app->user->id);
            return self::match($tags);
        } else {
            if(Yii::$app->user->isGuest)
                return User::findOne(Yii::$app->params['anonymousUserId'])
                    ->recommendDocuments;
            else
                return User::findOne(Yii::$app->user->id)
                    ->recommendDocuments;
        }
    }

    public static function match($tags, $exclude = false) {
        $or = ['or'];

        foreach($tags as $tag)
            $or[] = ['name' => "$tag"];

        $subQuery = (new Query)->select('id')
            ->from('tag')
            ->where($or);

        $query = (new Query)->select('document_id')
            ->from('map_document_tag')
            ->where(['tag_id' => $subQuery])
            ->groupBy('document_id')
            ->orderBy(new Expression('SUM(weight)'))
            ->limit("50");

        if($exclude)
            $query->andWhere(['not in', 'document_id', $exclude]);

        return Document::find()->where(['id' => $query])->all();
    }

    public static function matchCounted($tags, $counts, $exclude = false) {
    }

    public static function search($query, $limit = 50)
    {
        $query_tags = array_count_values(Tag::escape($query));
        return self::match(array_keys($query_tags));
    }

    public static function getTagType($IN, $DN, $IT, $DT) {
        if($DN && $IN) return 8;
        elseif($DN && $IT && !$IN) return 7;
        elseif($IN && $DT) return 6;
        elseif($DN) return 5;
        elseif($IN) return 4;
        elseif($DT && $IT) return 3;
        elseif($DT) return 2;
        elseif($IT) return 1;
        else return 0;
    }
}
