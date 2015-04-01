<?php

use yii\db\Schema;
use yii\db\Migration;

class m150331_073031_user_log extends Migration
{
    public function up()
    {
    
        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = '
                CHARACTER SET utf8
                COLLATE utf8_slovak_ci ENGINE=InnoDB
            ';
        }

        $this->createTable('action_type', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
        ]);

        $this->batchInsert('action_type', ['id', 'name'], [
            ['id' => 1, 'name' => 'display'],
            ['id' => 2, 'name' => 'stay'],
            ['id' => 3, 'name' => 'focus'],
            ['id' => 4, 'name' => 'blur'],
            ['id' => 5, 'name' => 'leave'],
        ]);

        $this->createTable('action', [
            'id' => 'pk',
            'type_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'timestamp' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
        ], $tableOptions);

        $this->addForeignKey('fk_user_action',
            'action', 'user_id',
            'users', 'id',
            'CASCADE'
        );

        $this->addForeignKey('fk_document_action', 
            'action', 'document_id',
            'documents', 'id',
            'CASCADE'
        );

        $this->addForeignKey('fk_action_type', 
            'action', 'type_id',
            'action_type', 'id'
        );

        $this->createTable('query', [
            'id' => 'pk',
            'query' => Schema::TYPE_STRING . ' NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'timestamp' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
        ], $tableOptions);

        $this->addForeignKey('fk_query_user',
            'query', 'user_id',
            'users', 'id',
            'CASCADE'
        );

        $this->insert('users', [
            'id' => 0,
            'email' => 'guest@anonimous.sk',
            'password' => 'none',
            'role_id' => 0,
            'access_token' => 'no_access',
            'auth_key' => 'no_key',
            'username' => 'guest',
        ]);
    }

    public function down()
    {
        $this->dropForeignKey('fk_user_action', 'action');
        $this->dropForeignKey('fk_document_action', 'action');
        $this->dropForeignKey('fk_action_type', 'action');
        $this->dropTable('action_type');
        $this->dropTable('action');

        $this->dropForeignKey('fk_query_user', 'query');
        $this->dropTable('query');
        $this->delete('users', ['id' => 0]);
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
