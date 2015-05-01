<?php

namespace app\models;

use Yii;

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
        if($user_id) $query->where(['user_id' => $user_id]);
        $query->groupBy('view.tag_id')->limit(50)->orderBy('id');

        $ids = [];
        foreach($query->all() as $view) $ids[] = $view->tag_id;

        return Tag::find()->where(['id' => $ids])->all();
    }

    public function __toString() {
        return $this->name;
    }
}
