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
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?></th>
                    <td><?= Html::encode($model->id_maquina) ?></td>
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
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_compra') ?></th>
                    <td><?= Html::encode($model->fecha_compra) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?></th>
                    <td><?= Html::encode($model->usuario) ?></td>
                </tr>
                
            </table>
        </div>
    </div>
    <!--INICIO LOS TABS-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#mantenimiento" aria-controls="mantenimiento" role="tab" data-toggle="tab">Mantenimiento <span class="badge"><?= 1 ?></span></a></li>

        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="mantenimiento">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Código</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Descripción de maquina</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th> 
                                          <th scope="col" style='background-color:#B9D5CE;'></th> 
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                </tbody>      
                            </table>
                        </div>
                    </div>   
                </div>
                <div class="panel-footer text-right"> 
                        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear', ['maquinas/mantenimiento', 'id' => $model->id_maquina], ['class' => 'btn btn-primary btn-sm']) ?>
               </div>
            </div>
            <!--INICIO EL OTRO TABS -->
        </div>
    </div>    
</div>
