<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tag_type".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Tags[] $tags
 */
class TagType extends \yii\db\ActiveRecord
{

    const TYPE = 1;
    const INTERPRET = 2;
    const NAME = 3;
    const GENRE = 4;
    const OTHER = 5;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique']
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
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['type_id' => 'id']);
    }
}
