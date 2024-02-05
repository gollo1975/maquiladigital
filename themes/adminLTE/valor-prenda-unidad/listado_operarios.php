<?php

//clases
use yii\bootstrap\Progress;
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
use yii\db\Expression;
use yii\db\Query;
    
/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$this->title = 'Listado de Confeccion';
$this->params['breadcrumbs'][] = ['label' => 'Listado tallas ', 'url' => ['listado_operaciones','id' => $id, 'id_detalle' => $id_detalle, 'token' => $token]];
$this->params['breadcrumbs'][] = $id;


?>
<div class="ordenproduccionproceso-view">
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-default btn"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['listado_operaciones','id' => $id, 'id_detalle' => $id_detalle, 'token' => $token],['class' => 'btn btn-primary btn-xs']) ?></button>
        
    </div>    
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle talla
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_orden')?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Referencia') ?></th>
                    <td><?= Html::encode($model->ordenproduccion->codigoproducto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Operacion') ?>:</th>
                    <td><?= Html::encode($model->proceso->proceso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?>:</th>
                    <td><?= Html::encode ($model->ordenproduccion->cliente->nombrecorto) ?></td>
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
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#operarios" aria-controls="operarios" role="tab" data-toggle="tab">Operarios <span class="badge"><?= count($operaciones) ?></span></a></li>
        </ul>
        <div class="tab-content">
           <div role="tabpanel" class="tab-pane active" id="operaciones">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                       <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                       <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                       <th scope="col" style='background-color:#B9D5CE;'>Vr. prenda</th>
                                       <th scope="col" style='background-color:#B9D5CE;'>F. Confeccion</th>
                                                                           
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($operaciones as $registro):?>
                                            <tr style="font-size: 85%;">
                                                <td><?= $registro->operarioProduccion->nombrecompleto ?></td>
                                                <td style="text-align: right"><?= ''.number_format($registro->cantidad) ?></td>
                                                <td style="text-align: right"><?= ''.number_format($registro->vlr_prenda) ?></td>
                                                <td><?= $registro->dia_pago ?></td>
                                            </tr>
                                    <?php endforeach;
                                    ?>  
                                </tbody> 
                            </table>
                        </div> 
                        <div class="panel-footer text-right">
                           <?= Html::a('<span class="glyphicon glyphicon-export"></span> Exportar excel', ['expotar_cantidad_confeccionada', 'id' => $id, 'id_detalle' => $id_detalle, 'id_operacion' => $id_operacion], ['class' => 'btn btn-primary btn-sm ']); ?>
                    
                        </div>
                    </div>    
                </div>
            </div>
        </div>  
    </div>   
    <?php ActiveForm::end(); ?>
</div>

