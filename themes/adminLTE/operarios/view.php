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

$this->title = 'Detalle operario';
$this->params['breadcrumbs'][] = ['label' => 'Operario', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_operario;
$view = 'operarios';
?>
<div class="operarios-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
	<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_operario], ['class' => 'btn btn-success btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 12, 'codigo' => $model->id_operario,'view' => $view], ['class' => 'btn btn-default btn-sm']) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_operario') ?>:</th>
                    <td><?= Html::encode($model->id_operario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipo_documento') ?>:</th>
                    <td><?= Html::encode($model->tipoDocumento->descripcion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Documento') ?>:</th>
                    <td><?= Html::encode($model->documento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Operario') ?>:</th>
                    <td><?= Html::encode($model->nombrecompleto) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Celular') ?>:</th>
                    <td><?= Html::encode($model->celular) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'email') ?>:</th>
                    <td><?= Html::encode($model->email) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Departamento') ?>:</th>
                    <td><?= Html::encode($model->departamento->departamento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Municipio') ?>:</th>
                    <td><?= Html::encode($model->municipio->municipio) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Polivalente') ?>:</th>
                    <td><?= Html::encode($model->polivalenteOperacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_nacimiento') ?>:</th>
                    <td><?= Html::encode($model->fecha_nacimiento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Activo') ?>:</th>
                    <td colspan="4"><?= Html::encode($model->estadoPago) ?></td>
                 
                </tr>
                 <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_Ingreso') ?>:</th>
                    <td><?= Html::encode($model->fecha_ingreso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_creacion') ?>:</th>
                    <td><?= Html::encode($model->fecha_creacion) ?></td>
                    <td colspan="5"></td>
                    
                 
                </tr>
                
            </table>
        </div>
    </div>
    <!--INICIO LOS TABS-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <?php
              $contMaquina = count($maquina_operario);
             ?>
            <li role="presentation" class="active"><a href="#maquinas" aria-controls="maquinas" role="tab" data-toggle="tab">Maquinas: <span class="badge"><?= $contMaquina ?></span></a></li>

        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="maquinas">
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
                                    <?php
                                     foreach ($maquina_operario as $maquina):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $maquina->id ?></td>
                                            <td><?= $maquina->tipo->descripcion ?></td>
                                            <td><?= $maquina->cantidad ?></td>
                                            <td><?= $maquina->fecha_registro ?></td>
                                            <td><?= $maquina->usuariosistema ?></td>
                                            <td style= 'width: 25px;' >
                                                <!-- Eliminar modal detalle -->
                                                <a href="#" data-toggle="modal" data-target="#idmaquina<?= $maquina->id ?>"><span class="glyphicon glyphicon-trash"></span></a>
                                                <div class="modal fade" role="dialog" aria-hidden="true" id="idmaquina<?= $maquina->id ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                                <h4 class="modal-title">Eliminar registro (SYSTIME)</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>¿ESTA SEGURO QUE DESEA ELIMINAR EL REGISTRO Nro : <?= $maquina->id ?>?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <?= Html::beginForm(Url::toRoute("operarios/eliminardetalle"), "POST") ?>
                                                                <input type="hidden" name="id_tipo" value="<?= $maquina->id ?>">
                                                                <input type="hidden" name="id" value="<?= $model->id_operario ?>">
                                                                <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                                <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Eliminar</button>
                                                                <?= Html::endForm() ?>
                                                            </div>
                                                        </div><!-- /.modal-content -->
                                                    </div><!-- /.modal-dialog -->
                                                </div><!-- /.modal -->
                                           </td>    
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                        </div>
                    </div>   
                </div>
                <div class="panel-footer text-right"> 
                        <?= Html::a('<span class="glyphicon glyphicon-search"></span> Buscar maquinas', ['operarios/relacionmaquina', 'id' => $model->id_operario], ['class' => 'btn btn-primary btn-sm']) ?>
               </div>
            </div>
            <!--INICIO EL OTRO TABS -->
        </div>
    </div>    
</div>
