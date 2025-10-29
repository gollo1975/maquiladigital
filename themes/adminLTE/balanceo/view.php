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
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$this->title = 'Detalle modulo';
$this->params['breadcrumbs'][] = ['label' => 'Detalle modulo', 'url' => ['proceso']];
$this->params['breadcrumbs'][] = $model->idordenproduccion;
$operarios = ArrayHelper::map(\app\models\Operarios::find()->where(['=','estado', 1])->andWhere(['=','id_planta', $model->id_planta])->orderBy('nombrecompleto ASC')->all(), 'id_operario', 'nombrecompleto');
?>
<div class="ordenproduccionproceso-view">
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-default btn"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'],['class' => 'btn btn-primary btn-xs']) ?></button>
        <?php if($model->estado_modulo == 0){?>
           <button type="button" class="btn btn-default btn"> 
            <?= Html::a('<span class="glyphicon glyphicon-user"></span> Nueva cantidad',            
                ['/balanceo/nuevacantidad','id' => $model->id_balanceo,'id_proceso_confeccion' => $id_proceso_confeccion, 'idordenproduccion' => $model->idordenproduccion, 'id_planta' => $model->id_planta],
                [
                    'title' => 'Nueva cantidad de operarios',
                    'data-toggle'=>'modal',
                    'data-target'=>'#modalnuevacantidad'.$model->id_balanceo,
                    'class' => 'btn btn-info btn-sm'
                ]
            )?>
            <?= Html::a('<span class="glyphicon glyphicon-user"></span> Cerrar modulo',            
             ['/balanceo/cerrarmodulo','id' => $model->id_balanceo,'id_proceso_confeccion' => $id_proceso_confeccion, 'idordenproduccion' => $model->idordenproduccion],
             [
                 'title' => 'Cerrar modulo de confección',
                 'data-toggle'=>'modal',
                 'data-target'=>'#modalcerrarmodulo'.$model->id_balanceo,
                 'class' => 'btn btn-warning btn-sm'
             ]
            );
            }
        ?></button>
    </div>
    <div class="modal remote fade" id="modalnuevacantidad<?= $model->id_balanceo ?>" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content"></div>
        </div>
    </div>
     <div class="modal remote fade" id="modalcerrarmodulo<?= $model->id_balanceo ?>" data-backdrop="static">
        <div class="modal-dialog modal-lg-centered">
           <div class="modal-content"></div>
        </div>
    </div>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle del registro
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id')?>:</th>
                    <td><?= Html::encode($model->id_balanceo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_modulo') ?>:</th>
                    <td><?= Html::encode($model->modulo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Orden_Produccion') ?>:</th>
                    <td><?= Html::encode($model->idordenproduccion)?> - <h7><b>Referencia:</b></h7> <?= Html::encode($model->ordenproduccion->codigoproducto)?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Estado') ?>:</th>
                    <td><?= Html::encode($model->estadomodulo) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Operarios') ?>:</th>
                    <td align="right"><?= Html::encode($model->cantidad_empleados) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_inicio') ?>:</th>
                    <td><?= Html::encode($model->fecha_inicio .  '     ('. $model->hora_inicio.')') ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_terminación') ?>:</th>
                    <td><?= Html::encode($model->fecha_terminacion) ?> -<b>Dias:</b> (<?= Html::encode($model->numero_dias_balanceo)?>)</td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?>:</th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Minutos') ?>:</th>
                    <td><?= Html::encode($model->tiempo_operario,2) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Segundos') ?>:</th>
                    <td align="right"><?= Html::encode($model->tiempo_operario * 60) ?></td>
                 
                </tr>
                <tr style="font-size: 85%;">
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Total_segundos') ?>:</th>
                    <td><?= Html::encode($model->total_segundos) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Sam_operativo') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion->sam_operativo) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Tipo_proceso') ?>:</th>
                    <td><?= Html::encode($model->procesoconfeccion->descripcion_proceso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Reproceso') ?>:</th>
                    <td><?= Html::encode($model->verreproceso) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Hora_inicial') ?></th>
                    <td><?= Html::encode($model->hora_inicio) ?></td>
                </tr>
                  <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Sam_balanceo') ?>:</th>
                    <td><?= Html::encode($model->tiempo_balanceo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_creación') ?>:</th>
                    <td><?= Html::encode($model->fecha_creacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Planta') ?>:</th>
                    <td><?= Html::encode($model->plantaempresa->nombre_planta) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Hora_cierre') ?></th>
                    <td><?= Html::encode($model->hora_cierre_modulo) ?></td>
                </tr>
                 </tr>
                  <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Horario') ?>:</th>
                    <td><?= Html::encode($model->horario->horario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'No_hora_terminación') ?></th>
                    <td><?= Html::encode($model->hora_final_modulo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Observaciones') ?>:</th>
                    <td colspan="6"><?= Html::encode($model->observacion) ?></td>
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
            <li role="presentation" class="active"><a href="#flujo" aria-controls="flujo" role="tab" data-toggle="tab">Operaciones <span class="badge"><?= count($flujo_operaciones) ?></span></a></li>
            <li role="presentation"><a href="#balanceo" aria-controls="balanceo" role="tab" data-toggle="tab">Balanceo <span class="badge"><?= count($balanceo_detalle) ?></span></a></li>
            <?php if($id_proceso_confeccion == 1){?>
               <li role="presentation"><a href="#automatico" aria-controls="automatico" role="tab" data-toggle="tab">Automatico</a></li>
            <?php }?>  
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="flujo">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            
                            <div class="panel panel-success" style="width: 50%; margin: 0 auto; float: left">
                                <div class="panel-heading">
                                    
                                </div>
                                <table class="table table-bordered table-hover">
                                  
                                    <td style="padding-left: 1; padding-right: 0;">
                                        <?= Select2::widget([
                                            'name' => 'id_operario',
                                            'data' => $operarios,
                                            'options' => [
                                                'placeholder' => 'Seleccione el operario...',
                                                'required' => true,
                                                'class' => 'col-sm-18 my-custom-select2', // Mantén tu clase original si la necesitas para estilos
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => true // Permite limpiar la selección
                                            ],
                                        ]); ?>
                                    </td>
                                    
                                </table>  
                            </div>    
                         
                            <table class="table table-bordered table-hover">
                                <thead>
                                     
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar('checkbox_proceso1', this);"/></th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Operaciones</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Segundos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Minutos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Orden</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Proceso</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Pieza</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Maquina</th>
                                                                         
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalsegundos = 0;
                                    $totalminutos = 0;
                                    $sam_balanceo = 0;
                                    $total = 0;
                                    $id_procesos_array = \yii\helpers\ArrayHelper::getColumn($flujo_operaciones, 'idproceso');

                                    // 2. Hacemos UNA sola consulta para obtener todos los detalles de balanceo necesarios
                                    //    indexBy('id_proceso') crea un array asociativo para una búsqueda rápida en el bucle
                                    $operaciones_modulos = app\models\BalanceoDetalle::find()
                                        ->where(['id_balanceo' => $model->id_balanceo])
                                        ->andWhere(['in', 'id_proceso', $id_procesos_array])
                                        ->indexBy('id_proceso')
                                        ->all();
                                    foreach ($flujo_operaciones as $val): 
                                    // Tu lógica de cálculo de totales
                                    $totalminutos += $val->minutos;
                                    $totalsegundos += $val->segundos;
                                    if ($val->operacion == 0) {
                                        $sam_balanceo += $val->minutos;
                                    } else {
                                        $sam_balanceo += $val->minutos;
                                    }
                                    ?>
                                    <tr style="font-size: 85%;">
                                        <td style="width: 30px;">
                                            <input type="checkbox"  class="checkbox_proceso1" name="idproceso[]" value="<?= $val->idproceso ?>">

                                        </td>

                                        <!-- Ahora verificamos la existencia de la operación en el array pre-cargado -->
                                        <?php $operacionModulo = isset($operaciones_modulos[$val->idproceso]); ?>

                                        <td><?= $val->proceso->idproceso ?></td>

                                        <?php if ($operacionModulo) { ?>
                                            <td style='background-color:#BEF1F0;'><?= $val->proceso->proceso ?></td>
                                        <?php } else { ?>
                                            <td><?= $val->proceso->proceso ?></td>
                                        <?php } ?>

                                        <td><?= $val->segundos ?></td>
                                        <td><?= $val->minutos ?></td>
                                        <td><?= $val->orden_aleatorio ?></td>

                                        <?php if ($val->operacion == 0) { ?>
                                            <td style='background-color:#B9D5CE;'><?= 'BALANCEO' ?></td>
                                        <?php } else { ?>
                                            <td style='background-color:#A5D3E6;'><?= 'PREPARACION' ?></td>
                                        <?php } ?>

                                        <?php if ($val->pieza == 0) { ?>
                                            <td style='background-color:#ACF1D8;'><?= 'PIEZA 1' ?></td>
                                        <?php } else { ?>
                                            <td style='background-color:#E3CDFC;'><?= 'PIEZA 2' ?></td>
                                        <?php } ?>

                                        <td><?= $val->tipomaquina->descripcion?></td>

                                        <!-- Campos hidden (no tienen efecto visual pero se envían en el formulario) -->
                                        <input type="hidden" name="id_balanceo[]" value="<?= $model->id_balanceo ?>">
                                        <input type="hidden" name="id_tipo[]" value="<?= $val->id_tipo ?>">
                                        <input type="hidden" name="segundos[]" value="<?= $val->segundos ?>">
                                        <input type="hidden" name="minutos[]" value="<?= $val->minutos ?>">
                                        <input type="hidden" name="totalminutos[]" value="<?= $totalminutos ?>">
                                        <input type="hidden" name="totalsegundos[]" value="<?= $totalsegundos ?>">
                                        <!-- CORRECCIÓN: Los siguientes campos DEBEN ser de tipo hidden para que se envíen al controlador -->
                                        <input type="hidden" name="idordenproduccion" value="<?= $idordenproduccion ?>">
                                        <input type="hidden" name="id" value="<?= $model->id_balanceo ?>">
                                        <input type="hidden" name="id_planta" value="<?= $id_planta ?>">
                                        <input type="hidden" name="id_proceso_confeccion" value="<?= $id_proceso_confeccion ?>">
                                    </tr>
                                <?php endforeach;?> 
                                    
                                    <?php
                                    $model->tiempo_balanceo = $sam_balanceo;
                                    if($model->cantidad_empleados == 0){
                                    }else{
                                        $model->tiempo_operario = $sam_balanceo/$model->cantidad_empleados;
                                         $model->save(false);
                                    }
                           
                                  
                                  
                                   ?>
                                </tbody>  
                                <?php if($model->id_proceso_confeccion == 1){?>
                                      <td colspan="3"></td><td style="font-size: 85%;background: #194E7B; color: #FFFFFF; width: 120px;" ><b>Segundos:</b> <?= $totalsegundos ?> <td style="font-size: 85%;background: #0B5345; color: #FFFFFF; width: 142px;"><b>Sam_Operativo:</b> <?= $model->ordenproduccion->sam_operativo ?></td><td style="font-size: 85%;background: #0B5345; color: #FFFFFF; width: 149px;"><b>Sam_balanceo:</b> <?= $model->ordenproduccion->sam_balanceo ?></td><td colspan="3"></td>
                                <?php }else{ ?>
                                      <td colspan="3"></td><td style="font-size: 85%;background: #194E7B; color: #FFFFFF; width: 120px;" ><b>Segundos:</b> <?= $totalsegundos ?> <td style="font-size: 85%;background: #0B5345; color: #FFFFFF; width: 142px;"><b>Sam_Operativo:</b> <?= $model->ordenproduccion->sam_operativo ?></td><td style="font-size: 85%;background: #0B5345; color: #FFFFFF; width: 149px;"><b>Sam_preparación:</b> <?= $model->ordenproduccion->sam_preparacion ?></td><td colspan="3"></td>
                                <?php } ?>      
                            </table>
                        </div>   
                        <?php
                        if($model->estado_modulo == 0){?>
                            <div class="panel-footer text-right">
                                <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm", 'name' => 'guardar']) ?>
                            </div>
                        <?php }?>
                    </div>
                </div>    
            </div>
                <!--- TERMINA EL TABS-->
            <div role="tabpanel" class="tab-pane" id="balanceo">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    
                                    <tr style="font-size: 90%;">
                                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operacion</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Minutos x operacion">Min.</span></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Segundos x operacion">Seg.</span></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Tiempo asignado">T. Asig.</span></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Tiempo faltante/Sobrante">F/S</span></th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Maquina</th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Unidades por hora">U. x hora (100%)</span></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Unidades ´por hora al 80%">U. x hora (80%)</span></th>
                                         <th scope="col" style='background-color:#B9D5CE;'><span title="Estado de la operacion">Estado</span></th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $total_mi = 0;
                                        $unidades = 0;
                                        $ochenta = 0; $contador = 0; $sam = 0;
                                         foreach ($balanceo_detalle as $val):
                                            $registro = app\models\BalanceoDetalle::find()->where([
                                                                                           'idordenproduccion' => $idordenproduccion,
                                                                                           'id_operario' => $val->id_operario,
                                                                                           'estado_operacion' => 0])->all();
                                            
                                            foreach ($registro as $variable){
                                                $contador += 1;
                                               $sam += $variable->minutos;
                                            }
                                            if($contador == 1){ 
                                                $porcentaje = (100 * $val->minutos)/ $sam;
                                                $unidades_cien = round(60/$val->minutos);
                                                $unidades_ochenta = (((60/$val->minutos)*80)/100);
                                                $ochenta = (((60 / $model->tiempo_balanceo) * ($model->cantidad_empleados)*80)/100);
                                            }else{
                                                $unidades_hora = round(60/$sam,0);
                                                $porcentaje = (100 * $val->minutos)/ $sam;
                                                $unidades_cien = round(($unidades_hora* $porcentaje)/100);
                                                $unidades_ochenta = round(($unidades_cien * 80)/100);
                                                $ochenta = (((60 / $model->tiempo_balanceo) * ($model->cantidad_empleados)*80)/100);
                                            }    
                                                ?>
                                                    
                                           <tr style="font-size: 85%;">
                                                <td><?= $val->proceso->idproceso?></td>
                                                 <td><?= $val->operario->nombrecompleto ?></td>
                                                <td><?= $val->proceso->proceso ?></td>
                                                <td><?= $val->minutos ?></td>
                                                <td><?= $val->segundos ?></td>
                                                 <td><?= $val->total_minutos ?></td>
                                                 <?php if($val->sobrante_faltante >= 0){?>
                                                 <td style="background: #277da1;; color: #FFFFFF;"><b><?= $val->sobrante_faltante ?></b></td>
                                                 <?php }else{ ?>
                                                     <td style="background: #F5BCA9; color: #DC2905;"><b><?= $val->sobrante_faltante ?></b></td>
                                                 <?php }?>     
                                                 <td><?= $val->tipo->descripcion ?></td>
                                                 <td><?= ''.number_format( $unidades_cien) ?> &nbsp; (<?= round($porcentaje,2) ?>%)</td>
                                                 <td><?= ''.number_format( $unidades_ochenta) ?></td>
                                                 <td><?= $val->estadoperacion ?></td>
                                                <?php

                                                if($model->estado_modulo == 0){?>
                                                    <td style=' width: 25px;'>
                                                      <a href="<?= Url::toRoute(["balanceo/editaroperacionasignada",'id_detalle'=>$val->id_detalle,'id' => $model->id_balanceo, 'idordenproduccion' => $model->idordenproduccion, 'id_proceso_confeccion' => $id_proceso_confeccion, 'id_planta' => $model->id_planta]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                                                    </td> 
                                                    <td style= 'width: 25px;'>
                                                      <?= Html::a('', ['eliminardetalle', 'id_planta' => $id_planta, 'id_detalle' => $val->id_detalle,'id'=>$model->id_balanceo,'idordenproduccion'=>$model->idordenproduccion, 'id_proceso_confeccion' => $id_proceso_confeccion], [
                                                          'class' => 'glyphicon glyphicon-trash',
                                                          'data' => [
                                                              'confirm' => 'Esta seguro de eliminar el registro?',
                                                              'method' => 'post',
                                                          ],
                                                      ]) ?>
                                                    </td>
                                                <?php }else{ ?>
                                                    <td style= 'width: 25px;'></td>
                                                    <td style= 'width: 25px;'></td>

                                                <?php } ?>    

                                            </tr>
                                   <?php
                                        $total_mi += $val->minutos;
                                        $contador = 0;
                                        $sam = 0;
                                        $porcentaje = 0;
                                        $unidades_cien = 0;
                                   endforeach; ?>
                                </tbody>  
                            </table>
                            <?php if(count($balanceo_detalle)> 0){?>
                                <table class="table table-bordered table-hover" style="margin-left: auto; margin-right: auto;">
                                    <tr>
                                        <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                                            <b>Servicio: <?= Html::encode($model->procesoconfeccion->descripcion_proceso) ?>:</b> <?= $model->tiempo_balanceo?></b> 
                                        </td>
                                        <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                                            <b>Total unidades al 100%: <?= ''. number_format((60 / $model->tiempo_balanceo) * $model->cantidad_empleados,0) ?></b> 
                                        </td>
                                        <td colspan="4" style="font-size: 90%; background: #277da1; color: #FFFFFF; text-align: center;">
                                            <b>Total unidades al 80%: </b> <?= ''. number_format($ochenta,0) ?></b> 
                                        </td>

                                    </tr>    
                                </table> 
                            <?php 
                                if($total_mi > $model->total_minutos){
                                    Yii::$app->getSession()->setFlash('warning', 'Importante: El tiempo asignado en el listado de operaciones ('. $total_mi .'), es mayor que el tiempo inicial asignado ('. $model->total_minutos .') ');
                                } 
                           }?>     
                        </div>
                        <div class="panel-footer text-right">
                           <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Expotar excel', ['excelbalanceo', 'id_balanceo' => $model->id_balanceo, 'idordenproduccion'=>$model->idordenproduccion], ['class' => 'btn btn-primary btn-sm']);?>
                        </div>
                    </div>
                </div>    
            </div> <!-- termina el tabs de operaciones-->
             <?php if($id_proceso_confeccion == 1){?>
               <!-- TERMINA EL TABS-->    
              <div role="tabpanel" class="tab-pane" id="automatico">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">

                            <table class="table table-bordered table-hover">
                                <thead>
                                    <!--ESTE ES EL PROCESO AUTOMATICO DEL BALANCEO -->
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar('checkbox_proceso2', this);"/></th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>codigo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Polivalente</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $id = 0;
                                    foreach ($operario as $val):
                                        $maquina = app\models\MaquinaOperario::find()->where(['=','id_operario', $val->id_operario])->all();
                                        foreach ($maquina as $valor):
                                            if($id <> $valor->id_operario){
                                                ?>
                                                 <tr style="font-size: 85%;">
                                                    <td style="width: 30px;">
                                                        <input type="checkbox"  class="checkbox_proceso2" name="id_operario[]" value="<?= $val->id_operario ?>">
                                                    </td>
                                                    <td><?= $val->nombrecompleto ?></td> 
                                                    <td ><?= $val->id_operario ?></td>
                                                    <td ><?= $val->documento ?></td>
                                                     <td ><?= $val->polivalenteOperacion ?></td>
                                                 </tr>
                                            <?php
                                            $id = $val->id_operario;
                                            
                                            }
                                            endforeach;?>
                                   <?php endforeach;
                                   ?>
                                    <input type="hidden" name="id_balanceo" value="<?= $model->id_balanceo ?>">
                                </tbody>  
                            </table>
                        </div>   
                        <?php
                        if($model->estado_modulo == 0){?>
                            <div class="panel-footer text-right">
                                <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Generar balanceo", ["class" => "btn btn-success btn-sm", 'name' => 'generar_balanceo']) ?>
                            </div>
                        <?php }?>
                    </div>
                </div>    
            </div>
             <?php } ?>    
       </div>
        
    </div>  
    <?php ActiveForm::end(); ?>
</div>



<script type="text/javascript">
	function marcar(clase_objetivo, source) 
	{
		//checkboxes=document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
                checkboxes = document.getElementsByClassName(clase_objetivo);
		for(i=0; i < checkboxes.length;i++) //recoremos todos los controles
		{
			checkboxes[i].checked = source.checked;
		}
	}
                                
</script>
