<?php

use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $model app\models\ValorPrendaUnidad */

$this->title = 'Editar pago: ' . $model->id_valor;
$this->params['breadcrumbs'][] = ['label' => 'Valor Prenda', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_valor, 'url' => ['view', 'id' => $model->id_valor]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="valor-prenda-unidad-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?php if($model->cerrar_pago == 0){?>
        <?= $this->render('_form', [
            'model' => $model,
            'orden' => $orden,
        ])?>
        <?php } else { 
         Yii::$app->getSession()->setFlash('warning', 'Este archivo no se puede modificar porque el proceso ya se cerro.');
       }?>      
    

</div>
