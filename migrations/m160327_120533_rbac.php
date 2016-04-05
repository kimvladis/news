<?php

use yii\db\Migration;

class m160327_120533_rbac extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // add "createArticle" permission
        $createPost = $auth->createPermission('createArticle');
        $createPost->description = 'Create a article';
        $auth->add($createPost);

        // add "author" role and give this role the "createPost" permission
        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $createPost);

        $createNotification = $auth->createPermission('createNotification');
        $createNotification->description = 'Create a notification';
        $auth->add($createNotification);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $createNotification);
        $auth->addChild($admin, $author);
    }

    public function down()
    {
        echo "m160327_120533_rbac cannot be reverted.\n";

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
