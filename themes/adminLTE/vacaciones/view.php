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

$this->title = 'Detalle vacaciones';
$this->params['breadcrumbs'][] = ['label' => 'Vacaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_vacacion;
$view = 'vacaciones';
?>
<div class="operarios-view">
   <!--<?= Html::encode($this->title) ?>-->
    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
         <?php
        if ($model->estado_autorizado == 0) { ?>
            <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->id_vacacion, 'token' => $token], ['class' => 'btn btn-default btn-sm']); 
        } else {
            if ($model->estado_cerrado == 0){
                echo Html::a('<span class="glyphicon glyphicon-refresh"></span> Desautorizar', ['desautorizado', 'id' => $model->id_vacacion, 'token' => $token], ['class' => 'btn btn-default btn-sm']);
                echo Html::a('<span class="glyphicon glyphicon-ok"></span> Cerrar', ['cerrarvacacion', 'id' => $model->id_vacacion, 'token' => $token], ['class' => 'btn btn-default btn-sm',
                'data' => ['confirm' => '¿Esta seguro de cerrar el proceso de vacaciones para el empleado '. $model->empleado->nombrecorto. ' ?', 'method' => 'post']]);
                echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimirvacacion', 'id' => $model->id_vacacion], ['class' => 'btn btn-default btn-sm']);            
            } else {
                 echo Html::a('<span class="glyphicon glyphicon-refresh"></span> Desautorizar', ['desautorizado', 'id' => $model->id_vacacion, 'token' => $token], ['class' => 'btn btn-default btn-sm disabled']);
                 echo Html::a('<span class="glyphicon glyphicon-ok"></span> Cerrar', ['cerrarvacacion', 'id' => $model->id_vacacion, 'token' => $token], ['class' => 'btn btn-default btn-sm disabled',
                'data' => ['confirm' => 'Esta seguro de cerrar el registro de las vacaciones con Nro '. $model->id_vacacion. '.', 'method' => 'post']]);
                if($model->estado_anulado == 0){ 
                    echo Html::a('<span class="glyphicon glyphicon-remove"></span> Anular', ['anularvacacion', 'id' => $model->id_vacacion, 'token' => $token], ['class' => 'btn btn-warning btn-sm ',
                   'data' => ['confirm' => 'Esta seguro de anular las vacaciones  del (a) empleado(a) '. $model->empleado->nombrecorto. '.', 'method' => 'post']]);
                } else {
                    echo Html::a('<span class="glyphicon glyphicon-remove"></span> Anular', ['anularvacacion', 'id' => $model->id_vacacion, 'token' => $token], ['class' => 'btn btn-default btn-sm disabled']);
                }    
                echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimirvacacion', 'id' => $model->id_vacacion], ['class' => 'btn btn-default btn-sm']);     
                echo Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 14, 'codigo' => $model->id_vacacion,'view' => $view, 'token' => $token], ['class' => 'btn btn-info btn-sm']);
            }    
        }  
   ?>     
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 84%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_vacacion') ?>:</th>
                    <td><?= Html::encode($model->id_vacacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Documento') ?>:</th>
                    <td><?= Html::encode($model->documento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Empleado') ?>:</th>
                    <td><?= Html::encode($model->empleado->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Inicio_vacacion') ?>:</th>
                    <td><?= Html::encode($model->fecha_desde_disfrute) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Final_vacacion') ?>:</th>
                    <td><?= Html::encode($model->fecha_hasta_disfrute) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Salario') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->salario_contrato,0)) ?></td>
                </tr>
                <tr style="font-size: 84%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_cont.') ?>:</th>
                    <td><?= Html::encode($model->id_contrato) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_pago') ?>:</th>
                    <td><?= Html::encode($model->nro_pago) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Grupo_pago') ?>:</th>
                    <td><?= Html::encode($model->grupoPago->grupo_pago) ?></td>
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Periodo_desde') ?>:</th>
                    <td><?= Html::encode($model->fecha_inicio_periodo) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Periodo_hasta') ?>:</th>
                    <td><?= Html::encode($model->fecha_final_periodo) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Promedio') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->salario_promedio,0)) ?></td>
                </tr>
                <tr style="font-size: 84%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_dias.') ?>:</th>
                    <td><?= Html::encode($model->dias_totales_periodo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Inicio_contrato') ?>:</th>
                    <td><?= Html::encode($model->fecha_ingreso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_proceso') ?>:</th>
                    <td><?= Html::encode($model->fecha_proceso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Dcto_eps') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->descuento_eps,0)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Dcto_pension') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->descuento_pension,0)) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Vlr_vacacion') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->total_pago_vacacion,0)) ?></td>
                </tr>
                 <tr style="font-size: 84%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Dias_vacacion.') ?>:</th>
                    <td><?= Html::encode($model->dias_total_vacacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Dias_disfrutados') ?>:</th>
                    <td><?= Html::encode($model->dias_disfrutados) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Vlr_pagado') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->vlr_vacacion_disfrute,0)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Dias_en_dinero') ?>:</th>
                    <td><?= Html::encode($model->dias_pagados) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Vlr_pagado') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->vlr_vacacion_dinero,0)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Descuentos') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->total_descuentos,0)) ?></td>
                </tr>
                <tr style="font-size: 84%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Ausentismo') ?>:</th>
                    <td><?= Html::encode($model->dias_ausentismo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cta') ?>:</th>
                    <td style="text-align: right"><?= Html::encode($model->empleado->cuenta_bancaria) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Ibp_nocturno') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->vlr_recargo_nocturno,0)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Abierto') ?>:</th>
                    <td><?= Html::encode($model->estadocerrado) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Bonificacion') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->total_bonificaciones,0)) ?></td>
                </tr>    
                <tr style="font-size: 84%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Observacion') ?>:</th>
                    <td colspan="9"><?= Html::encode($model->observacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Total_pagar') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->total_pagar,0)) ?></td>
                </tr>    
            </table>
        </div>
    </div>
    <!--INICIO LOS TABS-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <?php
             $cont = count($vacacion_adicion);
            /*  $inca = count($incapacidad);
              $lice = count($licencia);
              $cred = count($credito);
              $estu = count($estudio);*/
             ?>
            <li role="presentation" class="active"><a href="#adiciones" aria-controls="bonificacion" role="tab" data-toggle="tab">Adiciones <span class="badge"><?= $cont ?></span></a></li>

        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="adiciones">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Id</b></th>  
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Nro vacación</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Concepto</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Tipo_Dcto</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Valor descuento</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Fecha proceso</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Usuario</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Observación</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                         <th scope="col" style='background-color:#B9D5CE;'></th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $tipo = '';
                                     foreach ($vacacion_adicion as $descuento):
                                         if ($descuento->tipo_adicion == 1){
                                             $tipo = 'BONIFICACION';
                                         }else{
                                             $tipo = 'DESCUENTO';
                                         }
                                         ?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $descuento->id_adicion ?></td>
                                            <td><?= $descuento->id_vacacion ?></td>
                                            <td><?= $descuento->codigoSalario->nombre_concepto ?></td>
                                            <td><?= $tipo ?></td>
                                            <td><?= '$'.number_format($descuento->valor_adicion,0) ?></td>
                                            <td><?= $descuento->fecha_creacion ?></td>
                                            <td><?= $descuento->usuariosistema ?></td>
                                            <td><?= $descuento->observacion ?></td>
                                            <td style="width: 25px;">
                                                <a href="<?= Url::toRoute(["vacaciones/update", "id_adicion" => $descuento->id_adicion, "tipo_adicion"=>$descuento->tipo_adicion, 'id' => $descuento->id_vacacion, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                                            </td>
                                            <td style= 'width: 25px;' >
                                                <?= Html::a('', ['eliminaradicion', 'id_adicion' => $descuento->id_adicion, 'id' => $descuento->id_vacacion, 'token' => $token], [
                                                        'class' => 'glyphicon glyphicon-trash',
                                                        'data' => [
                                                            'confirm' => 'Esta seguro de eliminar el registro?',
                                                            'method' => 'post',
                                                        ],
                                                    ]) ?>
                                               
                                           </td>    
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                        </div>
                    </div>   
                </div>
                <?php
                if($model->estado_autorizado == 0){?>
                    <div class="panel-footer text-right"> 
                         <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Adición', ['vacaciones/adicionsalario', 'id' => $model->id_vacacion, 'token' => $token], ['class' => 'btn btn-primary btn-sm']) ?>
                        <?= Html::a('<span class="glyphicon glyphicon-minus-sign"></span> Descuento', ['vacaciones/descuento', 'id' => $model->id_vacacion, 'token' => $token], ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                <?php }else{ ?>
                     <div class="panel-footer text-right"> 
                        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Adición', ['vacaciones/adicionsalario', 'id' => $model->id_vacacion], ['class' => 'btn btn-primary btn-sm disabled']) ?>
                         <?= Html::a('<span class="glyphicon glyphicon-minus-sign"></span> Descuento', ['vacaciones/descuento', 'id' => $model->id_vacacion], ['class' => 'btn btn-primary btn-sm disabled']) ?>
                    </div>
                <?php } ?>
            </div>
            <!--INICIO EL OTRO TABS -->
        </div>
    </div>    
</div>
