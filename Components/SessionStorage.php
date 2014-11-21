<?php

namespace Modules\Cart\Components;

/**
 * Class SessionStorage
 * @package Modules\Cart\Components
 */
class SessionStorage
{
    const KEY = 'cart';

    public function __construct()
    {
        if (!isset($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = [];
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $data = $this->getData();
        return unserialize($data[$key]);
    }

    /**
     * @param $key
     * @return bool
     */
    public function remove($key)
    {
        if ($this->has($key)) {
            unset($_SESSION[self::KEY][$key]);
            return true;
        }
        return false;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function add($key, $value)
    {
        $_SESSION[self::KEY][$key] = serialize($value);
        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count(isset($_SESSION[self::KEY]) ? $_SESSION[self::KEY] : $_SESSION[self::KEY] = []);
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $_SESSION[self::KEY] = [];
        return $this;
    }

    /**
     * @return \Modules\Cart\Components\CartItem[]
     */
    public function getItems()
    {
        $items = [];
        foreach ($this->getData() as $item) {
            $items[] = unserialize($item);
        }
        return $items;
    }

    /**
     * @param $key
     * @return bool
     */
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
