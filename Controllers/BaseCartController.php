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

abstract class BaseCartController extends CoreController
{
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

    abstract public function addInternal($uniqueId, $quantity = 1);

    public function actionList()
    {
        $cart = $this->getCart();
//        $cart->add(new Product(['price' => 10, 'id' => 1]), 1, ['color' => 'white']);
//        $cart->add(new Product(['price' => 20, 'id' => 1]), 1, ['color' => 'black']);
        echo $this->render('cart/list.html', [
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
