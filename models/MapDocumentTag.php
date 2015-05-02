<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "map_document_tag".
 *
 * @property integer $id
 * @property integer $document_id
 * @property integer $tag_id
 * @property integer $count
 *
 * @property Tag $tag
 * @property Document $document
 */
class MapDocumentTag extends \yii\db\ActiveRecord
{

    private $_logDocumentTermFrequency = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_document_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_id', 'tag_id'], 'required'],
            [['document_id', 'tag_id'], 'integer'],
            [['document_id', 'tag_id'], 'unique', 'targetAttribute' => [
                'document_id', 
                'tag_id'
            ]],
            [['count', 'weight'], 'safe'],
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
            'tag_id' => 'Tag ID',
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

    public function getLogDocumentTermFrequency() {
        if(!$this->$_logDocumentTermFrequency)
            $this->$_logDocumentTermFrequency = log10($this->count) + 1;
        return $this->$_logDocumentTermFrequency;
    }
}
