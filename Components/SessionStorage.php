<?php

namespace Modules\Cart\Components;

/**
 * Class SessionStorage
 * @package Modules\Cart\Components
 */
class SessionStorage
{
    const KEY = 'cart';

    /**
     * @var Cart
     */
    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;

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
            $this->cart->getEventManager()->send($this->cart, 'onRemoveItem', unserialize($_SESSION[self::KEY][$key]));
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
        $this->cart->getEventManager()->send($this->cart, 'onAddItem', $value);
        $_SESSION[self::KEY][$key] = serialize($value);
        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($_SESSION[self::KEY]);
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
        return array_key_exists($key, $this->getData());
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $_SESSION[self::KEY];
    }
}
