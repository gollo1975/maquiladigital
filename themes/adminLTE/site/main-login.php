<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Matriculaempresa;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Iniciar Sesión';
?>

<div class="login-box">
    <div class="panel panel-default panel-login">
        <div class="panel-heading">
            <div class="login-logo text-center">
                <?php
                // Es más eficiente y seguro buscar el modelo una sola vez y no en la vista
                $empresa = Matriculaempresa::findOne(1);
                $nombreSistema = $empresa ? $empresa->nombresistema : 'Mi Sistema';
                ?>
                <a href="#" style="color: #006d77;"><b><?= Html::encode($nombreSistema) ?></b></a>
            </div>
        </div>
        <div class="panel-body login-box-body">
            <p class="login-box-msg" style="color: #006d77; font-size: 18px">Inicia sesión para comenzar</style:></p>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'enableClientValidation' => true, // Activar la validación del lado del cliente
                'options' => ['class' => 'form-vertical']
            ]); ?>

            <?= $form->field($model, 'username', [
                'options' => ['class' => 'form-group has-feedback'],
                'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>", // Usar un ícono más apropiado para el usuario
            ])->textInput(['placeholder' => $model->getAttributeLabel('username')])->label(false) ?>

            <?= $form->field($model, 'password', [
                'options' => ['class' => 'form-group has-feedback'],
                'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>",
            ])->passwordInput(['placeholder' => $model->getAttributeLabel('password')])->label(false) ?>

            <div class="row">
                <div class="col-xs-8">
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                </div>
                <div class="col-xs-4">
                    <?= Html::submitButton(
                        'Iniciar',
                        [
                            'class' => 'btn btn-primary btn-block', // Usar clases de Bootstrap para un diseño más moderno
                            'name' => 'login-button'
                        ]
                    ) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

            <div class="text-center" style="margin-top: 30px;">
                <?= Html::img('@web/dist/images/logos/logomaquila.jpeg', [
                    'alt' => 'Logo de la empresa',
                    'class' => 'img-responsive center-block',
                    'style' => 'max-width: 50%;' // Controlar el tamaño con CSS
                ]) ?>
            </div>

        </div>
    </div>
</div>