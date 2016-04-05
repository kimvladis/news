<?php

namespace app\controllers;

use app\models\Notification;
use app\models\UserNotification;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * NotificationController implements the CRUD actions for Notification model.
 */
class NotificationController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaiors()
    {
        return [
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['index', 'view', 'create', 'update', 'delete', 'params', 'my', 'notified'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'params'],
                        'allow'   => true,
                        'roles'   => ['createNotification'],
                    ],
                    [
                        'actions' => ['my', 'notified'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Notification models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
                                                   'query' => Notification::find(),
                                               ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Notification model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Notification model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Notification();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model'  => $model,
                'events' => Yii::$app->events->getAll(),
            ]);
        }
    }

    /**
     * Updates an existing Notification model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model'  => $model,
                'events' => Yii::$app->events->getAll(),
            ]);
        }
    }

    /**
     * Deletes an existing Notification model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Notification model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Notification the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Notification::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Displays params for message template by event name.
     *
     * @param string $event event name
     * @return array of possible params
     */
    public function actionParams($event)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = Yii::$app->events->getModelByEvent($event);

        return array_keys(call_user_func($model . '::getTemplateParams'));
    }

    /**
     * Lists all user notifications.
     *
     * @return string
     */
    public function actionMy()
    {
        $query = UserNotification::find()
                                 ->where(['to' => Yii::$app->user->getId()])
                                 ->orderBy('created_at DESC');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);

        $models = $query->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();

        return $this->render('my', [
            'models' => $models,
            'pages'  => $pages,
        ]);
    }

    /**
     * Marks user notification as notified
     * Expects user notification id in $_POST['id']
     */
    public function actionNotified()
    {
        /**
         * @var $notification UserNotification
         */
        $notification = UserNotification::findOne(Yii::$app->request->post('id'));
        $notification->notified = 1;
        $notification->save();
    }
}
