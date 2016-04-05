<?php

use yii\db\Migration;

class m160308_101319_init extends Migration
{
    public function up()
    {
        $this->createTable('user', [
            'id'         => $this->primaryKey(),
            'name'       => $this->string()->notNull(),
            'email'      => $this->string()->notNull()->unique(),
            'password'   => $this->string(),
            'auth_key'   => $this->string(),
            'access_token' => $this->string(),
            'verified'   => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp(),
        ]);

        $this->createTable('article', [
            'id'         => $this->primaryKey(),
            'title'      => $this->string()->notNull(),
            'content'    => $this->text(),
            'photo'      => $this->string(),
            'author_id'  => $this->integer(),
            'created_at' => $this->timestamp(),
        ]);
        $this->addForeignKey('fk_article_user', 'article', 'author_id', 'user', 'id', 'cascade', 'cascade');

        $this->createTable('verification', [
            'user_id'    => $this->integer()->notNull()->unique(),
            'key'        => $this->string()->notNull(),
            'created_at' => $this->timestamp(),
        ]);
        $this->addForeignKey('fk_verification_user', 'verification', 'user_id', 'user', 'id', 'cascade', 'cascade');
    }

    public function down()
    {
        $this->dropTable('article');
        $this->dropTable('verification');
        $this->dropTable('user');

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
