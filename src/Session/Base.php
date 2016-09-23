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
        return Session::set($this->key, $value);
    }

    protected function setAndGetDefault()
    {
        $default = $this->getDefault();
        $this->set($default);
        return $default;
    }

    abstract protected function getDefault();
}
