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

$this->title = 'Inducciones de Operaciones';
$this->params['breadcrumbs'][] = ['label' => 'Flujo de operaciones', 'url' => ['view_induccion_operacion','id' => $id]];
$this->params['breadcrumbs'][] = $model->idordenproduccion;
//codigo que permite buscar el si la OP tiene modulo de balanceo

?>
<div class="orden-produccion-view">
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-default btn"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['produccionbalanceo'],['class' => 'btn btn-primary btn-xs']) ?></button>
    </div>    
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle del registro
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_orden')?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?></th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigoproducto') ?>:</th>
                    <td><?= Html::encode($model->codigoproducto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Unidades') ?>:</th>
                    <td align="right"><?= Html::encode (''.number_format($model->cantidad),0) ?></td>
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
            <li role="presentation" class="active"><a href="#operaciones" aria-controls="operaciones" role="tab" data-toggle="tab">Operaciones <span class="badge"><?= count($operaciones) ?></span></a></li>
        </ul>
        <div class="tab-content">
           <div role="tabpanel" class="tab-pane active" id="operaciones">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style="font-size: 85%;">
                                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Segundos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Minutos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Sam induccion</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Aplico</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Sam Observacion</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                     
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($operaciones as $registro):?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $registro->idproceso ?></td>
                                            <td><?= $registro->proceso->proceso ?></td>
                                            <td><?= ''.number_format($registro->segundos,0) ?></td>
                                            <td><?= ''.number_format($registro->minutos ,2) ?></td>
                                            <?php if($registro->tiempo_induccion > 0){?>
                                                  <td style="background-color: #B9D5CE; color: #0000CC"><?= ''.number_format($registro->tiempo_induccion ,2)?></td>
                                            <?php }else{?>
                                                  <td><?= ''.number_format($registro->tiempo_induccion ,2)?></td>
                                            <?php }?>     
                                            <td><?= $registro->aplicoTiempo ?></td>      
                                            <td><?= $registro->observacion ?></td>
                                            <?php if($registro->aplica_induccion == 0){?> 
                                                <td style= 'width: 20px; height: 20px;'>
                                                    <?= Html::a('<span class="glyphicon glyphicon-time"></span>  ',
                                                            ['orden-produccion/subir_sam_induccion', 'id' => $model->idordenproduccion, 'id_operacion'=> $registro->id],
                                                            [
                                                                'class' => '',   
                                                                'title' => 'Subir sam a la induccion',
                                                                'data-toggle'=>'modal',
                                                                'data-target'=>'#modalsubirsaminduccion'.$model->idordenproduccion,
                                                            ])    
                                                       ?>
                                                    <div class="modal remote fade" id="modalsubirsaminduccion<?= $model->idordenproduccion?>" data-backdrop="static">
                                                        <div class="modal-dialog modal-lg" style ="width: 550px;">
                                                            <div class="modal-content"></div>
                                                        </div>
                                                    </div>
                                                </td> 
                                            <?php }else{?>
                                                <td style= 'width: 20px; height: 20px;'></td>
                                            <?php }?> 
                                            <td style= 'width: 20px; height: 20px;'>
                                                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>  ',
                                                            ['orden-produccion/editar_sam_operacion', 'id' => $model->idordenproduccion, 'id_operacion'=> $registro->id],
                                                            [
                                                                'class' => '',   
                                                                'title' => 'Permite editar el sam de la operacion',
                                                                'data-toggle'=>'modal',
                                                                'data-target'=>'#modaleditarsamoperacion'.$model->idordenproduccion,
                                                            ])    
                                                       ?>
                                                    <div class="modal remote fade" id="modaleditarsamoperacion<?= $model->idordenproduccion?>" data-backdrop="static">
                                                        <div class="modal-dialog modal-lg" style ="width: 550px;">
                                                            <div class="modal-content"></div>
                                                        </div>
                                                    </div>
                                            </td>     
                                        </tr>
                                    <?php endforeach; ?>  
                                </tbody> 
                               
                            </table>
                        </div>    
                    </div>
                </div>
                <?php
                if(count($operaciones) > 0){ ?>
                    <div class="panel-footer text-right">
                        <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Excel', ['exceloperaciones_iniciales', 'id'=>$model->idordenproduccion], ['class' => 'btn btn-primary btn-sm'])?>
                    </div>    
                <?php }?>
            </div>
           <!--TERMINA TABS-->
        </div>  
    </div>   
    <?php ActiveForm::end(); ?>
</div>

