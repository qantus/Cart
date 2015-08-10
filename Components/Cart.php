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

use Mindy\Base\Mindy;
use Mindy\Helper\Creator;
use Mindy\Helper\Traits\Accessors;
use Mindy\Helper\Traits\Configurator;
use Modules\Cart\Interfaces\ICartItem;

class Cart
{
    use Accessors, Configurator;

    /**
     * @var string|array component configuration
     */
    public $storage;
    /**
     * @var IDiscount[]
     */
    public $discounts = [];
    /**
     * @var bool force renew object when call getItems() method
     */
    public $forceFetch = false;
    /**
     * @var \Modules\Cart\Components\SessionStorage
     */
    private $_storage;
    /**
     * @var IDiscount[]
     */
    private $_discounts = null;

    public function init()
    {
        $signal = $this->getEventManager();
        $signal->handler($this, 'addItem', [$this, 'onAddItem']);
        $signal->handler($this, 'removeItem', [$this, 'onRemoveItem']);
    }

    public function getEventManager()
    {
        return Mindy::app()->signal;
    }

    public function onAddItem($item)
    {

    }

    public function onRemoveItem($item)
    {

    }

    /**
     * @return SessionStorage
     */
    public function getStorage()
    {
        if ($this->_storage === null) {
            $this->_storage = new SessionStorage($this);
        }
        return $this->_storage;
    }

    /**
     * @param ICartItem $object
     * @param array $data
     * @return string
     */
    protected function makeKey(ICartItem $object, array $data)
    {
        return strtr("{class}{unique_id}", [
            "{class}" => get_class($object),
            "{unique_id}" => serialize(['unique_id', $object->getUniqueId(), 'data' => $data])
        ]);
    }

    /**
     * @param ICartItem $object
     * @param array $data
     * @return mixed
     */
    public function get(ICartItem $object, array $data = [])
    {
        return $this->getStorage()->get($this->makeKey($object, $data));
    }

    /**
     * @param ICartItem $object
     * @param int $quantity
     * @param null $type
     * @param array $data
     * @return $this
     */
    public function add(ICartItem $object, $quantity = 1, $type = null, array $data = [])
    {
        $key = $this->makeKey($object, $data);
        if ($this->has($object, $data)) {
            $oldItem = $this->get($object, $data);
            $item = new CartItem([
                'object' => $oldItem->object,
                'data' => $oldItem->data,
                'quantity' => $oldItem->quantity + $quantity,
                'type' => $type,
            ]);
            $this->getStorage()->remove($key);
        } else {
            $item = new CartItem([
                'object' => $object,
                'data' => $data,
                'quantity' => $quantity,
                'type' => $type,
            ]);
        }

        $item->applyDiscount($this, $this->getDiscounts());
        $this->getStorage()->add($key, $item);
        return $this;
    }

    /**
     * @param $key
     * @return null
     */
    public function getPositionByKey($key)
    {
        $data = array_values(array_flip($this->getStorage()->getData()));
        return isset($data[$key]) ? $data[$key] : null;
    }

    /**
     * @param $key
     * @param $quantity
     * @return bool
     */
    public function updateQuantityByKey($key, $quantity)
    {
        $positionKey = $this->getPositionByKey($key);
        if ($positionKey) {
            $item = $this->getStorage()->get($positionKey);
            $item->setQuantity($quantity);
            $item->applyDiscount($this, $this->getDiscounts());
            $this->getStorage()->add($positionKey, $item);
            return true;
        }
        return false;
    }

    /**
     * @param $key
     * @return bool
     */
    public function increaseQuantityByKey($key)
    {
        $positionKey = $this->getPositionByKey($key);
        if ($positionKey) {
            $item = $this->getStorage()->get($positionKey);
            $item->setQuantity($item->getQuantity() + 1);
            $item->applyDiscount($this, $this->getDiscounts());
            $this->getStorage()->add($positionKey, $item);
            return true;
        }
        return false;
    }

    /**
     * @param ICartItem $object
     * @param array $data
     * @return bool
     */
    public function increaseQuantity(ICartItem $object, array $data = [])
    {
        $item = $this->get($object, $data);
        if ($item) {
            $item->setQuantity($item->getQuantity() + 1);
            $item->applyDiscount($this, $this->getDiscounts());
            $key = $this->makeKey($object, $data);
            $this->getStorage()->add($key, $item);
            return true;
        }
        return false;
    }

    /**
     * @param $key
     * @return bool
     */
    public function decreaseQuantityByKey($key)
    {
        $positionKey = $this->getPositionByKey($key);
        if ($positionKey) {
            $item = $this->getStorage()->get($positionKey);
            $item->setQuantity($item->getQuantity() - 1);
            $item->applyDiscount($this, $this->getDiscounts());
            $this->getStorage()->add($positionKey, $item);
            return true;
        }
        return false;
    }

    /**
     * @param ICartItem $object
     * @param array $data
     * @return bool
     */
    public function decreaseQuantity(ICartItem $object, array $data = [])
    {
        $item = $this->get($object, $data);
        if ($item) {
            $item->setQuantity($item->getQuantity() - 1);
            $item->applyDiscount($this, $this->getDiscounts());
            $key = $this->makeKey($object, $data);
            $this->getStorage()->add($key, $item);
            return true;
        }
        return false;
    }

    /**
     * @param $key
     * @return bool
     */
    public function removeByKey($key)
    {
        $key = $this->getPositionByKey($key);
        return $key === null ? false : $this->getStorage()->remove($key);
    }

    /**
     * @param ICartItem $object
     * @param array $data
     * @return bool
     */
    public function remove(ICartItem $object, array $data = [])
    {
        return $this->getStorage()->remove($this->makeKey($object, $data));
    }

    /**
     * @param ICartItem $object
     * @param array $data
     * @return bool
     */
    public function has(ICartItem $object, array $data = [])
    {
        $key = $this->makeKey($object, $data);
        return $this->getStorage()->has($key);
    }

    /**
     * @return $this
     */
    public function clear()
    {
        return $this->getStorage()->clear();
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        $quantity = 0;
        foreach ($this->getItems() as $item) {
            $quantity += $item->quantity;
        }
        return $quantity;
    }

    /**
     * @return float|int
     */
    public function getTotal()
    {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += $item->getPrice();
        }
        return $total;
    }

    /**
     * @return \Modules\Cart\Components\CartItem[]
     */
    public function getItems()
    {
        $items = $this->getStorage()->getItems();
        if ($this->forceFetch) {
            $newItems = [];
            foreach ($items as $item) {
                $object = $item->getObject();
                if ($newObject = $object->objects()->get(['pk' => $object->pk])) {
                    $item->setObject($newObject);
                    $newItems[] = $item;
                } else {
                    $this->remove($object, []);
                }
            }
            $items = $newItems;
        }
        return $items;
    }

    /**
     * @return bool
     */
    public function getIsEmpty()
    {
        return $this->getStorage()->count() === 0;
    }

    public function applyDiscount(ICartItem $object, $quantity = 1, $type = null, array $data = [])
    {
        $item = new CartItem([
            'quantity' => $quantity,
            'type' => $type,
            'data' => $data,
            'object' => $object
        ]);
        $item->applyDiscount($this, $this->getDiscounts());
        return $item->getPrice();
    }

    public function getDiscounts()
    {
        if ($this->_discounts === null) {
            $this->_discounts = [];
            foreach ($this->discounts as $className) {
                $this->_discounts[] = Creator::createObject($className);
            }
        }

        return $this->_discounts;
    }
}
