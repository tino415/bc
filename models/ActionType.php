<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "action_type".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Action[] $actions
 */
class ActionType extends \yii\db\ActiveRecord
{
    
    const DISPLAY_ID = 1;
    const STAY_ID = 2;
    const FOCUS_ID = 3;
    const BLUR_ID = 4;
    const LEAVE_ID = 5;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'action_type';
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
    public function getActions()
    {
        return $this->hasMany(Action::className(), ['type_id' => 'id']);
    }

}
