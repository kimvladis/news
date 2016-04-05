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
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $user = new DbUser();
        $user->sendVerificationEmail = false;
        $user->save();

        $this->assertArrayHasKey('email', $user->errors, 'error message should be set');

        $user->email = 'demo@demo.com';
        $user->save();

        $this->assertNotNull($user->id, 'user id can not be null');
        $this->assertEquals(0, $user->verified, 'user should be not verified');

        $user->verify();

        $this->assertEquals(1, $user->verified, 'user should be verified');
        $this->assertFalse(\Yii::$app->authManager->checkAccess($user->id, 'createArticle'), 'user should have not access to create article');

        $user->delete();
    }
}
