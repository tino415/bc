<?php

use yii\db\Schema;
use yii\db\Migration;

class m150401_112344_link_update extends Migration
{
    public function up()
    {
        $this->execute("
            UPDATE documents 
            SET link = CONCAT('http://www.supermusic.sk/', link)
        ");
    }

    public function down()
    {
        $this->execute("
            UPDATE documents
            SET link = REPLACE(link, 'http://www.supermusic.sk/', '')
        ");
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
