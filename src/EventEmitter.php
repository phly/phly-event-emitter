<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\EventEmitter;

use Psr\Event\Dispatcher\EmitterInterface;
use Psr\Event\Dispatcher\EventInterface;
use Psr\Event\Dispatcher\ListenerAggregateInterface;

class EventEmitter implements EmitterInterface
{
    /**
     * @var ListenerAggregateInterface
     */
    private $listeners;

    public function __construct(ListenerAggregateInterface $listeners)
    {
        $this->listeners = $listeners;
    }

    /**
     * {@inheritDoc}
     */
    public function emit(EventInterface $event): void
    {
        foreach ($this->listeners->getListenersForEvent($event) as $listener) {
            $listener($event);
            if ($event->isStopped()) {
                break;
            }
        }
    }
}
