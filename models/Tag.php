<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tags".
 *
 * @property integer $id
 * @property string $name
 *
 * @property MapDocumentsTags[] $mapDocumentsTags
 * @property Documents[] $documents
 * @property MapUsersTags[] $mapUsersTags
 * @property Users[] $users
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tags';
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
    public function getMapDocumentsTags()
    {
        return $this->hasMany(MapDocumentsTags::className(), ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(Documents::className(), [
            'id' => 'document_id'])->viaTable('map_documents_tags', ['tag_id' => 'id']);
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
            ['id' => 'user_id'])->viaTable('map_users_tags', ['tag_id' => 'id']);
    }

    public function exists() {
        if(self::find(['name' => $this->id])->exists()) {
            $this->id = $this->getPrimaryKey();
            return true;
        }
        return false;
    }
}
