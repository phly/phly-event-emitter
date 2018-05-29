<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\EventEmitter;

use Psr\Event\Dispatcher\ListenerAggregateInterface;
use Psr\Event\Dispatcher\EventInterface;
use Zend\Stdlib\PriorityQueue;

class PrioritizedListenerAggregate implements ListenerAggregateInterface
{
    /**
     * @var \stdClass[]
     */
    protected $listeners = [];

    /**
     * {@inheritDoc}
     */
    public function getListenersForEvent(EventInterface $event): iterable
    {
        $queue = new PriorityQueue();
        foreach ($this->listeners as $listenerData) {
            if (! $event instanceof $listenerData->event) {
                continue;
            }

            $queue->insert($listenerData->listener, $listenerData->priority);
        }
        return $queue;
    }

    public function on(string $eventType, callable $listener, int $priority = 1) : void
    {
        $this->listeners[] = (object) [
            'event'    => $eventType,
            'listener' => $listener,
            'priority' => $priority,
        ];
    }
}
