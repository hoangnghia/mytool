<?php
namespace app\modules\user\frontend\controllers;
use Yii;
use luya\web\Controller;

class DefaultController extends Controller
{

    public function actionHello()
    {
        return $this->render('hello');
    }

    public function actionBye()
    {
        return 'I am the bye action';
    }

    public function actionWhoAmI()
    {
        $id = $this->id;  // returns the controller id
        return $id;
    }
}