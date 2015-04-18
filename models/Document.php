<?php

namespace app\models;

use Yii;
use \yii\helpers\Url;
use \yii\helpers\BaseArrayHelper;

/**
 * This is the model class for table "document".
 *
 * @property integer $id
 * @property string $name
 * @property string $link
 * @property integer $interpret_id
 *
 * @property Interpret $interpret
 * @property MapDocumentTag[] $mapDocumentTag
 * @property Action[] $actions
 * @property Tag[] $tags
 */
class Document extends \yii\db\ActiveRecord
{
    private $SPECIAL_TAGS = [
        '$' => ' $ ',
        '-' => ' - ',
        '+' => ' + ',
        '/' => ' / ',
        '\\' => ' \\ ',
        '%' => ' % ',
        '*' => ' * ',
        '&' => ' & ',
        '^' => ' ^ ',
        '#' => ' # ',
        '@' => ' @ ',
        '!' => ' ! ',
        '?' => ' ? ',
        '.' => ' . ',
        '_' => ' _ ',
        '"' => ' " ',
        "'" => " ' ",
        "=" => " = ",
    ];

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
            [['interpret_id', 'type_id'], 'integer'],
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
        return $this->hasMany( Tag::className(), ['id' => 'tag_id'])
            ->viaTable( MapDocumentTag::tableName(), ['document_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType() {
        return $this->hasOne( DocumentType::className(), ['id' => 'type_id']);
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

    public static function indexString($string) {
        $string = mb_strtolower($string, 'UTF-8');
        $string = strtr($string, $this->SPECIAL_TAGS);
        return preg_split('/[ ,\n\t`\[\]\(\)\{\}:]+/', $string);
    }

    public function getIndex() {
        return array_merge(
            static::indexString($this->name),
            static::indexString($this->interpret->name),
            ($this->type->name == 'akordy') ? 
                ['akordy', 'text'] :
                [$this->type->name]
        );
    }

    public static function indexedSearch($query)
    {
        return self::findBySql('
            SELECT document.*, s.search_string FROM document
            INNER JOIN document_search_string AS s ON document.id = s.document_id
            WHERE MATCH(s.search_string) AGAINST(:query)
            LIMIT 50
        ', [':query' => $query])->all();
    }
}
