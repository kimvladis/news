<?php
namespace app\components;

use app\models\Article;
use app\models\DbUser;
use app\models\Notification;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;

/**
 * Class EventsBootstrap
 *
 * @package app\components
 */
class EventsBootstrap implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $this->add(
            DbUser::className(),
            ActiveRecord::EVENT_AFTER_INSERT,
            'newUser',
            function () {
                return true;
            }
        );
        $this->add(
            DbUser::className(),
            ActiveRecord::EVENT_AFTER_UPDATE,
            'userVerified',
            function ($data) {
                return (isset($data->changedAttributes['verified']) && $data->changedAttributes['verified'] == 1);
            }
        );
        $this->add(
            Article::className(),
            ActiveRecord::EVENT_AFTER_UPDATE,
            'articleTitleChanged',
            function (AfterSaveEvent $data) {
                return (isset($data->changedAttributes['title']));
            }
        );
        $this->add(
            Article::className(),
            ActiveRecord::EVENT_AFTER_INSERT,
            'newArticle',
            function () {
                return true;
            }
        );

        /**
         * Attaching created notifications
         *
         * @var $notifications Notification[]
         */
        $notifications = Notification::find()->all();
        foreach ($notifications as $notification) {
            Event::on(
                Yii::$app->events->getModelByEvent($notification->event),
                $notification->event,
                $notification->getHandler()
            );
        }
    }

    /**
     * The method for create new event
     *
     * @param string   $className class that will trigger an event
     * @param string   $initEvent initial event that would be listened for trigger a new event
     * @param string   $newEvent  new event name
     * @param \Closure $filter    filter that would be apply before trigger new event
     */
    protected function add($className, $initEvent, $newEvent, $filter)
    {
        Yii::$app->events->add($className, $newEvent);
        Event::on($className, $initEvent, function ($data) use ($className, $newEvent, $filter) {
            if ($filter($data)) {
                Event::trigger($className, $newEvent, $data);
            }
        });
    }
}