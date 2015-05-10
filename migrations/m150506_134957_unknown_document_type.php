<?php

use yii\db\Schema;
use yii\db\Migration;

class m150506_134957_unknown_document_type extends Migration
{
    public function up()
    {
        $this->insert('document_type', [
            'id' => 100,
            'name' => 'unknown',
        ]);
    }

    public function down()
    {
        echo "m150506_134957_unknown_document_type cannot be reverted.\n";

        return false;
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
