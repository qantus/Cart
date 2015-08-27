<?php

namespace Modules\Cart\Components;

/**
 * Class SessionStorage
 * @package Modules\Cart\Components
 */
class SessionStorage
{
    /**
     * @var Cart
     */
    protected $cart;
    /**
     * @var string
     */
    protected $key;

    public function __construct(Cart $cart, $key = 'cart')
    {
        $this->key = $key;
        $this->cart = $cart;

        if (!isset($_SESSION[$this->key])) {
            $_SESSION[$this->key] = [];
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
            $this->cart->getEventManager()->send($this->cart, 'onRemoveItem', unserialize($_SESSION[$this->key][$key]));
            unset($_SESSION[$this->key][$key]);
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
        $_SESSION[$this->key][$key] = serialize($value);
        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($_SESSION[$this->key]);
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $_SESSION[$this->key] = [];
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
        return $_SESSION[$this->key];
    }
}
