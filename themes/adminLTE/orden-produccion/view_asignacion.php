 <?php

//modelos
use app\models\Ordenproducciondetalle;
use app\models\Ordenproduccion;
use app\models\Cliente;
use app\models\Color;
use app\models\Remision;
use app\models\Producto;
use app\models\Productodetalle;
//clase
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\Session;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$this->title = 'Detalle orden';
$this->params['breadcrumbs'][] = ['label' => 'Ordenes de Producción', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idordenproduccion;
$view = 'orden-produccion';
?>

<?php
    $remision = Remision::find()->where(['=', 'idordenproduccion', $model->idordenproduccion])->one();
?>

<div class="ordenproduccion-view">
    <p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index_asignacion'], ['class' => 'btn btn-primary btn-sm']) ?>
    </p>        
    <div class="panel panel-success">
        <div class="panel-heading">
            Orden de Producción
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, "idordenproduccion") ?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?>:</th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idtipo') ?></th>
                    <td><?= Html::encode($model->tipo->tipo) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Unidades') ?>:</th>
                    <td><?= Html::encode($model->cantidad) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechallegada') ?></th>
                    <td><?= Html::encode($model->fechallegada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechaprocesada') ?></th>
                    <td><?= Html::encode($model->fechaprocesada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechaentrega') ?></th>
                    <td><?= Html::encode($model->fechaentrega) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'usuariosistema') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ordenproduccion') ?></th>
                    <td><?= Html::encode($model->ordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ordenproduccionext') ?></th>
                    <td><?= Html::encode($model->ordenproduccionext) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigoproducto') ?></th>
                    <td><?= Html::encode($model->codigoproducto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Minutos') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->duracion,0)) ?></td>                    
                </tr>
                <tr style="font-size: 85%;">
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?></th>
                    <td colspan="3"><?= Html::encode($model->observacion) ?></td>    
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Exportacion') ?>:</th>
                    <td><?= Html::encode($model->exportarOrden) ?></td>    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'totalorden') ?>:</th>
                    <td  style="text-align: right"><?= Html::encode('$ '.number_format($model->totalorden,0)) ?></td>
                </tr>
              
            </table>
        </div>
    </div>
     <?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
    ]);?>
    <!-- comienza los tabs -->
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#detalle_orden" aria-controls="detalle_orden" role="tab" data-toggle="tab">Detalle <span class="badge"><?= count($modeldetalles) ?></span></a></li>
        </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="detalle_orden">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Producto</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Precio</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Subtotal</th>
                                            <th scope="col" style='background-color:#B9D5CE;'></th>
                                        </tr>
                                    </thead>    
                                    <body>
                                        <?php foreach ($modeldetalles as $val): ?>
                                            <tr style="font-size: 85%;">
                                                <td><?= $val->iddetalleorden ?></td>
                                                <td><?= $val->productodetalle->prendatipo->prenda.' / '.$val->productodetalle->prendatipo->talla->talla   ?></td>
                                                <td><?= $val->codigoproducto ?></td>
                                                <?php if($val->id_planta == 1){?>
                                                    <td><?= $val->plantaProduccion->nombre_planta ?></td>
                                                <?php }else{?>
                                                    <td style='background-color:#F6F7B3'><?= $val->plantaProduccion->nombre_planta ?></td>
                                                <?php }?>    
                                                <td><?= $val->cantidad ?></td>
                                                <td><?= '$ '.number_format($val->vlrprecio,2) ?></td>
                                                <td><?= '$ '.number_format($val->subtotal,2) ?></td>
                                                <td style="width: 25px; height: 25px;">
                                                      <!-- Inicio Nuevo Detalle proceso -->
                                                        <?= Html::a('<span class="glyphicon glyphicon-user"></span> ',
                                                            ['/orden-produccion/asignacion_talla', 'id' => $model->idordenproduccion, 'id_detalle' => $val->iddetalleorden],
                                                            [
                                                                'title' => 'Asignar tallas a la planta',
                                                                'data-toggle'=>'modal',
                                                                'data-target'=>'#modalasignaciontalla'.$model->idordenproduccion,
                                                            ])    
                                                       ?>
                                                    <div class="modal remote fade" id="modalasignaciontalla<?= $model->idordenproduccion ?>">
                                                        <div class="modal-dialog modal-lg" style ="width: 550px;">
                                                            <div class="modal-content"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </body>          
                                </table>    
                            </div>  
                        </div>
                    </div>    
                </div>
               <!-- TERMINA TABS DE NOVEDADES -->
            </div>  
    </div>
  <?php ActiveForm::end(); ?>  
</div>

   