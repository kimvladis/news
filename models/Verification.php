<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "verification".
 *
 * @property integer $user_id
 * @property string  $key
 * @property string  $created_at
 *
 * @property DbUser  $user
 * @method static Verification|null findOne($condition)
 */
class Verification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'verification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['key'], 'string', 'max' => 255],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id'    => 'User ID',
            'key'        => 'Key',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(DbUser::className(), ['id' => 'user_id']);
    }
}