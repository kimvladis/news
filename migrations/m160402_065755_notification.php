<?php

use yii\db\Migration;

class m160402_065755_notification extends Migration
{
    public function up()
    {
        $this->createTable('notification', [
            'id'         => $this->primaryKey(),
            'name'       => $this->string()->notNull(),
            'event'      => $this->string()->notNull(),
            'from'       => $this->integer()->notNull(),
            'to'         => $this->integer()->notNull(),
            'title'      => $this->string()->notNull(),
            'message'    => $this->text(),
            'type'       => $this->string()->notNull(),
        ]);
        $this->addForeignKey('fk_notification_user', 'notification', 'from', 'user', 'id', 'cascade', 'cascade');
    }

    public function down()
    {
        $this->dropTable('notifiaction');

        return true;
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
