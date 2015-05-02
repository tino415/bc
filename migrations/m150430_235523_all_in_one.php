<?php

use yii\db\Schema;
use yii\db\Migration;

class m150430_235523_all_in_one extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_slovak_ci ENGINE=InnoDB';
        }

        $this->createTable('user', [
            'id' => 'pk',
            'username' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
            'email' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'access_token' => Schema::TYPE_STRING,
            'auth_key' => Schema::TYPE_STRING,
            'role_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 2',
        ], $tableOptions);

        $this->insert('user', [
            'id' => 1,
            'username' => 'admin',
            'email' => 'cernakmartin3@gmail.com',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('admin'),
            'access_token' => 'test_access_token',
            'auth_key' => 'test_auth_key',
            'role_id' => 8
        ]);

        $this->insert('user', [
            'id' => 2,
            'username' => 'guest',
            'email' => 'guest@guest',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('guest'),
            'access_token' => 'guest_token',
            'auth_key' => 'guest_key',
            'role_id' => '0',
        ]);

        $this->insert('user', [
            'id' => 3,
            'username' => 'demo',
            'email' => 'martin.cernak@iaeste.sk',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('demo'),
            'access_token' => 'test_access_token',
            'auth_key' => 'test_auth_key',
            'role_id' => '2',
        ]);

        $this->createTable('interpret', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
            'alias' => Schema::TYPE_STRING,
        ], $tableOptions);

        $this->createTable('document_type', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('tag', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
        ], $tableOptions);

        $this->createTable('session', [
            'id' => 'pk',
        ], $tableOptions);

        $this->createTable('query', [
            'id' => 'pk',
            'query' => Schema::TYPE_STRING,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
        ], $tableOptions);

        $this->addForeignKey( 'fk_query_user',
            'query', 'user_id',
            'user', 'id',
            'CASCADE'
        );

        $this->createTable('document', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'interpret_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'content' => Schema::TYPE_TEXT,
        ], $tableOptions);

        $this->addForeignKey( 'fk_document_type',
            'document', 'type_id',
            'document_type', 'id',
            'CASCADE'
        );

        $this->addForeignKey( 'fk_document_interpret',
            'document', 'interpret_id',
            'interpret', 'id',
            'CASCADE'
        );

        $this->createTable('map_document_tag', [
            'id' => 'pk',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'tag_id' => Schema::TYPE_INTEGER . ' NOT NULL', 
            'type_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'weight' => Schema::TYPE_FLOAT . ' NOT NULL DEFAULT 0',
            'count' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1',
            'UNIQUE(document_id, tag_id, type_id)',
        ], $tableOptions);

        $this->addForeignKey( 'fk_map_document_tag_document',
            'map_document_tag', 'document_id',
            'document', 'id',
            'CASCADE'
        );

        $this->addForeignKey( 'fk_map_document_tag_tag',
            'map_document_tag', 'tag_id',
            'tag', 'id',
            'CASCADE'
        );

        $this->createTable('schema', [
            'id' => 'pk',
            'content' => Schema::TYPE_STRING . ' NOT NULL',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'UNIQUE(content, document_id)',
        ], $tableOptions);

        $this->addForeignKey( 'fk_schema_document',
            'schema', 'document_id',
            'document', 'id',
            'CASCADE'
        );

        $this->createTable('view', [
            'id' => 'pk',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'tag_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'session_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
        ], $tableOptions);

        $this->addForeignKey( 'fk_view_document',
            'view', 'document_id',
            'document', 'id',
            'CASCADE'
        );

        $this->addForeignKey( 'fk_view_user',
            'view', 'user_id',
            'user', 'id',
            'CASCADE'
        );

        $this->addForeignKey( 'fk_view_tag',
            'view', 'tag_id',
            'tag', 'id',
            'CASCADE'
        );

        $this->addForeignKey( 'fk_view_session',
            'view', 'session_id',
            'session', 'id',
            'CASCADE'
        );

    }

    public function down()
    {
        echo "m150430_235523_all_in_one cannot be reverted.\n";

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
