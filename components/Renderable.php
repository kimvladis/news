<?php
namespace app\components;

/**
 * Renderable is the interface that should be implemented by models who want to participate in notifications.
 *
 * The method [[getTemplateParams()]] will be invoked by an notification at the render method.
 *
 * The method [[getTemplateParams()]] should return array of passable params:
 *
 * ```php
 *  return [
 *      'param1' => function($model) { return $model->param1; },
 *      'param2' => function($model) { return doSomethingWith($model->param3); },
 *  ];
 * ```
 */
interface Renderable
{
    /**
     * Method to be called during rendering.
     *
     * @return array
     */
    public static function getTemplateParams();
}