<?php

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
/* @var $model app\models\Empleado */

$this->title = 'Detalle maquina';
$this->params['breadcrumbs'][] = ['label' => 'Maquinas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_maquina;
$view = 'maquinas';
$maquina = \app\models\Maquinas::findOne($model->id_maquina);
$debaja = \app\models\DebajaMaquina::find()->where(['=','id_maquina', $model->id_maquina])->one();
?>
<div class="maquinas-view">

    <!--<?= Html::encode($this->title) ?>-->
   
    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php if($maquina->estado_maquina == 0){?>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_maquina], ['class' => 'btn btn-success btn-sm']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 17, 'codigo' => $model->id_maquina,'view' => $view], ['class' => 'btn btn-default btn-sm']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-hand-down"></span> Dar debaja',
                            ['/maquinas/dar_debaja_maquina', 'id' =>$model->id_maquina],
                            [
                                'title' => 'Dar de baja a la maquinas',
                                'data-toggle'=>'modal',
                                'data-target'=>'#modaldardebajamaquina',
                                'class' => 'btn btn-warning btn-xs'
                            ])    
                       ?>
                    </div> 
                    <div class="modal remote fade" id="modaldardebajamaquina"><?= $model->id_maquina?>
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content"></div>
                        </div>
                    </div>
        <?php }else{ ?>
               <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 17, 'codigo' => $model->id_maquina,'view' => $view], ['class' => 'btn btn-default btn-sm']) ?>
                <tr>
                    <button class="btn btn-primary btn-sm" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        Ver nota..
                    </button>
                    <div class="collapse" id="collapseExample">
                       <div class="well" style="font-size: 85%;">
                           <div class="panel panel-success">
                                <div class="panel-body">
                                    <table class="table table-bordered table-striped table-hover">
                                       <thead>
                                            <tr>
                                                <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th> 
                                                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                                <th scope="col" style='background-color:#B9D5CE; width: 80%;'>Observación</th>  
                                            </tr>
                                       </thead>  
                                        <tr>
                                            <td><?= $debaja->fecha_proceso?></td>
                                            <td><?= $debaja->usuario?></td>
                                            <td><?= $debaja->observacion ?></td> 
                                        </tr>    
                                     </table>
                              </div>
                           </div>   
                              
                        </div>
                     </div>
                </tr>
        <?php } ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_Maquina') ?>:</th>
                    <td><?= Html::encode($model->codigo_maquina) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_tipo') ?>:</th>
                    <td><?= Html::encode($model->tipo->descripcion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Marca') ?>:</th>
                    <td><?= Html::encode($model->marca->descripcion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Serial') ?>:</th>
                    <td><?= Html::encode($model->serial) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Modelo') ?>:</th>
                    <td><?= Html::encode($model->modelo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Codigo') ?>:</th>
                    <td><?= Html::encode($model->codigo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?>:</th>
                    <td><?= Html::encode($model->id_maquina) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?>:</th>
                    <td><?= Html::encode($model->usuario) ?></td>
                </tr>
                 <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_compra') ?>:</th>
                    <td><?= Html::encode($model->fecha_compra) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Ultimo_Mto') ?>:</th>
                    <td><?= Html::encode($model->fecha_ultimo_mantenimiento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nuevo_Mto') ?>:</th>
                    <td ><?= Html::encode($model->fecha_nuevo_mantenimiento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Bodega') ?>:</th>
                    <td ><?= Html::encode($model->bodega->descripcion) ?></td>
                </tr>
                
            </table>
        </div>
    </div>
    <!--INICIO LOS TABS-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#mantenimiento" aria-controls="mantenimiento" role="tab" data-toggle="tab">Mantenimiento <span class="badge"><?= count($mantenimiento) ?></span></a></li>
        </ul>
        <?php if($maquina->estado_maquina == 0){?>
            <div class="panel-footer text-right">
                      <!-- Inicio Nuevo Detalle proceso -->
                        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear mantenimiento',
                            ['/maquinas/mantenimiento_maquina', 'id' =>$model->id_maquina],
                            [
                                'title' => 'Crear mantenimiento de maquinas',
                                'data-toggle'=>'modal',
                                'data-target'=>'#modalmantenimientomaquina',
                                'class' => 'btn btn-info btn-xs'
                            ])    
                       ?>
                    </div> 
                    <div class="modal remote fade" id="modalmantenimientomaquina"><?= $model->id_maquina?>
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content"></div>
                        </div>
                    </div>
            </div>
        <?php }?>   
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="mantenimiento">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Servicio</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Mecánico</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha Mto</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th> 
                                        <th scope="col" style='background-color:#B9D5CE; width: 530px;'>Observación</th> 
                                          <th scope="col" style='background-color:#B9D5CE;'></th> 
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    foreach ($mantenimiento as $val):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $val->id_mantenimiento?></td>
                                            <td><?= $val->servicio->servicio?></td>
                                            <td><?= $val->mecanico->nombre_completo?></td>
                                            <td><?= $val->fecha_mantenimiento?></td>
                                            <td><?= $val->usuario?></td>
                                            <td><?= $val->observacion?></td>
                                            <?php if($maquina->estado_maquina == 0){?>
                                                <td style=' width: 25px;'>
                                                         <!-- Inicio Nuevo Detalle proceso -->
                                                           <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ',
                                                               ['/maquinas/editarobservacion', 'id' =>$model->id_maquina,'id_mto' => $val->id_mantenimiento],
                                                               [
                                                                   'title' => 'Editar mantenimiento',
                                                                   'data-toggle'=>'modal',
                                                                   'data-target'=>'#modaleditarmantenimiento',
                                                                   'class' => 'btn btn-success btn-xs'
                                                               ])    
                                                          ?>
                                                       </div> 
                                                       <div class="modal remote fade" id="modaleditarmantenimiento"><?= $model->id_maquina?>
                                                           <div class="modal-dialog modal-dialog">
                                                               <div class="modal-content"></div>
                                                           </div>
                                                       </div>
                                                 </td>
                                            <?php }else{?>
                                                 <td style=' width: 25px;'></td>
                                            <?php }?>     
                                        </tr>
                                <?php endforeach;
                                ?>
                                    
                                </tbody>      
                            </table>
                        </div>
                    </div>   
                </div>
                 
               
            <!--INICIO EL OTRO TABS -->
        </div>
    </div>    
</div>
