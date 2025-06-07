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

$this->title = 'PROCESO DISCIPLINARIO';
$this->params['breadcrumbs'][] = ['label' => 'Proceso disciplinario', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_proceso;
$view = 'proceso-disciplinario';
?>
<div class="proveedor-view">

    <!--<?= Html::encode($this->title) ?>-->
    <p>
        <?php if($token == 0){?>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php }else{?>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index_search'], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php }?> 
        <?php if ($model->autorizado == 0 && $model->numero_radicado == 0) { ?>
            <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->id_proceso, 'token' => $token], ['class' => 'btn btn-default btn-sm']);
        }else{
            if ($model->autorizado == 1 && $model->numero_radicado == 0){
                    echo Html::a('<span class="glyphicon glyphicon-refresh"></span> Desautorizar', ['autorizado', 'id' => $model->id_proceso, 'token' => $token], ['class' => 'btn btn-default btn-sm']);
                    echo Html::a('<span class="glyphicon glyphicon-check"></span> Generar consecutivo', ['generar_consecutivo', 'id' => $model->id_proceso, 'token'=> $token],['class' => 'btn btn-warning btn-sm',
                               'data' => ['confirm' => 'Esta seguro de generar el consecutivo para este proceso disciplinario. Tener presente que al  generar el CONSECUTIVO el proceso se cierra.', 'method' => 'post']]);
                    echo Html::a('<span class="glyphicon glyphicon-print"></span> Visualizar PDF', ['imprimir_proceso', 'id' => $model->id_proceso], ['class' => 'btn btn-default btn-sm']);
            } else {
                echo Html::a('<span class="glyphicon glyphicon-print"></span> Visualizar PDF', ['imprimir_proceso', 'id' => $model->id_proceso], ['class' => 'btn btn-default btn-sm']);
                echo Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 22, 'codigo' => $model->id_proceso,'view' => $view, 'token' => $token,], ['class' => 'btn btn-default btn-sm']) ?>
            <?php }
        }    ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
          Registros del proceso
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>Id:</th>
                    <td><?= $model->id_proceso ?></td>
                    <th style='background-color:#F0F3EF;'>Documento:</th>
                    <td><?= $model->empleado->identificacion ?></td>
                    <th style='background-color:#F0F3EF;'>Empleado:</th>
                    <td><?= $model->empleado->nombrecorto ?></td>
                    <th style='background-color:#F0F3EF;' >Tipo proceso:</th>
                    <td style="background-color: <?= $model->tipoDisciplinario->color_proceso?>"><?= $model->tipoDisciplinario->concepto ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>Fecha de la falta:</th>
                    <td><?= $model->fecha_falta ?></td>
                    <th style='background-color:#F0F3EF;'>Fecha inicio suspension:</th>
                    <td><?= $model->fecha_inicio_suspension ?></td>
                    <th style='background-color:#F0F3EF;'>Fecha final suspension:</th>
                    <td><?= $model->fecha_final_suspension ?></td>
                    <th style='background-color:#F0F3EF;' >Motivo:</th>
                    <td><?= $model->motivo->concepto ?></td>
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
    <!--INICIO LOS TABS-->
        
    <div>
        <!-- Nav tabs -->
        <?php
        if($model->tipoDisciplinario->codigo_interface == 1){?>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#detalle_proceso" aria-controls="detalle_proceso" role="tab" data-toggle="tab">Detalle del proceso </a></li>
            </ul>
        <?php }else{?>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#detalle_proceso" aria-controls="detalle_proceso" role="tab" data-toggle="tab">Detalle del proceso </a></li>
                <li role="presentation" ><a href="#descargo_proceso" aria-controls="descargo_proceso" role="tab" data-toggle="tab">Acta de descargo </a></li>
            </ul>
        <?php }
        if($model->tipoDisciplinario->codigo_interface == 1){?>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="detalle_proceso">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr style="font-size: 85%;">
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'>LLamado / Suspension del contrato</th> 
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'></th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="font-size: 85%;">
                                             <td> <?= $model->descripcion_proceso ?> </td> 
                                             <?php 
                                             if($model->autorizado == 0){?>
                                                <td style="width: 25px; height: 25px;">
                                                   <!-- Inicio Nuevo Detalle proceso -->
                                                   <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ',
                                                         ['/proceso-disciplinario/actualizar_texto_llamado', 'id' => $model->id_proceso, 'token' => $token],
                                                         [
                                                             'title' => 'Actualizar el texto del llamado de atencion.',
                                                             'data-toggle'=>'modal',
                                                             'data-target'=>'#modalactualizartextollamado'.$model->id_proceso,
                                                             'data-backdrop' => 'static',
                                                         ])    
                                                   ?>
                                                   <div class="modal remote fade" id="modalactualizartextollamado<?= $model->id_proceso ?>">
                                                        <div class="modal-dialog modal-lg" style ="width: 1000px;">
                                                             <div class="modal-content"></div>
                                                        </div>
                                                   </div>
                                               </td>
                                            <?php }else{ ?>
                                               <td style="width: 25px; height: 25px;"></td>
                                            <?php } ?>   
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php }else{?>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="detalle_proceso">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr style="font-size: 85%;">
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'>Suspension del contrato</th> 
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'></th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="font-size: 85%;">
                                             <td> <?= $model->descripcion_proceso ?> </td> 
                                             <?php 
                                             if($model->autorizado == 0){?>
                                                <td style="width: 25px; height: 25px;">
                                                   <!-- Inicio Nuevo Detalle proceso -->
                                                   <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ',
                                                         ['/proceso-disciplinario/actualizar_texto_llamado', 'id' => $model->id_proceso, 'token' => $token],
                                                         [
                                                             'title' => 'Actualizar el texto del llamado de atencion.',
                                                             'data-toggle'=>'modal',
                                                             'data-target'=>'#modalactualizartextollamado'.$model->id_proceso,
                                                             'data-backdrop' => 'static',
                                                         ])    
                                                   ?>
                                                   <div class="modal remote fade" id="modalactualizartextollamado<?= $model->id_proceso ?>">
                                                        <div class="modal-dialog modal-lg" style ="width: 1000px;">
                                                             <div class="modal-content"></div>
                                                        </div>
                                                   </div>
                                               </td>
                                            <?php }else{ ?>
                                               <td style="width: 25px; height: 25px;"></td>
                                            <?php } ?>   
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <!--TERMINATABS-->
                 <div role="tabpanel" class="tab-pane " id="descargo_proceso">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr style="font-size: 85%;">
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'>Acta de descargo</th> 
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'></th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="font-size: 85%;">
                                             <td> <?= $model->proceso_descargo ?> </td> 
                                             <?php 
                                             if($model->autorizado == 0){?>
                                                <td style="width: 25px; height: 25px;">
                                                   <!-- Inicio Nuevo Detalle proceso -->
                                                   <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ',
                                                         ['/proceso-disciplinario/acta_descargo_empleado', 'id' => $model->id_proceso, 'token' => $token],
                                                         [
                                                             'title' => 'Permite crear los descargos del empleado.',
                                                             'data-toggle'=>'modal',
                                                             'data-target'=>'#modalactadescargoempleado'.$model->id_proceso,
                                                             'data-backdrop' => 'static',
                                                         ])    
                                                   ?>
                                                   <div class="modal remote fade" id="modalactadescargoempleado<?= $model->id_proceso ?>">
                                                        <div class="modal-dialog modal-lg" style ="width: 1200px;">
                                                             <div class="modal-content"></div>
                                                        </div>
                                                   </div>
                                               </td>
                                            <?php }else{ ?>
                                               <td style="width: 25px; height: 25px;">
                                                   <?= Html::a('<span class="glyphicon glyphicon-print"></span>', ['imprimir_suspension', 'id' => $model->id_proceso]);?>
                                               </td>
                                            <?php } ?>   
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
            <!--TERMINA TABS-->        

    </div>
     <?php ActiveForm::end(); ?> 
</div>
