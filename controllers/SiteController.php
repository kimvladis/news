<?php

namespace app\controllers;

use app\models\Article;
use app\models\DbUser;
use app\models\LoginForm;
use app\models\Verification;
use kartik\markdown\Markdown;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays news feed
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Article::find()->orderBy('created_at DESC');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);

        if (Yii::$app->user->isGuest) {
            $pages->totalCount = 10;
        }

        $models = $query->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();

        return $this->render('index', [
            'models' => $models,
            'pages'  => $pages,
        ]);
    }

    /**
     * Login form
     *
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (Yii::$app->user->can('createArticle')) {
                return $this->redirect('article/index');
            } else {
                return $this->redirect('notification/my');
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Sign up form
     *
     * @return string|\yii\web\Response
     */
    public function actionSignup()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new DbUser();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->render('finish', [
                'model' => $model,
            ]);
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Verify account form
     *
     * @param string $key from email
     * @return string|\yii\web\Response
     * @throws HttpException
     * @throws \yii\base\Exception
     */
    public function actionVerify($key)
    {
        $verification = Verification::findOne(['key' => $key]);

        if ($verification) {
            $user = DbUser::findOne($verification->user_id);
            $user->setScenario('verify');

            if ($user->load(Yii::$app->request->post())) {
                $user->password = Yii::$app->security->generatePasswordHash($user->password);

                if ($user->verify()) {
                    Yii::$app->user->login($user, 0);

                    if (Yii::$app->user->can('createArticle')) {
                        return $this->redirect('article/index');
                    } else {
                        return $this->redirect('notification/my');
                    }
                }
            }

            return $this->render('verify', ['model' => $user]);
        } else {
            throw new HttpException(404, "The key can not be found.");
        }
    }

    /**
     * Logout current user
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * RSS link
     *
     * @throws \Exception
     */
    public function actionRss()
    {
        $dataProvider = new ActiveDataProvider(
            [
                'query'      => Article::find()->with(['author']),
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);

        $response = Yii::$app->getResponse();
        $headers = $response->getHeaders();

        $headers->set('Content-Type', 'application/rss+xml; charset=utf-8');

        echo \Zelenin\yii\extensions\Rss\RssView::widget(
            [
                'dataProvider' => $dataProvider,
                'channel'      => [
                    'title'       => Yii::$app->name,
                    'link'        => Url::toRoute('/', true),
                    'description' => 'News ',
                    'language'    => function ($widget, \Zelenin\Feed $feed) {
                        return Yii::$app->language;
                    },
                    'image'       => function ($widget, \Zelenin\Feed $feed) {
                        $feed->addChannelImage('http://ru.webinames.com/wp-content/uploads/2015/09/news.jpg', 'http://news.local', 88, 31, 'News');
                    },
                ],
                'items'        => [
                    'title'       => function ($model, $widget, \Zelenin\Feed $feed) {
                        return $model->title;
                    },
                    'description' => function ($model, $widget, \Zelenin\Feed $feed) {
                        return StringHelper::truncateWords(Markdown::convert($model->content), 50);
                    },
                    'link'        => function ($model, $widget, \Zelenin\Feed $feed) {
                        return Url::toRoute(['article/view', 'id' => $model->id], true);
                    },
                    'author'      => function ($model, $widget, \Zelenin\Feed $feed) {
                        return $model->author->name;
                    },
                    'guid'        => function ($model, $widget, \Zelenin\Feed $feed) {
                        return Url::toRoute(['article/view', 'id' => $model->id], true);
                    },
                    'pubDate'     => function ($model, $widget, \Zelenin\Feed $feed) {
                        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $model->created_at);

                        return $date->format(DATE_RSS);
                    },
                ],
            ]
        );
    }

    public function actionEvents()
    {
        var_dump(Yii::$app->events->getAll());
//        Yii::$app->events->add();
//        Yii::$app->init()
//        $article = new Article();
////        $c = new Component();
////        $getEvents = function (Article $a) {
////            return $a->_events;
////        };
////        $getEvents = \Closure::bind($getEvents, null, $article);
//
//        $sweetsThief = new \ReflectionProperty('yii\base\Component', '_events');
//        $sweetsThief->setAccessible(true);
//
//        var_dump($sweetsThief->getValue($article));
    }
}
