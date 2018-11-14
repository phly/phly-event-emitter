<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace PhlyTest\EventEmitter;

use Phly\EventEmitter\Exception;
use Phly\EventEmitter\ListenerProvider;
use Phly\EventEmitter\MessageNotifier;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\MessageInterface;
use Psr\EventDispatcher\MessageNotifierInterface;
use RuntimeException;
use Throwable;

class MessageNotifierTest extends TestCase
{
    public function setUp()
    {
        $this->provider = new ListenerProvider();
    }

    public function testMessageNotifierNotifiesAllListenersRelevantToMessage()
    {
        $expected = 5;
        $counter  = (object) ['count' => 0];
        $listener = function (MessageInterface $message) use ($counter) {
            $counter->count += 1;
        };

        for ($i = 0; $i < $expected; $i += 1) {
            $this->provider->on(TestAsset\TestMessage::class, $listener);
            $this->provider->on(TestAsset\TestTask::class, $listener);
        }

        $notifier = new MessageNotifier($this->provider);
        $message  = new TestAsset\TestMessage();

        $this->assertNull($notifier->notify($message));
        $this->assertSame($expected, $counter->count);
    }

    public function testMessageNotifierRaisesExceptionAggregatingAllCaughtExceptions()
    {
        $expected = 5;

        $goodCounter  = (object) ['count' => 0];
        $goodListener = function (MessageInterface $message) use ($goodCounter) {
            $goodCounter->count += 1;
        };

        $badCounter  = (object) ['count' => 0];
        $badListener = function (MessageInterface $message) use ($badCounter) {
            $badCounter->count += 1;
            throw new RuntimeException(sprintf('I threw for the %d time', $badCounter->count));
        };

        for ($i = 0; $i < $expected; $i += 1) {
            $this->provider->on(TestAsset\TestMessage::class, $goodListener);
            $this->provider->on(TestAsset\TestMessage::class, $badListener);
        }

        $notifier = new MessageNotifier($this->provider);
        $message  = new TestAsset\TestMessage();

        try {
            $notifier->notify($message);
        } catch (Throwable $e) {
        }

        if (! isset($e)) {
            $this->fail('Exceptions should be caught and re-raised by the notifier as an aggregate');
        }

        $this->assertSame($expected, $goodCounter->count);
        $this->assertSame($expected, $badCounter->count);
        $this->assertInstanceOf(Exception\ExceptionAggregate::class, $e);
        $exceptions = $e->getListenerExceptions();
        $this->assertCount(5, $exceptions);
    }
}
