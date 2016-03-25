<?php

namespace tests\codeception\unit\models;

use app\models\DbUser;
use Codeception\Specify;
use yii\codeception\TestCase;

class UserTest extends TestCase
{
    use Specify;

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @throws \Exception
     * @group tmp
     */
    public function testCreateUser()
    {
        $user = new DbUser();
        $user->sendVerificationEmail = false;
        $user->save();

        $this->specify('user can not created without email', function () use ($user) {
            expect('error message should be set', $user->errors)->hasKey('email');
        });

        $user->email = 'demo@demo.com';
        $user->save();

        $this->specify('user created', function () use ($user) {
            expect('user id can not be null', $user->id)->notNull();
            expect('user should be not verified', $user->verified)->equals(0);
        });

        $user->verify();

        $this->specify('user verified', function () use ($user) {
            expect('user should be verified', $user->verified)->equals(1);
            expect('user should have access to create article', \Yii::$app->authManager->checkAccess($user->id, 'createArticle'))->true();
        });

        $user->delete();
    }
}
