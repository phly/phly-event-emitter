<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\EventEmitter;

use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\MessageInterface;
use Psr\EventDispatcher\MessageNotifierInterface;
use Throwable;

class MessageNotifier implements MessageNotifierInterface
{
    /**
     * @var ListenerProviderInterface
     */
    private $listeners;

    public function __construct(ListenerProviderInterface $listeners)
    {
        $this->listeners = $listeners;
    }

    /**
     * {@inheritDoc}
     */
    public function notify(MessageInterface $message) : void
    {
        $position   = 0;
        $exceptions = [];

        foreach ($this->listeners->getListenersForEvent($message) as $listener) {
            try {
                $listener($message);
            } catch (Throwable $e) {
                $exceptions[$position] = $e;
            }

            $position += 1;
        }

        if ([] !== $exceptions) {
            throw Exception\ExceptionAggregate::fromExceptions($exceptions);
        }
    }
}
