<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace PhlyTest\EventEmitter;

use Phly\EventEmitter\ListenerAggregate;
use PHPUnit\Framework\TestCase;
use Psr\Event\Dispatcher\EventInterface;
use Psr\Event\Dispatcher\ListenerAggregateInterface;

class ListenerAggregateTest extends TestCase
{
    /** @var ListenerAggregate */
    protected $listeners;

    public function setUp()
    {
        $this->listeners = new ListenerAggregate();
    }

    public function createListener()
    {
        return function ($event) {
        };
    }

    public function testListenersAreEmptyByDefault()
    {
        $this->assertAttributeEmpty('listeners', $this->listeners);
    }

    public function testReturnsOnlyListenersForTheGivenEvent()
    {
        $listener1 = $this->createListener();
        $listener2 = $this->createListener();
        $listener3 = $this->createListener();

        $this->listeners->on(NonExistentEvent::class, $listener1);
        $this->listeners->on(TestAsset\TestEvent::class, $listener2);
        $this->listeners->on(EventInterface::class, $listener3);

        $event = new TestAsset\TestEvent();

        $listeners = iterator_to_array($this->listeners->getListenersForEvent($event));

        $this->assertContains($listener2, $listeners);
        $this->assertContains($listener3, $listeners);
        $this->assertNotContains($listener1, $listeners);
    }
}
