<?php

use yii\db\Schema;
use yii\db\Migration;

class m150405_104118_document_update extends Migration
{
    
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_slovak_ci ENGINE=InnoDB';
        }

        $this->renameTable('documents', 'document');
        $this->renameTable('users', 'user');
        $this->renameTable('interprets', 'interpret');
        $this->renameTable('tags' , 'tag');
        $this->renameTable('map_documents_tags', 'map_document_tag');
        $this->renameTable('map_users_tags', 'map_user_tag');

        $this->dropColumn('document', 'link');
        $this->addColumn('document', 'type_id', Schema::TYPE_INTEGER);

        $this->addColumn('interpret', 'alias', Schema::TYPE_STRING);

        $this->createTable('document_type', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING. ' NOT NULL UNIQUE',
        ], $tableOptions);

        $this->addForeignKey('fk_document_type',
            'document', 'type_id',
            'document_type', 'id',
            'CASCADE'
        );
        $this->insert('interpret', ['name' => '???']);
    }
    
    public function safeDown()
    {
        $this->delete('interpret', ['name' => '???']);
        $this->addColumn('document', 'link', Schema::TYPE_STRING . ' NOT NULL UNIQUE');
        $this->dropForeignKey('fk_document_type', 'document');

        $this->dropColumn('document', 'type_id');
        $this->dropColumn('interpret', 'alias');

        $this->dropTable('document_type');

        $this->renameTable('document', 'documents');
        $this->renameTable('user', 'users');
        $this->renameTable('interpret', 'interprets');
        $this->renameTable('tag' , 'tags');
        $this->renameTable('map_document_tag', 'map_documents_tags');
        $this->renameTable('map_user_tag', 'map_users_tags');

    }
}
