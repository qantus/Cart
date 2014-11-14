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
 * @date 28/10/14.10.2014 15:51
 */

namespace Modules\Cart\Controllers;

use Mindy\Base\Mindy;
use Modules\Cart\CartModule;
use Modules\Core\Controllers\CoreController;

class CartController extends CoreController
{
    /**
     * @var string
     */
    public $listTemplate = 'cart/list.html';

    /**
     * @return \Modules\Cart\Components\Cart
     */
    protected function getCart()
    {
        return Mindy::app()->getModule('Cart')->getComponent('cart');
    }

    public function actionAdd($uniqueId, $quantity = 1)
    {
        $this->addInternal($uniqueId, $quantity);
        $this->r->flash->success(CartModule::t('Product added'));
        $this->r->redirect('cart.list');
    }

    public function actionList()
    {
        $cart = $this->getCart();
        echo $this->render($this->listTemplate, [
            'items' => $cart->getItems(),
            'total' => $cart->getTotal(),
        ]);
    }

    public function actionQuantity($key, $quantity)
    {
        $cart = $this->getCart();
        if ($cart->updateQuantityByKey($key, $quantity)) {
            $this->r->flash->success(CartModule::t('Quantity updated'));
        } else {
            $this->r->flash->success(CartModule::t('Error has occurred'));
        }
        $this->r->redirect('cart.list');
    }

    public function actionIncrease($key)
    {
        $cart = $this->getCart();
        if ($cart->increaseQuantityByKey($key)) {
            $this->r->flash->success(CartModule::t('Quantity updated'));
        } else {
            $this->r->flash->success(CartModule::t('Error has occurred'));
        }
        $this->r->redirect('cart.list');
    }

    public function actionDecrease($key)
    {
        $cart = $this->getCart();
        if ($cart->decreaseQuantityByKey($key)) {
            $this->r->flash->success(CartModule::t('Quantity updated'));
        } else {
            $this->r->flash->success(CartModule::t('Error has occurred'));
        }
        $this->r->redirect('cart.list');
    }

    public function actionDelete($key)
    {
        $deleted = $this->getCart()->removeByKey($key);
        if ($deleted) {
            $this->r->flash->success(CartModule::t('Position sucessfully removed'));
        } else {
            $this->r->flash->error(CartModule::t('Error has occurred'));
        }
        $this->r->redirect('cart.list');
    }
}
