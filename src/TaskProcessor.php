<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\EventEmitter;

use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableTaskInterface;
use Psr\EventDispatcher\TaskInterface;
use Psr\EventDispatcher\TaskProcessorInterface;

class TaskProcessor implements TaskProcessorInterface
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
    public function process(TaskInterface $task): TaskInterface
    {
        $isStoppable = $task instanceof StoppableTaskInterface;
        foreach ($this->listeners->getListenersForEvent($task) as $listener) {
            $listener($task);
            if ($isStoppable && $task->isPropagationStopped()) {
                break;
            }
        }
        return $task;
    }
}
