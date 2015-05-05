<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\Expression;

class TagStatisticsModel extends Model {

    private $id;
    private $name = false;
    private $count = false;
    private $weight = false;

    public function getId() {return $this->id;}
    public function getName() {return $this->name;}
    public function getCount() {return $this->count;}
    public function getWeight() {return $this->weight;}

    public function getDb() {
        return Yii::$app->db;
    }

    public static function instantiate() {
        return new static;
    }

    protected static function agregates() {
        return [
            'count' => [
                'column' => new Expression('COUNT(*) AS count'),
                'order' => new Expression('COUNT(*)'),
            ],
            'weight' => [
                'column' => new Expression('LOG(COUNT(*)) AS count'),
                'order' => new Expression('LOG(COUNT(*))'),
            ],
        ];
    }

    protected static function addAgregateColumn($query, $agregate) {
        return $query->addSelect(static::agregates()[$agregate]['column']);
    }

    protected static function baseQuery($agregates, $table, $withName = false) {
        $query = (new ActiveQuery('app\models\TagStatisticsModel'))
            ->select("$table.tag_id AS id")
            ->from($table)
            ->groupBy("$table.tag_id")
            ->orderBy(static::agregates()[$agregates[0]]['order']);

        if($withName) static::addName($table, $query);

        foreach($agregates as $agregate)
            static::addAgregateColumn($query, $agregate);

        return $query;
    }

    public static function documentFind($documentAgregates, $withName = false) {
        return static::baseQuery($documentAgregates, 'map_document_tag', $withName);
    }

    public static function userFind($userAgregates, $withName = false) {
        return static::baseQuery($userAgregates, 'view', $withName);
    }

    public static function addName($table, $query) {
        $query->innerJoin('tag', new Expression("tag.id = $table.tag_id"))
            ->addSelect('tag.name AS name')
            ->addGroupBy('tag.name');
        return $query;
    }
}
