<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

$this->title = 'Listado de operaciones';
$this->params['breadcrumbs'][] = ['label' => 'Valor prenda unidad', 'url' => ['_form_acumulado_operaciones']];
$this->params['breadcrumbs'][] = $model->id_operario;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="valor-prenda-unidad-view">

    <!--<?= Html::encode($this->title) ?>-->
    <div class="panel panel-success">
        <div class="panel-heading">
           Informacion del operario 
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'documento') ?>:</th>
                    <td><?= Html::encode($model->documento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Operario') ?>:</th>
                    <td><?= Html::encode($model->nombrecompleto) ?></td>
                </tr> 
            </table>
        </div>
    </div> 
    <table class="table table-bordered table-hover">
        <thead>
            <tr style='font-size:85%;'>
               
                <th scope="col" style='background-color:#B9D5CE; width: 290px;'>Referencia</th> 
                <th scope="col" style='background-color:#B9D5CE; width: 290px;'>Nombre operacion</th>                        
                <th scope="col" style='background-color:#B9D5CE;width: 75px;'>Total unidades</th> 
               
        </thead>
        <tbody>
            <?php foreach ($detalleUnidades as $item): ?>
                <tr style="font-size: 85%;">
                    <td><?= htmlspecialchars($item['codigos_productos']) ?></td> <td><?= htmlspecialchars($item['proceso']) ?></td>
                    <td><?= number_format($item['total_cantidad'], 0) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
               
</div>    
    