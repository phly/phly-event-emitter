<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace PhlyTest\EventEmitter\TestAsset;

use Fig\EventDispatcher\StoppableTaskTrait;
use Psr\EventDispatcher\StoppableTaskInterface;
use Psr\EventDispatcher\TaskInterface;

class StoppableTask implements StoppableTaskInterface
{
    use StoppableTaskTrait;

    public function stopPropagation() : void
    {
        $this->stopPropagation = true;
    }
}
