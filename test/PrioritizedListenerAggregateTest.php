<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace PhlyTest\EventEmitter;

use Phly\EventEmitter\PrioritizedListenerAggregate;
use PHPUnit\Framework\TestCase;
use Psr\Event\Dispatcher\EventInterface;
use Psr\Event\Dispatcher\ListenerAggregateInterface;

class PrioritizedListenerAggregateTest extends TestCase
{
    /** @var PrioritizedListenerAggregate */
    protected $listeners;

    public function setUp()
    {
        $this->listeners = new PrioritizedListenerAggregate();
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

    public function testReturnsOnlyListenersForTheGivenEventInPriorityOrder()
    {
        $listener1 = $this->createListener();
        $listener2 = $this->createListener();
        $listener3 = $this->createListener();

        $this->listeners->on(NonExistentEvent::class, $listener1, 100);
        $this->listeners->on(TestAsset\TestEvent::class, $listener2, -100);
        $this->listeners->on(EventInterface::class, $listener3, 100);

        $event = new TestAsset\TestEvent();

        foreach ($this->listeners->getListenersForEvent($event) as $listener) {
            $listeners[] = $listener;
        }

        $this->assertSame([
            $listener3,
            $listener2,
        ], $listeners);
    }
}
