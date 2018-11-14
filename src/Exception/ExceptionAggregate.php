<?php
/**
 * @see       https://github.com/phly/phly-event-emitter for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney (https://mwop.net)
 * @license   https://github.com/phly/phly-event-emitter/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\EventEmitter\Exception;

use RuntimeException;

class ExceptionAggregate extends RuntimeException implements ExceptionInterface
{
    private $exceptions;

    public static function fromExceptions(array $exceptions) : self
    {
        $e = new self('One or more listeners raised an exception during notification');
        $e->exceptions = $exceptions;
        return $e;
    }

    public function getListenerExceptions() : array
    {
        return $this->exceptions;
    }
}
