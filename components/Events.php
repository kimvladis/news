<?php
namespace app\components;

use Yii;
use yii\base\Component;

/**
 * Events is an application component that stores all the possible events.
 */
class Events extends Component
{
    protected $_events = [];

    /**
     * Method for adding new event.
     *
     * @todo class name validation
     * @param string $class class that triggers event
     * @param string $eventName
     */
    public function add($class, $eventName)
    {
        if (isset($this->_events[$class])) {
            $this->_events[$class][$eventName] = $eventName;
        } else {
            $this->_events[$class] = [$eventName => $eventName];
        }
    }

    /**
     * Gets all events
     *
     * @return array
     */
    public function getAll()
    {
        return $this->_events;
    }

    /**
     * Find the model by event name
     * Returns the model class name.
     * Returns FALSE if the needle was not found.
     *
     * @param $eventName
     * @return bool|string
     */
    public function getModelByEvent($eventName)
    {
        foreach ($this->_events as $model => $modelEvents) {
            foreach ($modelEvents as $event) {
                if ($event == $eventName) {
                    return $model;
                }
            }
        }

        return false;
    }
}