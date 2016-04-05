<?php

namespace tests\codeception\unit\components;

use app\components\Events;
use Codeception\Specify;
use yii\codeception\TestCase;

class EventsTest extends TestCase
{
    use Specify;

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @throws \Exception
     */
    public function testAddEvents()
    {
        $events = new Events();
        $events->add('TestModelA', 'testModelAEvent');
        $events->add('TestModelB', 'testModelBEvent');

        $this->assertEquals([
                                'TestModelA' => ['testModelAEvent' => 'testModelAEvent'],
                                'TestModelB' => ['testModelBEvent' => 'testModelBEvent'],
                            ], $events->getAll(), 'events should be stored');

        $this->assertEquals('TestModelB', $events->getModelByEvent('testModelBEvent'), 'model name should be returned');

        $this->assertFalse($events->getModelByEvent('testModelCEvent'), 'incorrect event name shold return false');
    }
}
