<?php

namespace Modules\Cart\Components;

class SessionStorage
{
    const KEY = 'cart';

    /**
     * Yii compatibility
     */
    public function __construct()
    {
        if (!isset($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = [];
        }
    }

    public function get($key)
    {
        $data = $this->getData();
        return unserialize($data[$key]);
    }

    public function remove($key)
    {
        if ($this->has($key)) {
            unset($_SESSION[self::KEY][$key]);
            return true;
        }
        return false;
    }

    public function add($key, $value)
    {
        $_SESSION[self::KEY][$key] = serialize($value);
    }

    public function count()
    {
        return count(isset($_SESSION[self::KEY]) ? $_SESSION[self::KEY] : $_SESSION[self::KEY] = []);
    }

    public function clear()
    {
        $_SESSION[self::KEY] = [];
        return $this;
    }

    public function getItems()
    {
        $items = [];
        foreach ($this->getData() as $item) {
            $items[] = unserialize($item);
        }
        return $items;
    }

    public function has($key)
    {
        $data = $this->getData();
        return array_key_exists($key, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return isset($_SESSION[self::KEY]) ? $_SESSION[self::KEY] : $_SESSION[self::KEY] = [];
    }
}
