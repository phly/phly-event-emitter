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

class ListenerAggregate implements ListenerAggregateInterface
{
    /**
     * @var callable[][]
     */
    protected $listeners = [];

    /**
     * {@inheritDoc}
     */
    public function getListenersForEvent(EventInterface $event): iterable
    {
        foreach ($this->listeners as $listenerData) {
            if (! $event instanceof $listenerData->type) {
                continue;
            }

            yield $listenerData->listener;
        }
    }

    public function on(string $eventType, callable $listener) : void
    {
        $this->listeners[] = (object) [
            'type'     => $eventType,
            'listener' => $listener,
        ];
    }
}
