<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "action".
 *
 * @property integer $id
 * @property integer $type_id
 * @property integer $user_id
 * @property integer $document_id
 * @property string $timestamp
 *
 * @property ActionType $type
 * @property Documents $document
 * @property Users $user
 */
class Action extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'action';
    }

    /**
     * @inheritdoc
     */
    public function behaviours() {
        return [
            'class' => TimestampBehavior::className(),
            'createdAtAttribute' => 'timestamp',
            'value' => new Expression('NOW()'),
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'user_id', 'document_id'], 'required'],
            [['type_id', 'user_id', 'document_id'], 'integer'],
            [['timestamp'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => 'Type ID',
            'user_id' => 'User ID',
            'document_id' => 'Document ID',
            'timestamp' => 'Timestamp',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ActionType::className(), ['id' => 'type_id']);
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
}
