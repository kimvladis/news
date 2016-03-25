<?php
use \tests\codeception\_pages\LoginPage;
use \app\models\DbUser;

class LoginCest
{
    /** @var  LoginPage */
    protected $page;
    /** @var  DbUser */
    protected $user;

    public function _before(FunctionalTester $I)
    {
        $this->user = DbUser::findOne(['email' => 'demo@demo.com']);
        if (!$this->user) {
            $user = new DbUser();
            $user->email = 'demo@demo.com';
            $user->name = 'demo';
            $user->password = Yii::$app->security->generatePasswordHash('demo');
            $user->save();
            $user->verify();
            $this->user = $user;
        }


        $this->page = LoginPage::openBy($I);

        $I->see('Login', 'h1');
    }

    public function _after(FunctionalTester $I)
    {
        $this->user->delete();
    }

    public function loginTest(FunctionalTester $I)
    {
        $I->amGoingTo('try to login with empty credentials');
        $this->page->login('', '');
        $I->expectTo('see validations errors');
        $I->see('Email cannot be blank.');
        $I->see('Password cannot be blank.');

        $I->amGoingTo('try to login with wrong credentials');
        $this->page->login('admin', 'wrong');
        $I->expectTo('see validations errors');
        $I->see('Incorrect username or password.');

        $I->amGoingTo('try to login with correct credentials');
        $this->page->login('demo@demo.com', 'demo');
        $I->expectTo('see user info');
        $I->see('Logout (demo)');
    }
}