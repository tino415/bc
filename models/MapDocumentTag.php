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
                'tag_id',
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

    public static function calculateWeights() {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->db->createCommand(
                "UPDATE map_document_tag AS tg2 ".
                "SET weight = ".
                "   (LOG(tg2.count) + 1)/t2.sumdtf * ".
                "   t2.U / (1 + 0.0115*t2.U) * ".
                "   LOG((SELECT COUNT(*) FROM document) / nf) * ".
                "   CASE    WHEN tg2.type_id = 0 THEN 0.5".
                "           WHEN tg2.type_id = 1 THEN 0.55".
                "           WHEN tg2.type_id = 2 THEN 0.6".
                "           WHen tg2.type_id = 3 THEN 0.65".
                "           WHEN tg2.type_id = 4 THEN 0.7".
                "           WHEN tg2.type_id = 5 THEN 0.75".
                "           WHEN tg2.type_id = 6 THEN 0.8".
                "           WHEN tg2.type_id = 7 THEN 0.85".
                "           WHEN tg2.type_id = 8 THEN 0.9".
                "   END ".

                "FROM map_document_tag AS tg ".
                "INNER JOIN (".
                "   SELECT document_id, ".
                "       SUM(LOG(count) +1) AS sumdtf, ".
                "       COUNT(tag_id) AS U ".
                "   FROM map_document_tag ".
                "   GROUP BY document_id ".
                ") AS t2 ON t2.document_id = tg.document_id ".
                "INNER JOIN (".
                "   SELECT tag_id, COUNT(document_id) AS nf ".
                "   FROM map_document_tag ".
                "   GROUP BY tag_id ".
                ") AS t3 ON t3.tag_id = tg.tag_id ".
                "WHERE tg2.id = tg.id "
            )->execute();
        } catch (Exception $e) {
            $transaction->rollBack();
        }

        $transaction->commit();
    }
}
