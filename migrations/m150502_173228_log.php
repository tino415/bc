<?php

use yii\db\Schema;
use yii\db\Migration;

class m150502_173228_log extends Migration
{
    public function up()
    {
        $this->addColumn(
            'view',
            'possition',
            Schema::TYPE_INTEGER
        );
    }

    public function down()
    {
        $this->dropColumn('view', 'possition');
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
