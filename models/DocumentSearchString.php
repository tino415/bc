<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document_search_string".
 *
 * @property integer $id
 * @property string $search_string
 * @property integer $document_id
 */
class DocumentSearchString extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'document_search_string';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_id'], 'integer'],
            [['search_string'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'search_string' => 'Search String',
            'document_id' => 'Document ID',
        ];
    }
}
