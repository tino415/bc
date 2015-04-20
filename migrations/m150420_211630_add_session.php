<?php

use yii\db\Schema;
use yii\db\Migration;

class m150420_211630_add_session extends Migration
{
    public function up()
    {
        $this->createTable('session', [
            'id' => 'pk',
        ]);

        $this->insert('session', ['id' => 1]);

        $this->addColumn('view', 'session_id', Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1');
        
        $this->addForeignKey('fk_view_session',
            'view', 'session_id',
            'session', 'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_view_session', 'view');
        $this->dropColumn('view', 'session_id');
        $this->dropTable('session');
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
