<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\EventEmitter;

use Psr\EventDispatcher\EventInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
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
            if ($event instanceof $listenerData->type) {
                yield $listenerData->listener;
            }
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
