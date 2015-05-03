<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "query".
 *
 * @property integer $id
 * @property string $query
 * @property integer $user_id
 * @property string $timestamp
 *
 * @property Users $user
 */
class Query extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'query';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['query', 'user_id'], 'required'],
            [['user_id'], 'integer'],
            [['timestamp'], 'safe'],
            [['query'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'query' => 'Query',
            'user_id' => 'User ID',
            'timestamp' => 'Timestamp',
        ];
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
            $this->created = date('Y-m-d G:i:s');
        }
        parent::beforeSave($insert);
        return true;
    }
}
