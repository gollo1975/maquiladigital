<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MensajeriaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mensajeria-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    

    <?php ActiveForm::end(); ?>

</div>
