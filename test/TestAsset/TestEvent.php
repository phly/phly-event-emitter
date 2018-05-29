<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

namespace PhlyTest\EventEmitter\TestAsset;

use Psr\Event\Dispatcher\EventArgumentsInterface;

use Psr\Event\Dispatcher\EventInterface;
use Psr\Event\Dispatcher\EventTrait;

class TestEvent implements EventInterface
{
    use EventTrait;
}
