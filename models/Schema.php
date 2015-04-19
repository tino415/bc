<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "schema".
 *
 * @property integer $id
 * @property string $content
 * @property integer $document_id
 *
 * @property Document $document
 */
class Schema extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'schema';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'document_id'], 'required'],
            [['document_id'], 'integer'],
            [['content'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Content',
            'document_id' => 'Document ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::className(), ['id' => 'document_id']);
    }
}
