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
 * @property Action[] $actions
 * @property Tag[] $tags
 */
class Document extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'document';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'interpret_id', 'type_id'], 'required'],
            [['interpret_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'interpret_id' => 'Interpret ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActions()
    {
        return $this->hashMany(Action::className(), ['document_id' => 'id']);
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
        return $this->hasMany(
            Tag::className(), 
            ['id' => 'tag_id']
        )->viaTable('map_document_tag', ['document_id' => 'id']);
    }

    public function getType() {
        return $this->hasOne( DocumentType::className(), ['id', 'type_id']);
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
                ['like', 'tag.name', $query],
                ['like', 'interpret.name', $query],
                ['like', 'document.name', $query],
            ]);

        Yii::info('And search: '.count($documents));

        if(count($documents) < 50) {
            $documents += self::searchQuery( 50 - count($documents),
                ['or',
                    ['and',
                        ['like', 'tag.name', $query],
                        ['like', 'interpret.name', $query],
                    ],
                    ['and',
                        ['like', 'interpret.name', $query],
                        ['like', 'document.name', $query],
                    ],
                    ['and', 
                        ['like', 'tag.name', $query],
                        ['like', 'document.name', $query],
                    ]
                ]
            );
        }

        if(count($documents) < 50) {
            $documents += self::searchQuery( 50 - count($documents),
                ['or',
                    ['like', 'tag.name', $query],
                    ['like', 'interpret.name', $query],
                    ['like', 'document.name', $query],
                ]
            );
        }

        Yii::info("Search results: ".count($documents));

        foreach($documents as $doc) {
            $tags = [];

            foreach($doc->tags as $tag) $tags[] = $tag->name;
            $result[] = [
                'name'      => $doc->name,
                'link'      => Url::toRoute(['document/rview', 'id' => $doc->id]),
                'interpret' => $doc->interpret->name,
                'tags'      => $tags,
            ];

        }

        return $result;
    }

    public static function tagSearch($query)
    {
        $documents = self::find()
            ->joinWith('tags')
            ->where(['like', 'tag.name', $query])
            ->orderBy('tag.type_id')
            ->limit(50)
            ->All();

        $results = [];
        foreach($documents as $doc) {
            $tags = [];
            foreach($doc->tags as $tag) $tags[] = $tag->name;
            $results[] = [
                'name'      => $doc->name,
                'link'      => Url::toRoute(['document/rview', 'id' => $doc->id]),
                'interpret' => $doc->interpret->name,
                'tags'      => $tags,
            ];
        }
        return $results;
    }
}
