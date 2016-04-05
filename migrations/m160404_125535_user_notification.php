<?php

use yii\db\Migration;

class m160404_125535_user_notification extends Migration
{
    public function up()
    {
        $this->createTable('user_notification', [
            'id' => $this->primaryKey(),
            'from' => $this->integer()->notNull(),
            'to' => $this->integer()->notNull(),
            'title' => $this->string(),
            'created_at' => $this->timestamp(),
            'message' => $this->text(),
            'notified' => $this->boolean()->defaultValue(0),
        ]);
        $this->addForeignKey('fk_user_notification_user_from', 'user_notification', 'from', 'user', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_user_notification_user_to', 'user_notification', 'to', 'user', 'id', 'cascade', 'cascade');
    }

    public function down()
    {
        $this->dropTable('user_notification');

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
