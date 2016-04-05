<?php

namespace app\models;

use app\components\Renderable;
use Yii;
use yii\base\UnknownMethodException;
use yii\db\AfterSaveEvent;
use yii\helpers\Json;

/**
 * This is the model class for table "notification".
 *
 * @property integer      $id
 * @property string       $name
 * @property string       $event
 * @property integer      $from
 * @property integer      $to
 * @property string       $title
 * @property string       $message
 * @property string|array $type
 *
 * @property DbUser       $fromUser
 * @property DbUser       $toUser
 */
class Notification extends \yii\db\ActiveRecord
{
    const TYPE_EMAIL = 1;
    const TYPE_WEB = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'event', 'from', 'to', 'title', 'type'], 'required'],
            [['from', 'to'], 'integer'],
            [['message'], 'string'],
            [['name', 'event', 'title', 'type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'      => 'ID',
            'name'    => 'Name',
            'event'   => 'Event',
            'from'    => 'From',
            'to'      => 'To',
            'title'   => 'Title',
            'message' => 'Message',
            'type'    => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne(DbUser::className(), ['id' => 'from']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(DbUser::className(), ['id' => 'to']);
    }

    /**
     * Returns array of all users for notification form.
     *
     * @return mixed
     */
    public function getFromUsers()
    {
        $users = DbUser::find()->all();

        return array_reduce($users, function ($result, DbUser $user) {
            $result[$user->getId()] = $user->name;

            return $result;
        }, []);
    }

    /**
     * Returns array of all users for notification form.
     *
     * @return array
     */
    public function getToUsers()
    {
        $users = [
            -1 => 'Current',
            -2 => 'All',
        ];

        return $users + $this->getFromUsers();
    }

    /**
     * Returns receiver user email
     *
     * @return string
     */
    public function getTo()
    {
        switch ($this->to) {
            case -1:
                return 'Current';
            case -2:
                return 'All';
            default:
                return $this->toUser->email;
        }
    }

    /**
     * Renders message with applying params from model
     *
     * @param string     $message that should be rendered
     * @param Renderable $model
     * @param DbUser     $user
     * @return string
     */
    public function render($message, $model, $user)
    {
        if ($model instanceof Renderable) {
            $searchArrayKeys = [];
            $searchArrayVals = [];
            foreach ($model->getTemplateParams() as $key => $val) {
                $searchArrayKeys[] = '{' . $key . '}';
                $searchArrayVals[] = $val($model);
            }
            $searchArrayKeys[] = '{username}';
            $searchArrayVals[] = $user->name;

            return str_replace($searchArrayKeys, $searchArrayVals, $message);
        } else {
            throw new UnknownMethodException('Unknown method: ' . get_class($model) . "::getTemplateParams(). Should implement Renderable interface.");
        }
    }

    /**
     * @param Renderable $model
     * @param DbUser     $user
     * @return string
     */
    public function renderMessage($model, $user)
    {
        return $this->render($this->message, $model, $user);
    }

    /**
     * @param Renderable $model
     * @param DbUser     $user
     * @return string
     */
    public function renderTitle($model, $user)
    {
        return $this->render($this->title, $model, $user);
    }

    /**
     * Method that generates closure function for events
     *
     * @return \Closure
     */
    public function getHandler()
    {
        return function (AfterSaveEvent $data) {
            if ($this->to == -2) {
                /**
                 * @var $users DbUser[]
                 */
                $users = DbUser::find()->all();
                foreach ($users as $user) {
                    $this->send($data->sender, $this->fromUser, $user);
                }
            } else {
                if ($this->to == -1) {
                    $to = Yii::$app->user;
                } else {
                    $to = DbUser::findOne(['id' => $this->to]);
                }
                $this->send($data->sender, $this->fromUser, $to);
            }
        };
    }

    /**
     * Notification method that implement all possible notification types
     *
     * @todo refactor notifications to queue
     * @param Renderable $model
     * @param DbUser     $from
     * @param DbUser     $to
     */
    public function send($model, $from, $to)
    {
        $message = $this->renderMessage($model, $to);
        $title = $this->renderTitle($model, $to);
        foreach ($this->type as $type) {
            switch ($type) {
                case self::TYPE_EMAIL:
                    Yii::$app->mail->compose('default', ['content' => $message])
                                   ->setFrom([$from->email => 'News'])
                                   ->setTo([$to->email => $to->email])
                                   ->setSubject($title)
                                   ->send();
                    break;
                case self::TYPE_WEB:
                    $userNotification = new UserNotification();
                    $userNotification->from = $from->getId();
                    $userNotification->to = $to->getId();
                    $userNotification->message = $message;
                    $userNotification->title = $title;
                    $userNotification->save();
                    break;
            }
        }
    }

    /**
     * Possible notification types
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_EMAIL => 'Email',
            self::TYPE_WEB   => 'Web',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        /** Encoding notification type to string */
        if (is_array($this->type)) {
            $this->type = Json::encode($this->type);
        }

        return parent::beforeValidate();
    }

    /**
     * @inheritDoc
     */
    public function afterFind()
    {
        /** Decoding notification type to array of notifications */
        $json = Json::decode($this->type);
        if ($json) {
            $this->type = $json;
        }

        parent::afterFind();
    }


}
