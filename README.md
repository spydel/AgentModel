Model Agent
===========

Sample
------

<?php
$formModelAgent = new FormModelAgent();
$formModelAgent->setGlobalModel(new BasicModel());
$formModelAgent->setDependencyClassForm(new BasicModelForm());

$orderForm = new BasicModelForm();
$orderForm->load(\Yii::$app->request->post());
$formModelAgent->registerObserver($orderForm);

$basicModelOther = new BasicModelOtherForm();
$basicModelOther->load(\Yii::$app->request->post());
$formModelAgent->registerObserver($basicModelOther);

if (!$formModelAgent->validateObservers()) {
	throw new SystemException('При формировании заказа переданы неверные параметры');
}

$result = $formModelAgent->start();
if (!$result) {
	throw new Exception('При добавлении заказа произошла ошибка');
}

$orderId = $formModelAgent->getGlobalId();