<?php

use yii\db\Schema;
use yii\db\Migration;

class m150403_120434_taging_name_author extends Migration
{
    public function safeUp()
    {
        $this->execute('INSERT tags (name, type_id) SELECT DISTINCT(name), 3 FROM documents;');
        $this->execute('
            INSERT map_documents_tags (document_id, tag_id)
            SELECT documents.id, tags.id FROM documents
            INNER JOIN tags ON tags.name = documents.name
            WHERE tags.type_id = 3;
        ');
        $this->execute('INSERT tags (name, type_id) SELECT DISTINCT(name), 2 FROM interprets;');
        $this->execute('
            INSERT map_documents_tags (document_id, tag_id)
            SELECT documents.id, tags.id FROM documents
            INNER JOIN interprets ON documents.interpret_id = interprets.id
            INNER JOIN tags ON interprets.name = tags.name
            WHERE tags.type_id = 2;
        ');
    }

    public function down()
    {
        $this->execute('DELETE FROM tags WHERE name IN(SELECT DISTINCT(name) FROM documents) AND type_id = 3;');
        $this->execute('DELETE FROM tags WHERE name IN(SELECT DISTINCT(name) FROM interprets) AND type_id = 2;');
    }
}
