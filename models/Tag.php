<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tags".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type_id
 *
 * @property MapDocumentsTags[] $mapDocumentsTags
 * @property Documents[] $documents
 * @property MapUsersTags[] $mapUsersTags
 * @property Users[] $users
 * @property TagType[] $type
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
            [['type_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['name', 'type_id'], 'unique', 'targetAttribute' => ['name', 'type_id'],
            'message' => 'The combination of Name and Type ID has already been taken.'],
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
    public function getMapDocumentsTags()
    {
        return $this->hasMany(MapDocumentsTags::className(), ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(Document::className(), [
            'id' => 'document_id'])->viaTable('map_document_tag', ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMapUsersTags()
    {
        return $this->hasMany(MapUsersTags::className(), ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(),
            ['id' => 'user_id'])->viaTable('map_user_tag', ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType() 
    {
        return $this->hasOne(TagType::className(), ['id' => 'type_id']);
    }

    public function exists() {
        if(self::find(['name' => $this->id])->exists()) {
            $this->id = $this->getPrimaryKey();
            return true;
        }
        return false;
    }
}
