<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "interprets".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Documents[] $documents
 */
class Interpret extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'interprets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['id'], 'safe']
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
    public function getDocuments()
    {
        return $this->hasMany(Documents::className(), ['interpret_id' => 'id']);
    }

    public function exists() {
        if(self::find(['name' => $this->id])->exists()) {
            $this->id = $this->getPrimaryKey();
            return true;
        }
        return false;
    }
}