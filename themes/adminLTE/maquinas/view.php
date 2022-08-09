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
?>
<div class="maquinas-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
	<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_maquina], ['class' => 'btn btn-success btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 17, 'codigo' => $model->id_maquina,'view' => $view], ['class' => 'btn btn-default btn-sm']) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro') ?></th>
                    <td><?= Html::encode($model->codigo_maquina) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_tipo') ?></th>
                    <td><?= Html::encode($model->tipo->descripcion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Marca') ?></th>
                    <td><?= Html::encode($model->marca->descripcion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Serial') ?></th>
                    <td><?= Html::encode($model->serial) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Modelo') ?></th>
                    <td><?= Html::encode($model->modelo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Codigo') ?></th>
                    <td><?= Html::encode($model->codigo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?></th>
                    <td><?= Html::encode($model->id_maquina) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?></th>
                    <td><?= Html::encode($model->usuario) ?></td>
                </tr>
                 <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_compra') ?></th>
                    <td><?= Html::encode($model->fecha_compra) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Ultimo_Mto') ?></th>
                    <td><?= Html::encode($model->fecha_ultimo_mantenimiento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nuevo_Mto') ?></th>
                    <td colspan="3"><?= Html::encode($model->fecha_nuevo_mantenimiento) ?></td>
                </tr>
                
            </table>
        </div>
    </div>
    <!--INICIO LOS TABS-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#mantenimiento" aria-controls="mantenimiento" role="tab" data-toggle="tab">Mantenimiento <span class="badge"><?= count($mantenimiento) ?></span></a></li>
        </ul>
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
