<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace PhlyTest\EventEmitter;

use Phly\EventEmitter\EventEmitter;
use Phly\EventEmitter\ListenerAggregate;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Event\Dispatcher\EmitterInterface;
use Psr\Event\Dispatcher\EventInterface;
use Psr\Event\Dispatcher\ListenerAggregateInterface;

class EventEmitterTest extends TestCase
{
    public function testImplementsEmitterInterface()
    {
        $listeners = $this->prophesize(ListenerAggregateInterface::class)->reveal();
        $emitter = new EventEmitter($listeners);
        $this->assertInstanceOf(EmitterInterface::class, $emitter);
    }

    public function testTriggersAllListenersWithEvent()
    {
        $event = new TestAsset\TestEvent();
        $counter = 0;

        $listeners = new ListenerAggregate();
        for ($i = 0; $i < 5; $i += 1) {
            $listeners->on(TestAsset\TestEvent::class, function ($e) use ($event, &$counter) {
                Assert::assertSame($event, $e);
                $counter += 1;
            });
        }

        $emitter = new EventEmitter($listeners);
        $this->assertNull($emitter->emit($event));

        $this->assertEquals(5, $counter);
    }

    public function testShortCircuitsIfAListenerStopsEventPropagation()
    {
        $event = new TestAsset\TestEvent();
        $counter = 0;

        $listeners = new ListenerAggregate();
        for ($i = 0; $i < 5; $i += 1) {
            $listeners->on(TestAsset\TestEvent::class, function ($e) use ($event, &$counter) {
                Assert::assertSame($event, $e);
                $counter += 1;
                if ($counter === 3) {
                    $e->stopPropagation();
                }
            });
        }

        $emitter = new EventEmitter($listeners);
        $this->assertNull($emitter->emit($event));

        $this->assertEquals(3, $counter);
    }
}
