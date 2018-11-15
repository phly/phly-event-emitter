# phly-event-emitter

[![Build Status](https://secure.travis-ci.org/phly/phly-event-emitter.svg?branch=master)](https://secure.travis-ci.org/phly/phly-event-emitter)
[![Coverage Status](https://coveralls.io/repos/github/phly/phly-event-emitter/badge.svg?branch=master)](https://coveralls.io/github/phly/phly-event-emitter?branch=master)

> ## Experimental!
>
> This library is experimental, tracking different iterations and experiments
> being proposed for PSR-14. It is highly unstable in terms of API; use at your
> own risk.

This library provides an implementation of the following proposed PSR-14 interfaces:

- `ListenerProvider` implements `ListenerProviderInterface`, and allows you to
  attach listeners to any message type. It then acts as a generator, looping
  through each listener and testing if it handles the message type.

- `PrioritizedListenerProvider` also implements `ListenerProviderInterface`,
  and allows you to attach listeners to any message type, with an integer
  _priority_. When listeners are retrieved, it loops through all attached
  listeners, and injects those capable of listening to the emitted message to a
  priority queue, which it then returns.

- `MessageNotifier` implements `MessageNotifierInterface`, and accepts a
  `ListenerProviderInterface` to its constructor. It then loops through
  and notifies listeners returned for the message. If any listeners throw
  exceptions, it catches them, and, when all listeners have been notified,
  throws a `Phly\EventEmitter\Exception\ExceptionAggregate` that aggregates all
  of them; call the `getListenerExceptions()` method of that class to iterate
  through them.

- `TaskProcessor` implements `TaskProcessorInterface`, and accepts a
  `ListenerProviderInterface` to its constructor. It then loops through
  and processes listeners returned for the task, halting early if the
  task is stoppable and indicates propagation has been stopped. Exceptions
  thrown by listeners are not caught.

It DOES NOT provide implementations for the following interfaces:

- `EventInterface` (consumers will create these)
- `MessageInterface` (consumers will create these)
- `TaskInterface` (consumers will create these)
- `StoppableTaskInterface` (consumers will create these)

## Installation

You will first need to add a repository entry to your `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/phly/phly-event-emitter.git"
    }
],
```

Then, run the following to install this library:

```bash
$ composer require phly/phly-event-emitter
```

## Documentation

### Basic usage

The following demonstrates using the `ListenerProvider` to attach a listener.
The provider is then used to seed either a `MessageNotifier` or `TaskProcessor`.

```php
use Phly\EventEmitter\MessageNotifier;
use Phly\EventEmitter\ListenerProvider;

$listeners = new ListenerProvider();
$listeners->on(BootstrapEvent::class, function ($e) {
    // do something with the bootstrap event
});

$notifier = new MessageNotifier($listeners);
$notifier->notify(new BootstrapEvent($params));
```

### Prioritized listeners

The following example uses a `PrioritizedListenerProvider` to provide three
different listeners, each with a different priority. Priorities are integers;
higher priorities execute first, while lower priorities (including _negative_
priorities) execute last.

```php
use Phly\EventEmitter\TaskProcessor;
use Phly\EventEmitter\PrioritizedListenerProvider;

$listeners = new PrioritizedListenerProvider();
$listeners->on(BootstrapTask::class, function ($e) {
    echo 1, PHP_EOL;
}, -100);
$listeners->on(BootstrapTask::class, function ($e) {
    echo 2, PHP_EOL;
}, 100);
$listeners->on(BootstrapTask::class, function ($e) {
    echo 3, PHP_EOL;
}, 1);

$processor = new TaskProcessor($listeners);
$processor->process(new BootstrapTask($params));
```

In the above, the output will become:

```text
2
3
1
```

## Support

* [Issues](https://github.com/phly/phly-event-emitter/issues/)
