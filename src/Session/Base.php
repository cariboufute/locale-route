<?php

namespace CaribouFute\LocaleRoute\Session;

use Session;

abstract class Base
{
    protected $key;

    public function get()
    {
        return Session::get($this->key) ?: $this->setAndGetDefault();
    }

    public function set($value)
    {
        return $this->put($value);
    }

    public function put($value)
    {
        return Session::put($this->key, $value);
    }

    protected function setAndGetDefault()
    {
        $default = $this->getDefault();
        $this->set($default);
        return $default;
    }

    abstract protected function getDefault();
}
