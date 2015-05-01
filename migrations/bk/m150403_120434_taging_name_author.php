<?php

use yii\db\Schema;
use yii\db\Migration;

class m150403_120434_taging_name_author extends Migration
{
    public function safeUp()
    {
        $this->execute('INSERT INTO tags (name, type_id) SELECT DISTINCT(name), 3 FROM documents;');
        $this->execute('
            INSERT INTO map_documents_tags (document_id, tag_id)
            SELECT documents.id, tags.id FROM documents
            INNER JOIN tags ON tags.name = documents.name
            WHERE tags.type_id = 3;
        ');
        $this->execute('INSERT INTO tags (name, type_id) SELECT DISTINCT(name), 2 FROM interprets;');
        $this->execute('
            INSERT INTO map_documents_tags (document_id, tag_id)
            SELECT documents.id, tags.id FROM documents
            INNER JOIN interprets ON documents.interpret_id = interprets.id
            INNER JOIN tags ON interprets.name = tags.name
            WHERE tags.type_id = 2;
        ');

        $this->addForeignKey('fk_document_action',
            'action', 'document_id',
            'documents', 'id',
            'CASCADE'
        );

        $this->addForeignKey('fk_user_action',
            'action', 'user_id',
            'users', 'id',
            'CASCADE'
        );

        $this->addForeignKey('fk_action_type',
            'action', 'type_id',
            'action_type', 'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->execute('
            DELETE FROM tags
            WHERE name IN(
                SELECT DISTINCT(name) FROM documents
            ) AND type_id = 3;
        ');
        $this->execute('
            DELETE FROM tags
            WHERE name IN(
                SELECT DISTINCT(name) FROM interprets
            ) AND type_id = 2;
        ');
        $this->dropForeignKey('fk_document_action', 'action');
        $this->dropForeignKey('fk_user_action', 'action');
        $this->dropForeignKey('fk_action_type', 'action');
    }
}
