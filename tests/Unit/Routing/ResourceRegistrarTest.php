<?php

namespace Tests\Unit\Routing;

use CaribouFute\LocaleRoute\Routing\LocaleRouter;
use CaribouFute\LocaleRoute\Routing\ResourceRegistrar;
use Illuminate\Routing\Router as IlluminateRouter;
use Mockery;
use Orchestra\TestBench\TestCase;

class ResourceRegistrarTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->router = Mockery::mock(IlluminateRouter::class);
        $this->localeRouter = Mockery::mock(LocaleRouter::class);

        $this->registrar = Mockery::mock(ResourceRegistrar::class, [$this->localeRouter, $this->router])->makePartial();
    }

    public function testTrue()
    {
        $this->assertTrue(true);
    }
}
