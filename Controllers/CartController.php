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

abstract class CartController extends CoreController
{
    /**
     * @var string
     */
    public $listTemplate = 'cart/list.html';
    /**
     * @var string
     */
    public $defaultListRoute = 'cart:list';
    /**
     * @var null|string
     */
    public $listRoute = null;

    protected function getListRoute()
    {
        $module = Mindy::app()->getModule('Cart');
        return $module->listRoute ? $module->listRoute : $this->defaultListRoute;
    }

    /**
     * @return \Modules\Cart\Components\Cart
     */
    protected function getCart()
    {
        return Mindy::app()->getModule('Cart')->getComponent('cart');
    }

    public function actionAdd($uniqueId, $quantity = 1, $type = null)
    {
        $isAjax = $this->request->isAjax;
        $cart = $this->getCart();
        if ($this->addInternal($uniqueId, $quantity, $type)) {
            if ($isAjax) {
                echo $this->json([
                    'status' => true,
                    'total' => $cart->getTotal(),
                    'message' => [
                        'title' => CartModule::t('Product added')
                    ]
                ]);
                Mindy::app()->end();
            } else {
                $this->r->flash->success(CartModule::t('Product added'));
                $this->r->redirect($this->getListRoute());
            }
        } else {
            if ($isAjax) {
                echo $this->json([
                    'status' => false,
                    'total' => $cart->getTotal(),
                    'message' => [
                        'title' => CartModule::t('Error has occurred')
                    ]
                ]);
                Mindy::app()->end();
            } else {
                $this->r->flash->success(CartModule::t('Error has occurred'));
                $this->r->redirect($this->getListRoute());
            }
        }
    }

    public function actionList()
    {
        $listRoute = $this->getListRoute();
        $url = Mindy::app()->urlManager->reverse($listRoute);
        if ($listRoute && strpos($this->request->path, $url) === false) {
            $this->r->redirect($listRoute);
        }
        $cart = $this->getCart();
        echo $this->render($this->listTemplate, [
            'items' => $cart->getItems(),
            'total' => $cart->getTotal(),
        ]);
    }

    public function actionQuantity($key, $quantity)
    {
        $isAjax = $this->request->isAjax;
        $cart = $this->getCart();
        if ($cart->updateQuantityByKey($key, $quantity)) {
            if ($isAjax) {
                echo $this->json([
                    'status' => true,
                    'total' => $cart->getTotal(),
                    'message' => [
                        'title' => CartModule::t('Quantity updated')
                    ]
                ]);
                Mindy::app()->end();
            } else {
                $this->r->flash->success(CartModule::t('Quantity updated'));
                $this->r->redirect($this->getListRoute());
            }
        } else {
            if ($isAjax) {
                echo $this->json([
                    'status' => false,
                    'total' => $cart->getTotal(),
                    'message' => [
                        'title' => CartModule::t('Error has occurred')
                    ]
                ]);
                Mindy::app()->end();
            } else {
                $this->r->flash->success(CartModule::t('Error has occurred'));
                $this->r->redirect($this->getListRoute());
            }
        }
    }

    public function actionIncrease($key)
    {
        $isAjax = $this->request->isAjax;
        $cart = $this->getCart();
        if ($cart->increaseQuantityByKey($key)) {
            if ($isAjax) {
                echo $this->json([
                    'status' => true,
                    'total' => $cart->getTotal(),
                    'message' => [
                        'title' => CartModule::t('Quantity updated')
                    ]
                ]);
                Mindy::app()->end();
            } else {
                $this->r->flash->success(CartModule::t('Quantity updated'));
                $this->r->redirect($this->getListRoute());
            }
        } else {
            if ($isAjax) {
                echo $this->json([
                    'status' => false,
                    'total' => $cart->getTotal(),
                    'error' => [
                        'title' => CartModule::t('Error has occurred')
                    ]
                ]);
                Mindy::app()->end();
            } else {
                $this->r->flash->success(CartModule::t('Error has occurred'));
                $this->r->redirect($this->getListRoute());
            }
        }
    }

    public function actionDecrease($key)
    {
        $isAjax = $this->request->isAjax;
        $cart = $this->getCart();
        if ($cart->decreaseQuantityByKey($key)) {
            if ($isAjax) {
                echo $this->json([
                    'status' => true,
                    'total' => $cart->getTotal(),
                    'message' => [
                        'title' => CartModule::t('Quantity updated')
                    ]
                ]);
                Mindy::app()->end();
            } else {
                $this->r->flash->success(CartModule::t('Quantity updated'));
                $this->r->redirect($this->getListRoute());
            }
        } else {
            if ($isAjax) {
                echo $this->json([
                    'status' => false,
                    'total' => $cart->getTotal(),
                    'error' => [
                        'title' => CartModule::t('Error has occurred')
                    ]
                ]);
                Mindy::app()->end();
            } else {
                $this->r->flash->success(CartModule::t('Error has occurred'));
                $this->r->redirect($this->getListRoute());
            }
        }
    }

    public function actionDelete($key)
    {
        $cart = $this->getCart();
        $deleted = $cart->removeByKey($key);
        $isAjax = $this->request->isAjax;
        if ($deleted) {
            if ($isAjax) {
                echo $this->json([
                    'status' => true,
                    'total' => $cart->getTotal(),
                    'message' => [
                        'title' => CartModule::t('Position sucessfully removed'),
                    ]
                ]);
                Mindy::app()->end();
            } else {
                $this->r->flash->success(CartModule::t('Position sucessfully removed'));
                $this->r->redirect($this->getListRoute());
            }
        } else {
            if ($isAjax) {
                echo $this->json([
                    'status' => false,
                    'total' => $cart->getTotal(),
                    'error' => [
                        'title' => CartModule::t('Error has occurred'),
                    ]
                ]);
                Mindy::app()->end();
            } else {
                $this->r->flash->error(CartModule::t('Error has occurred'));
                $this->r->redirect($this->getListRoute());
            }
        }
    }

    abstract protected function addInternal($uniqueId, $quantity, $type);
}
