<?php

namespace CaribouFute\LocaleRoute\Session;

use Illuminate\Session\Store;

abstract class Base
{
    protected $store;
    protected $key;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function get()
    {
        return $this->store->get($this->key) ?: $this->setAndGetDefault();
    }

    public function set($value): void
    {
        $this->put($value);
    }

    public function put($value): void
    {
        $this->store->put($this->key, $value);
    }

    protected function setAndGetDefault()
    {
        $default = $this->getDefault();
        $this->set($default);
        return $default;
    }

    abstract protected function getDefault();
}
