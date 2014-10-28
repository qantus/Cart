# Shopping cart

Example usage:

#### Model

```php
<?php

use Mindy\Helper\Traits\Accessors;
use Mindy\Helper\Traits\Configurator;
use Modules\Cart\Interfaces\ICartItem;

class Product implements ICartItem
{
    use Accessors, Configurator;

    public $id;

    public $price;

    public function __toString()
    {
        return (string)$this->id;
    }

    /**
     * @return mixed unique product identification
     */
    public function getUniqueId()
    {
        return $this->id;
    }

    /**
     * @return int|float
     */
    public function getPrice()
    {
        return $this->price;
    }
}
```

#### Controller

```php
<?php

class CartController extends BaseCartController
{
    public function addInternal($uniqueId, $quantity = 1)
    {
        $data = [
            'color' => 'white'
        ];
        $this->getCart()->add(new Product(['price' => 10, 'id' => 1]), $quantity, $data);
    }
}
```