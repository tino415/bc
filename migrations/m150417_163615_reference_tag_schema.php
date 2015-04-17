<?php

use yii\db\Schema;
use yii\db\Migration;

class m150417_163615_reference_tag_schema extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_slovak_ci ENGINE=InnoDB';
        }

        $this->execute('DELETE FROM tag');

        $this->dropForeignKey('fk_documnet_tag', 'tag');

        $this->dropColumn('tag', 'document_id');

        $this->createTable('view', [
            'id' => 'pk',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'tag_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'view_timestamp' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
        ], $tableOptions);

        $this->addForeignKey('fk_view_document',
            'view', 'document_id',
            'document', 'id',
            'CASCADE'
        );

        $this->addForeignKey('fk_view_user',
            'view', 'user_id',
            'user', 'id',
            'CASCADE'
        );

        $this->addForeignKey('fk_view_tag',
            'view', 'tag_id',
            'tag', 'id',
            'CASCADE'
        );

    }
    
    public function safeDown()
    {
    }
}
