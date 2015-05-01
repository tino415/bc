<?php

use yii\db\Schema;
use yii\db\Migration;

class m150418_114231_add_weight_map_document_tag extends Migration
{
    public function up()
    {
        $this->addColumn(
            'map_document_tag',
            'weight',
            Schema::TYPE_DECIMAL . '(9, 8) NOT NULL DEFAULT 0'
        );

        $this->addColumn(
            'map_document_tag',
            'count',
            Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1'
        );
    }
    
    public function down()
    {
        $this->dropColumn('map_document_tag', 'weight');
        $this->dropColumn('map_document_tag', 'count');
    }
}
