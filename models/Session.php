<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "session".
 *
 * @property integer $id
 *
 * @property View[] $views
 */
class Session extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'session';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViews()
    {
        return $this->hasMany(View::className(), ['session_id' => 'id']);
    }

    public function renev() {
        Yii::$app->session['search_session'] = "$this->id:".time();
    }

    public static function create() {
        $session = new Session;
        $session->save();
        Yii::$app->session['search_session'] = "$session->primaryKey:".time();
        return $session;
    }

    public static function getSession() {
        Yii::info("Session is ".Yii::$app->session['search_session']);
        if(
            !isset(Yii::$app->session['search_session']) ||
            !preg_match('/[0-9]+:[0-9]+/', Yii::$app->session['search_session'])
        ) return false;
        else {
            list($session_id, $session_time) = explode(
                ':', Yii::$app->session['search_session']
            );
            $session_time += Yii::$app->params['search_session_timeout'];
            if($session_time > time()) {
                $session = Session::findOne($session_id);
                $session->id = $session_id;
                return $session;
            } else return false;
        }
    }
}
