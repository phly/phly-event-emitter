<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace PhlyTest\EventEmitter;

use Phly\EventEmitter\TaskProcessor;
use Phly\EventEmitter\ListenerProvider;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\TaskInterface;
use Psr\EventDispatcher\TaskProcessorInterface;

class TaskProcessorTest extends TestCase
{
    public function testImplementsTaskProcessorInterface()
    {
        $listeners = $this->prophesize(ListenerProviderInterface::class)->reveal();
        $processor = new TaskProcessor($listeners);
        $this->assertInstanceOf(TaskProcessorInterface::class, $processor);
    }

    public function testTriggersAllListenersWithTask()
    {
        $task = new TestAsset\TestTask();
        $counter = 0;

        $listeners = new ListenerProvider();
        for ($i = 0; $i < 5; $i += 1) {
            $listeners->on(TestAsset\TestTask::class, function ($e) use ($task, &$counter) {
                Assert::assertSame($task, $e);
                $counter += 1;
            });
        }

        $processor = new TaskProcessor($listeners);
        $this->assertSame($task, $processor->process($task));

        $this->assertEquals(5, $counter);
    }

    public function testShortCircuitsIfAListenerStopsEventPropagation()
    {
        $task = new TestAsset\StoppableTask();
        $counter = 0;

        $listeners = new ListenerProvider();
        for ($i = 0; $i < 5; $i += 1) {
            $listeners->on(TestAsset\StoppableTask::class, function ($e) use ($task, &$counter) {
                Assert::assertSame($task, $e);
                $counter += 1;
                if ($counter === 3) {
                    $e->stopPropagation();
                }
            });
        }

        $processor = new TaskProcessor($listeners);
        $this->assertSame($task, $processor->process($task));

        $this->assertEquals(3, $counter);
    }
}
