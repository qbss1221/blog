<?php
/**
 * Created by PhpStorm.
 * User: deserts
 * Date: 2017/11/23
 * Time: 11:23
 */
namespace frontend\controllers\base;

use yii\web\Controller;

 class BaseController extends Controller
 {
     public function beforeAction($action)
     {
         if(!parent::beforeAction($action))
         {
             return false;
         }
         return true;
     }
 }