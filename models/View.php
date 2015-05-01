<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "view".
 *
 * @property integer $id
 * @property integer $document_id
 * @property integer $user_id
 * @property integer $tag_id
 * @property string $created
 *
 * @property Tag $tag
 * @property Document $document
 * @property User $user
 */
class View extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_id', 'user_id', 'tag_id'], 'required'],
            [['document_id', 'user_id', 'tag_id'], 'integer'],
            [['created'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'document_id' => 'Document ID',
            'user_id' => 'User ID',
            'tag_id' => 'Tag ID',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::className(), ['id' => 'tag_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::className(), ['id' => 'document_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function beforeSave($insert) {
        if($this->isNewRecord && !$this->created) {
            $this->created = time();
        }
    }
}
