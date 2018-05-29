# phly-event-emitter

[![Build Status](https://secure.travis-ci.org/phly/phly-event-emitter.svg?branch=master)](https://secure.travis-ci.org/phly/phly-event-emitter)
[![Coverage Status](https://coveralls.io/repos/github/phly/phly-event-emitter/badge.svg?branch=master)](https://coveralls.io/github/phly/phly-event-emitter?branch=master)

> ## Experimental!
>
> This library is experimental, tracking different iterations and experiments
> being proposed for PSR-14. It is highly unstable in terms of API; use at your
> own risk.

This library provides an implementation of two proposed PSR-14 interfaces:

- `EventEmitter` implements `EmitterInterface`, and accepts a
  `ListenerAggregateInterface` to its constructor. It then loops through
  and triggers listeners returned for the event emitted, halting early if the
  event indicates propagation has been stopped.

- `ListenerAggregate` implements `ListenerAggregateInterface`, and allows you to
  attach listeners to any event type. It then acts as a generator, looping
  through each listener and testing it against the emitted type.

- `PrioritizedListenerAggregate` also implements `ListenerAggregateInterface`,
  and allows you to attach listeners to any event type, with an integer
  _priority_. When listeners are retrieved, it loops through all attached
  listeners, and injects those capable of listening to the emitted event to a
  priority queue, which it then returns.

It DOES NOT provide the following interfaces:

- `EventInterface` (consumers will create these)
- `EventArgumentsInterface` (consumers will create these, or use the PSR-14
  `EventArguments` implementation)
- `AttachableListenerAggregateInterface`
- `PositionableListenerAggregateInterface`
- `PrioritizedListenerAggregateInterface`
- `ReflectableListenerAggregateInterface`

The latter four may or may not be in the final spec; users and implementors can
always implement the more generic `ListenerAggregateInterface`, as I have done
here.

## Installation

Run the following to install this library:

```bash
$ composer require phly/phly-event-emitter
```

## Documentation

### Basic usage

The following demonstrates using the `ListenerAggregate` to attach a listener.
The aggregate is then used to seed an `EventEmitter`, which we then use to emit
an event:

```php
use Phly\EventEmitter\EventEmitter;
use Phly\EventEmitter\ListenerAggregate;

$listeners = new ListenerAggregate();
$listeners->on(BootstrapEvent::class, function ($e) {
    // do something with the bootstrap event
});

$emitter = new EventEmitter($listeners);
$emitter->emit(new BootstrapEvent($params));
```

### Prioritized listeners

The following example uses a `PrioritizedListenerAggregate` to aggregate three
different listeners, each with a different priority. Priorities are integers;
higher priorities execute first, while lower priorities (including _negative_
priorities) execute last.

```php
use Phly\EventEmitter\EventEmitter;
use Phly\EventEmitter\PrioritizedListenerAggregate;

$listeners = new PrioritizedListenerAggregate();
$listeners->on(BootstrapEvent::class, function ($e) {
    echo 1, PHP_EOL;
}, -100);
$listeners->on(BootstrapEvent::class, function ($e) {
    echo 2, PHP_EOL;
}, 100);
$listeners->on(BootstrapEvent::class, function ($e) {
    echo 3, PHP_EOL;
}, 1);

$emitter = new EventEmitter($listeners);
$emitter->emit(new BootstrapEvent($params));
```

In the above, the output will become:

```text
2
3
1
```

### Use cases

This design allows for several use cases:

- Single pub-sub style system to inject in any service that emits events. This
  approach can be useful as it allows a single location for attaching _all_
  listeners to _any_ event.
- Subject/Observer style systems, with a discrete `EventEmitter` composed in a
  target class that will emit events, allowing attachment only by those
  interested in specific events the target class emits.
- Workflow-style event systems, using the `PrioritizedListenerAggregate`.

## Support

* [Issues](https://github.com/zendframework/phly-event-emitter/issues/)
