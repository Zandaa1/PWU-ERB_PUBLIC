<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if ($this->app->bound('blade.compiler')) {
            $compiler = $this->app->make('blade.compiler');
            $ref = new \ReflectionProperty($compiler, 'forElseCounter');
            $ref->setAccessible(true);
            $ref->setValue($compiler, 0);
        }
    }
}
