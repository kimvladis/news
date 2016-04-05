<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "user_notification".
 *
 * @property integer $id
 * @property integer $from
 * @property integer $to
 * @property string  $title
 * @property string  $created_at
 * @property string  $message
 * @property integer $notified
 *
 * @property DbUser  $fromUser
 * @property DbUser  $toUser
 */
class UserNotification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to'], 'required'],
            [['from', 'to', 'notified'], 'integer'],
            [['created_at'], 'safe'],
            [['message'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'from'       => 'From',
            'to'         => 'To',
            'title'      => 'Title',
            'created_at' => 'Created At',
            'message'    => 'Message',
            'notified'   => 'Notified',
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
