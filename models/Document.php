<?php

namespace app\models;

use Yii;
use \yii\helpers\Url;
use \yii\helpers\BaseArrayHelper;

/**
 * This is the model class for table "documents".
 *
 * @property integer $id
 * @property string $name
 * @property string $link
 * @property integer $interpret_id
 *
 * @property Interpret $interpret
 * @property MapDocumentsTags[] $mapDocumentsTags
 * @property Tag[] $tags
 */
class Document extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'documents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'link', 'interpret_id'], 'required'],
            [['interpret_id'], 'integer'],
            [['name', 'link'], 'string', 'max' => 255],
            [['link'], 'unique']
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
            'link' => 'Link',
            'type_id' => 'Type ID',
            'interpret_id' => 'Interpret ID',
        ];
    }

    public function exists() {
        if(self::find(['name' => $this->id])->exists()) {
            $this->id = $this->getPrimaryKey();
            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterpret()
    {
        return $this->hasOne(Interpret::className(), ['id' => 'interpret_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags() {
        return $this->hasMany(Tag::className(), 
            ['id' => 'tag_id'])->viaTable('map_documents_tags', ['document_id' => 'id']
        );
    }

    private static function searchQuery($limit, $where) {
        return self::find()
            ->joinWith(['tags', 'interpret'])
            ->where($where)
            ->limit($limit)
            ->all();
    }

    public static function search($query)
    {
        $result = [];
        $documents = [];
        $documents = self::searchQuery(
            50, ['and', 
                ['like', 'tags.name', $query],
                ['like', 'interprets.name', $query],
                ['like', 'documents.name', $query],
            ]);

        Yii::info('And search: '.count($documents));

        if(count($documents) < 50) {
            $documents += self::searchQuery( 50 - count($documents),
                ['or',
                    ['and',
                        ['like', 'tags.name', $query],
                        ['like', 'interprets.name', $query],
                    ],
                    ['and',
                        ['like', 'interprets.name', $query],
                        ['like', 'documents.name', $query],
                    ],
                    ['and', 
                        ['like', 'tags.name', $query],
                        ['like', 'documents.name', $query],
                    ]
                ]
            );
        }

        if(count($documents) < 50) {
            $documents += self::searchQuery( 50 - count($documents),
                ['or',
                    ['like', 'tags.name', $query],
                    ['like', 'interprets.name', $query],
                    ['like', 'documents.name', $query],
                ]
            );
        }

        Yii::info("Search results: ".count($documents));

        foreach($documents as $doc) {
            $result[] = [
                'name'      => $doc->name,
                'link'      => Url::toRoute(['site/song', 'id' => $doc->id]),
                'interpret' => $doc->interpret->name,
                'tags'      => [],
            ];

            foreach($doc->tags as $tag) {
                $result['tags'][] = $tag->name;
            }
        }

        return $result;
    }
}
