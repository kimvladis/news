<?php

namespace app\models;

use app\components\Renderable;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string  $title
 * @property string  $content
 * @property string  $photo
 * @property integer $author_id
 * @property string  $created_at
 *
 * @property DbUser  $author
 */
class Article extends \yii\db\ActiveRecord implements Renderable
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['content'], 'string'],
            [['author_id'], 'integer'],
            [['created_at'], 'safe'],
            [['title', 'photo'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'extensions' => 'png, jpg, jpeg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'title'      => 'Title',
            'content'    => 'Content',
            'photo'      => 'Photo',
            'author_id'  => 'Author ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(DbUser::className(), ['id' => 'author_id']);
    }

    /**
     * Upload photo to server
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->validate() && $this->imageFile instanceof UploadedFile) {
            $this->imageFile->name = md5_file($this->imageFile->tempName) . '.' . $this->imageFile->getExtension();
            $this->photo = $this->imageFile->name;
            $this->imageFile->saveAs('uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->author_id = Yii::$app->user->getId();
            }
            $this->upload();

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public static function getTemplateParams()
    {
        return [
            'title'     => function (Article $data) {
                return $data->title;
            },
            'shortText' => function (Article $data) {
                return StringHelper::truncateWords($data->content, 50);
            },
            'url'       => function (Article $data) {
                return Url::to(['article/view', 'id' => $data->id], true);
            },
        ];
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