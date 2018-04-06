<?php

namespace luya\addressbook\tests\frontend;

use Yii;
use luya\Exception;
use luya\addressbook\frontend\controllers\DefaultController;
use luya\addressbook\tests\AddressbookTestCase;

class DefaultControllerTest extends AddressbookTestCase
{
    public function testActionIndex()
    {
        $module = Yii::$app->getModule('addressbook');
        $this->assertInstanceOf('luya\addressbook\frontend\Module', $module);
        $mock = $this->getMockBuilder(DefaultController::class)->setConstructorArgs(["id" => "default", "module" => $module])->getMock();
        $mock->method("actionIndex")->willThrowException(new Exception());
    }
}