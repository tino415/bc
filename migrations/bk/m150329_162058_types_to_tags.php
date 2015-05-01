<?php

use yii\db\Schema;
use yii\db\Migration;

class m150329_162058_types_to_tags extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_slovak_ci ENGINE=InnoDB';
        }

        $this->createTable('interprets', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
        ], $tableOptions);

        $this->createTable('tags', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
        ], $tableOptions);

        $this->createTable('map_users_tags', [
            'id' => 'pk',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'tag_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'UNIQUE(user_id, tag_id)',
        ], $tableOptions);

        $this->addForeignKey( 'fk_document_has_interpret',
            'documents', 'interpret_id',
            'interprets', 'id',
            'CASCADE'
        );

        $this->addForeignKey('fk_map_user_tag', 
            'map_users_tags', 'user_id',
            'users', 'id',
            'CASCADE'
        );

        $this->addForeignKey('fk_map_user_tag_tag',
            'map_users_tags', 'tag_id',
            'tags', 'id',
            'CASCADE'
        );

        $this->createTable('map_documents_tags', [
            'id' => 'pk',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'tag_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'UNIQUE(document_id, tag_id)',
        ], $tableOptions);

        $this->addForeignKey('fk_map_document_tag',
            'map_documents_tags', 'document_id',
            'documents', 'id',
            'CASCADE'
        );

        $this->addForeignKey('fk_map_document_tag_tag',
            'map_documents_tags', 'tag_id',
            'tags', 'id',
            'CASCADE'
        );

    }

    public function down()
    {
        $this->dropForeignKey('fk_document_has_interpret', 'documents');
        $this->dropForeignKey('fk_map_user_tag', 'map_users_tags');
        $this->dropForeignKey('fk_map_user_tag_tag', 'map_users_tags');
        $this->dropForeignKey('fk_map_document_tag', 'map_documents_tags');
        $this->dropForeignKey('fk_map_document_tag_tag', 'map_documents_tags');

        $this->dropTable('tags');
        $this->dropTable('map_users_tags');
        $this->dropTable('map_documents_tags');
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
