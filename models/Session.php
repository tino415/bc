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

    public function getTags() {
        $tags = Tag::findBySql("
            SELECT * FROM tag
            WHERE tag_id IN(
                SELECT tag_id FROM view
                WHERE session_id = :session_id
                GROUP BY tag_id
                ORDER BY COUNT(*)
            );
        ")
        ->bindParam([':session_id' => $this->id])
        ->queryAll();
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

    private static $_session = false;

    public static function getSession() {
        Yii::info("Session is ".Yii::$app->session['search_session']);
        if(
            !isset(Yii::$app->session['search_session']) ||
            !preg_match('/[0-9]+:[0-9]+/', Yii::$app->session['search_session'])
        ) return false;
        elseif(!self::$_session) {
            list($session_id, $session_time) = explode(
                ':', Yii::$app->session['search_session']
            );
            $session_time += Yii::$app->params['search_session_timeout'];
            if($session_time > time()) {
                self::$_session = Session::findOne($session_id);
            } else return false;
        } 
        return self::$_session;
    }
}
