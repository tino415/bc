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
        return $this->hasMany( Schema::className(), ['document_id' => 'id']);
    }

    public function getTagsOrdered() {
        return Tag::find()
            ->innerJoin('map_document_tag map',
                new Expression('map.tag_id = tag.id'))
            ->where(['map.document_id' => $this->id])
            ->orderBy('map.weight DESC');
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

            if($tag->validate()) $tag->save();
            else $tag = Tag::find()->where(['name' => $tag->name])->one();

            $map = MapDocumentTag::find()->where([ 
                'document_id' => $this->id, 'tag_id' => $tag->getPrimaryKey()
            ])->one();


            if(!$map) $map = new MapDocumentTag;
            $map->document_id = $this->id;
            $map->tag_id = $tag->getPrimaryKey();
            $map->count = $tag_val['count'];
            $map->type_id = $tag_val['type'];

            try {
                if($map->validate()) $map->save();
                else {
                    Yii::info('Error saving map'. print_r($map->errors, 1));
                }
            } catch(\Exception $e) {
                Yii::error("$e\n");
            }
        }
    }

    public function createTagsFromAtts() {
        $tags = $this->getTagsFromAtts();
        Yii::info('Get tags '.print_r($tags, true));
        $this->saveTags($tags);
    }

    public static function recommend($exclude = false) {
        if(Yii::$app->params['time_aware_recommendation']) {
            Yii::info('Time aware recommendation');
            if(Yii::$app->user->isGuest)
                return User::findOne(Yii::$app->params['anonymousUserId'])
                    ->getTimeAwareRecommendDocuments($exclude);
            else
                return User::findOne(Yii::$app->user->id)
                    ->getTimeAwareRecommendDocuments($exclude);
        } else {
            Yii::info('No time aware recommendation');
            if(Yii::$app->user->isGuest)
                return User::findOne(Yii::$app->params['anonymousUserId'])
                    ->getRecommendDocuments($exclude);
            else
                return User::findOne(Yii::$app->user->id)
                    ->getRecommendDocuments($exclude);
        }
    }

    public function getSimiliar() {
        return static::find()
            ->innerJoin('map_document_tag map',
                new Expression('map.document_id = document.id'))
            ->innerJoin(['doc_map' => $this->getMapDocumentTags()],
                new Expression('doc_map.tag_id = map.tag_id'))
            ->where(['<>', 'document.id', $this->id])
            ->groupBy('document.id')
            ->orderBy(new Expression('SUM(map.weight * doc_map.weight) DESC'));
    }

    public static function similiarTags($document1, $document2) {
        return Tag::find()
            ->innerJoin('map_document_tag map1', new Expression('map1.tag_id = tag.id'))
            ->where(['map1.document_id' => $document1->id])
            ->innerJoin('map_document_tag map2', new Expression('map2.tag_id = tag.id'))
            ->andWhere(['map2.document_id' => $document2->id])
            ->andWhere(new Expression('map1.document_id = map2.document_id'));
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
            ->orderBy(new Expression('SUM(weight) DESC'))
            ->limit("50");

        if($exclude)
            $query->andWhere(['not in', 'document_id', $exclude]);

        return Document::find()->where(['id' => $query]);
    }

    public static function search($query)
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
