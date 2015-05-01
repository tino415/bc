<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property integer $id
 * @property string $name
 *
 * @property View[] $views
 * @property Document[] $documents
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'unique'],
            [['name'], 'string', 'max' => 255]
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
    public function getViews()
    {
        return $this->hasMany(View::className(), ['tag_id' => 'id']);
    }

    public function getDocuments() {
        return $this->hasMany(Document::className(), ['id' => 'document_id'])
            ->viaTable( MapDocumentTag::tableName(), ['tag_id' => 'id']);
    }

    public static function getProfileTags($user_id = false) {
        $query = View::find();
        if($user_id) $query->where(['user_id' => $user_id]);
        $query->groupBy('view.tag_id')->limit(50)->orderBy('id');

        $ids = [];
        foreach($query->all() as $view) $ids[] = $view->tag_id;

        return Tag::find()->where(['id' => $ids])->all();
    }

    public static function calculateWeights() {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->db->createCommand(
                "UPDATE map_document_tag AS tg2".
                "SET weight = ".
                "   (LOG(tg2.count) + 1)/t2.sumdtf *".
                "   t2.U / (1 + 0.0115*t2.U) *".
                "   LOG((SELECT COUNT(*) FROM document) / nf)".

                "FROM map_document_tag AS tg".
                "INNER JOIN (".
                "   SELECT document_id,".
                "       SUM(LOG(count) +1) AS sumdtf,".
                "       COUNT(tag_id) AS U".
                "   FROM map_document_tag".
                "   GROUP BY document_id".
                ") AS t2 ON t2.document_id = tg.document_id".
                "INNER JOIN (".
                "   SELECT tag_id, COUNT(document_id) AS nf".
                "   FROM map_document_tag".
                "   GROUP BY tag_id".
                ") AS t3 ON t3.tag_id = tg.tag_id".
                "WHERE tg2.id = tg.id"
            )->execute();
        } catch (Exception $e) {
            $transaction->rollBack();
        }

        $transaction->commit();
    }

    public function __toString() {
        return $this->name;
    }
}
