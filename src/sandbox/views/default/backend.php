<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$orderId = '1197';
$orderId = '1625';
$eventId = '16';

$merchantCode = 'M09111';
$merchantKey = 'tFgrFE0vUR';
$paymentId = '';
$refNo = $eventId.'-'.$orderId;
$currency = 'MYR';
$status = 1;
$amount = 1.52;
$signature = \ant\sandbox\gateway\ipay88\Sandbox::createSignature($merchantKey, $merchantCode, $paymentId, $refNo, $amount, $currency, $status)
?>
<?php $form = ActiveForm::begin([
    'method' => 'post',
    'action' => 'https://event.my/payment/default/complete-payment?payId='.$orderId.'&type=order&paymentMethod=ipay88&backend=1',
]) ?>

    </br></br></br></br>
    <?php /*
    <?= Html::hiddenInput('payId', 1552) ?>
    <?= Html::hiddenInput('type', 'order') ?>
    <?= Html::hiddenInput('paymentMethod', 'ipay88') ?>
    <?= Html::hiddenInput('backend', 1) ?>
    */ ?>

    
    <?= Html::hiddenInput('MerchantCode', $merchantCode) ?>
    <?= Html::hiddenInput('PaymentId', $paymentId) ?>
    <?= Html::hiddenInput('RefNo', $refNo) ?>
    <?= Html::hiddenInput('Amount', $amount) ?>
    <?= Html::hiddenInput('Currency', $currency) ?>
    <?= Html::hiddenInput('Remark', '') ?>
    <?= Html::hiddenInput('TransId', 'TEST_BACKEND_'.uniqid()) ?>
    <?= Html::hiddenInput('AuthCode', '') ?>
    <?= Html::hiddenInput('Status', $status) ?>
    <?= Html::hiddenInput('ErrDesc', '') ?>
    <?= Html::hiddenInput('Signature', $signature) ?>

    <button>Backend Submit</button>
<?php ActiveForm::end() ?>