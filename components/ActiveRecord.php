<?php

namespace app\components;

use app\models\Tag;

abstract class ActiveRecord extends \yii\db\ActiveRecord {

    private $_nameTags = false;

    public function getNameTags() {
        if(!$this->_nameTags) {
            $this->_nameTags = array_count_values(
                Tag::escape($this->name)
            );
            $name = mb_strtolower($this->name, 'UTF-8');
            if(!array_key_exists($name, $this->_nameTags))
                $this->_nameTags[$name] = 1;
        }
        return $this->_nameTags;
    }
}
