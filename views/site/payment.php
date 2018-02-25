<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Оплата';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <h1><?= $name ?></h1>

    <?php if (is_null($payment)) : ?>
        <p>Выставленных счетов нет</p>
    <?php elseif (Yii::$app->session->hasFlash('payment')): ?>
        <p>Оплата проведена</p>
    <?php else: ?>
        <?php $form = ActiveForm::begin(['id' => 'payment-form']); ?>

        <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'currency') ?>

        <?= $form->field($model, 'summ') ?>
        <?= $form->field($model, 'email') ?>

        <div class="form-group">
            <?= Html::submitButton('Оплатить', ['class' => 'btn btn-primary', 'name' => 'payment-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    <?php endif; ?>

</div>
