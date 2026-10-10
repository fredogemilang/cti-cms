<?php

namespace Tests\Unit;

use App\Support\Action;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Action::clear();
        parent::tearDown();
    }

    public function test_run_calls_callbacks_with_arguments(): void
    {
        $received = [];
        Action::add('demo.event', function (string $a, int $b) use (&$received) {
            $received[] = [$a, $b];
        });

        Action::run('demo.event', 'x', 7);

        $this->assertSame([['x', 7]], $received);
    }

    public function test_callbacks_run_in_priority_order(): void
    {
        $order = [];
        Action::add('demo.order', function () use (&$order) {
            $order[] = 'late';
        }, 20);
        Action::add('demo.order', function () use (&$order) {
            $order[] = 'early';
        }, 5);
        Action::add('demo.order', function () use (&$order) {
            $order[] = 'default';
        });

        Action::run('demo.order');

        $this->assertSame(['early', 'default', 'late'], $order);
    }

    public function test_has_and_clear(): void
    {
        $this->assertFalse(Action::has('demo.has'));

        Action::add('demo.has', fn () => null);
        $this->assertTrue(Action::has('demo.has'));

        Action::clear();
        $this->assertFalse(Action::has('demo.has'));
    }

    public function test_running_unknown_tag_is_a_noop(): void
    {
        Action::run('demo.unknown', 'anything');

        $this->assertFalse(Action::has('demo.unknown'));
    }
}
