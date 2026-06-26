<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;

// Determinar el color del error según el código de estado
$statusCode = isset($exception) ? $exception->statusCode : 500;
$textColor = ($statusCode == 404) ? 'text-warning' : 'text-danger';
$icon = ($statusCode == 404) ? 'fa-exclamation-triangle' : 'fa-ban';
?>
<section class="content" style="padding-top: 50px;">

    <div class="error-page d-flex align-items-start">
        <h2 class="headline <?= $textColor ?> mr-4" style="font-size: 100px; font-weight: 300; margin-top: -20px;">
            <?= $statusCode ?>
        </h2>

        <div class="error-content" style="margin-left: 20px;">
            <h3>
                <i class="fa <?= $icon ?> <?= $textColor ?>"></i> <?= Html::encode($this->title) ?>
            </h3>

            <p class="lead text-muted" style="margin-top: 15px;">
                <?= nl2br(Html::encode($message)) ?>
            </p>

            <p>
                El error anterior ocurrió mientras el servidor web procesaba su solicitud.
                Por favor, póngase en contacto con el administrador si cree que se trata de un error del sistema. Muchas gracias.
            </p>
            
            <p class="mt-4">
                Mientras tanto, puede <a class="btn btn-sm btn-primary" href='<?= Yii::$app->homeUrl ?>'><i class="fa fa-home"></i> Regresar al Inicio</a>
            </p>

            </div>
    </div>

</section>