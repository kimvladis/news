<?php

namespace app\models;

use app\components\Renderable;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer      $id
 * @property string       $name
 * @property string       $email
 * @property string       $password
 * @property string       $access_token
 * @property string       $auth_key
 * @property integer      $verified
 *
 * @property Article[]    $articles
 * @property Verification $verification
 *
 * @method static DbUser|null findOne($condition)
 */
class DbUser extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface, Renderable
{
    /**
     * @var bool if true sends confirmation email
     */
    public $sendVerificationEmail = true;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['verified'], 'integer'],
            [['name', 'email', 'password', 'auth_key', 'access_token'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['name', 'password'], 'required', 'on' => 'verify'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'name'     => 'Name',
            'email'    => 'Email',
            'password' => 'Password',
            'verified' => 'Verified',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['author_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVerification()
    {
        return $this->hasOne(Verification::className(), ['user_id' => 'id']);
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString();
            }

            return true;
        }

        return false;
    }

    /**
     * Verify user and assign role
     *
     * @return bool
     */
    public function verify()
    {
        if ($this->isNewRecord || $this->verified == 1) {
            return false;
        }

        $this->verified = 1;

        /** set default user role
         * $auth = Yii::$app->authManager;
         * $author = $auth->getRole('author');
         * $auth->assign($author, $this->id);
         */

        return $this->save();
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        $auth = Yii::$app->authManager;
        $auth->revokeAll($this->id);

        parent::afterDelete();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            if (!$this->password) {
                $verification = new Verification();
                $verification->user_id = $this->getId();
                $verification->key = Yii::$app->security->generateRandomString();
                if ($verification->save() && $this->sendVerificationEmail) {
                    Yii::$app->mail->compose('welcome', ['verification' => $verification])
                                   ->setFrom(['noreply@news.local' => 'News'])
                                   ->setTo([$this->email => $this->email])
                                   ->setSubject('Hello')
                                   ->send();
                }
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @param $username
     * @return DbUser|null
     */
    public static function findByUsername($username)
    {
        return self::findOne(['email' => $username]);
    }

    /**
     * @inheritdoc
     */
    public static function getTemplateParams()
    {
        return [
            'id'   => function (DbUser $model) {
                return $model->id;
            },
            'name' => function (DbUser $model) {
                return $model->name;
            },
        ];
    }

    /**
     * Returns count of user notifications
     *
     * @return int|string
     */
    public function getNotificationsCount()
    {
        return UserNotification::find()->where(['to' => $this->getId(), 'notified' => 0])->count();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value'              => new Expression('NOW()'),
            ],
        ];
    }
}