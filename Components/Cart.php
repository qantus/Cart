<?php
/**
 *
 *
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 28/10/14.10.2014 13:06
 */

namespace Modules\Cart\Components;

use Mindy\Helper\Traits\Accessors;
use Mindy\Helper\Traits\Configurator;
use Modules\Cart\Interfaces\ICartItem;

class Cart
{
    use Accessors, Configurator;

    /**
     * @var
     */
    public $storage;
    /**
     * @var \Modules\Cart\Components\SessionStorage
     */
    private $_storage;

    public function getStorage()
    {
        if ($this->_storage === null) {
            $this->_storage = new SessionStorage();
        }
        return $this->_storage;
    }

    protected function makeKey(ICartItem $object, array $data)
    {
        return strtr("{class}{unique_id}", [
            "{class}" => get_class($object),
            "{unique_id}" => serialize(['unique_id', $object->getUniqueId(), 'data' => $data])
        ]);
    }

    public function get(ICartItem $object, array $data = [])
    {
        return $this->getStorage()->get($this->makeKey($object, $data));
    }

    public function add(ICartItem $object, $quantity = 1, array $data = [])
    {
        $key = $this->makeKey($object, $data);
        if ($this->has($object, $data)) {
            $item = $this->get($object, $data);
            $item['quantity'] += $quantity;
            $item['price'] = $item['object']->getPrice() * $item['quantity'];
            $this->getStorage()->remove($key);
        } else {
            $item = [
                'object' => $object,
                'quantity' => $quantity,
                'data' => $data,
                'price' => $object->getPrice() * $quantity
            ];
        }
        $this->getStorage()->add($key, $item);
        return $this;
    }

    protected function getPositionByKey($key)
    {
        $data = array_values(array_flip($this->getStorage()->getData()));
        return isset($data[$key]) ? $data[$key] : null;
    }

    public function updateQuantityByKey($key, $quantity)
    {
        $positionKey = $this->getPositionByKey($key);
        if ($positionKey) {
            $item = $this->getStorage()->get($positionKey);

            $item['quantity'] = $quantity;
            $item['price'] = $item['object']->getPrice() * $item['quantity'];
            $this->getStorage()->remove($positionKey);

            $this->getStorage()->add($positionKey, $item);

            return true;
        }
        return false;
    }

    public function increaseQuantityByKey($key)
    {
        $positionKey = $this->getPositionByKey($key);
        if ($positionKey) {
            $item = $this->getStorage()->get($positionKey);

            $item['quantity'] += 1;
            $item['price'] = $item['object']->getPrice() * $item['quantity'];
            $this->getStorage()->remove($positionKey);
            $this->getStorage()->add($positionKey, $item);
            return true;
        }
        return false;
    }

    public function increaseQuantity(ICartItem $object, array $data = [])
    {
        $item = $this->get($object, $data);
        if ($item) {
            $item['quantity'] += 1;
            $item['price'] = $item['object']->getPrice() * $item['quantity'];
            $key = $this->makeKey($object, $data);
            $this->getStorage()->remove($key);
            $this->getStorage()->add($key, $item);
            return true;
        }
        return false;
    }

    public function decreaseQuantityByKey($key)
    {
        $positionKey = $this->getPositionByKey($key);
        if ($positionKey) {
            $item = $this->getStorage()->get($positionKey);

            $item['quantity'] -= 1;
            $item['price'] = $item['object']->getPrice() * $item['quantity'];
            $this->getStorage()->remove($positionKey);
            $this->getStorage()->add($positionKey, $item);
            return true;
        }
        return false;
    }

    public function decreaseQuantity(ICartItem $object, array $data = [])
    {
        $item = $this->get($object, $data);
        if ($item) {
            $item['quantity'] -= 1;
            $item['price'] = $item['object']->getPrice() * $item['quantity'];
            $key = $this->makeKey($object, $data);
            $this->getStorage()->remove($key);
            $this->getStorage()->add($key, $item);
            return true;
        }
        return false;
    }

    public function removeByKey($key)
    {
        $key = $this->getPositionByKey($key);
        return $key === null ? false : $this->getStorage()->remove($key);
    }

    public function remove(ICartItem $object, array $data = [])
    {
        return $this->getStorage()->remove($this->makeKey($object, $data));
    }

    public function has(ICartItem $object, array $data = [])
    {
        $key = $this->makeKey($object, $data);
        return $this->getStorage()->has($key);
    }

    public function clear()
    {
        return $this->getStorage()->clear();
    }

    public function getQuantity()
    {
        $quantity = 0;
        foreach ($this->getItems() as $item) {
            $quantity += $item['quantity'];
        }
        return $quantity;
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += $item['price'];
        }
        return $total;
    }

    public function getItems()
    {
        return $this->getStorage()->getItems();
    }
}