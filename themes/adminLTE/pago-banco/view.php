<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\Session;
use yii\db\ActiveQuery;
use app\models\Operarios;

/* @var $this yii\web\View */
/* @var $model app\models\Licencia */

$this->title = 'Detalle del pago';
$this->params['breadcrumbs'][] = ['label' => 'Pago banco', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_pago_banco;
$view = 'pago-banco';
?>
<div class="pago-banco-view">

    <p>
        <?php if($model->autorizado == 0){?>
         <button type="button" class="btn btn-default btn-sm"><?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?></button>
        <button type="button" class="btn btn-default btn-sm">  <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->id_pago_banco], ['class' => 'btn btn-default btn-sm'])?></button>
        <?php }else{
            if($model->cerrar_proceso == 0){?>
                <button type="button" class="btn btn-default btn-sm"><?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?></button>
                <button type="button" class="btn btn-default btn-sm"><?= Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->id_pago_banco], ['class' => 'btn btn-default btn-sm'])?></button>
                <button type="button" class="btn btn-default btn-sm"><?= Html::a('<span class="glyphicon glyphicon-folder-close"></span> Cerrar pago', ['close_cast', 'id' => $model->id_pago_banco, 'tipo_proceso' => $model->id_tipo_nomina], ['class' => 'btn btn-info btn-sm',
                    'data' => ['confirm' => 'Esta seguro que desea cerrar el pago a banco No '. $model->id_pago_banco. '', 'method' => 'post']])?></button>
           <?php }else{?>
                <div class="btn-group" role="group" aria-label="...">
                    <button type="button" class="btn btn-default btn-sm"><?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?></button>
                    <button type="button" class="btn btn-default btn-sm"> <?= Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->id_pago_banco], ['class' => 'btn btn-default btn-sm disabled'])?></button>
                    <button type="button" class="btn btn-default btn-sm"> <?= Html::a('<span class="glyphicon glyphicon-folder-close"></span> Cerrar pago', ['close_cast', 'id' => $model->id_pago_banco], ['class' => 'btn btn-info btn-sm disabled',
                        'data' => ['confirm' => 'Esta seguro que desea cerrar el pago a banco No '. $model->id_pago_banco. '', 'method' => 'post']])?></button>
                    <button type="button" class="btn btn-default btn-sm"><?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimir', 'id' => $model->id_pago_banco], ['class' => 'btn btn-default btn-sm'])?> </button>            
                    <button type="button" class="btn btn-default btn-sm"> <?=  Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 19, 'codigo' => $model->id_pago_banco,'view' => $view], ['class' => 'btn btn-default btn-sm'])?></button>
                     <div class="btn-group" role="group">
                         <button type="button" class="btn btn-success btn-lg dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           Bancolombia
                           <span class="caret"></span>
                         </button>
                           <ul class="dropdown-menu">
                            <li><?= Html::a('<span class="glyphicon glyphicon-save-file"></span> Archivo PAB', ['pagoarchivopab', 'id' => $model->id_pago_banco]) ?></li>
                           </ul>
                     </div>
                </div>    
            <?php }    
        }?>
        
     </p>   
     
    <div class="panel panel-success">
        <div class="panel-heading">
           Registro
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                  <tr style="font-size: 85%;">
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_pago_banco') ?>:</th>
                    <td><?= Html::encode($model->id_pago_banco) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'banco') ?>:</th>
                    <td><?= Html::encode($model->banco->entidad) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipo_pago') ?>:</th>
                    <td><?= Html::encode($model->tipoPago) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'aplicacion') ?>:</th>
                    <td><?= Html::encode($model->aplicacionPago) ?></td>
                   </tr>   
                   <tr style="font-size: 85%;">
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_creacion') ?>:</th>
                        <td><?= Html::encode($model->fecha_creacion) ?></td>
                           <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_aplicacion') ?>:</th>
                        <td><?= Html::encode($model->fecha_aplicacion) ?></td>  
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'secuencia') ?>:</th>
                        <td><?= Html::encode($model->secuencia) ?></td>
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'usuario') ?>:</th>
                        <td><?= Html::encode($model->usuario) ?></td>
                    </tr>  
                    <tr style="font-size: 85%;">
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'autorizado') ?>:</th>
                        <td><?= Html::encode($model->estadoAutorizado) ?></td>
                           <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cerrar_proceso') ?>:</th>
                        <td><?= Html::encode($model->estadoCerrado) ?></td>  
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_empleados') ?>:</th>
                        <td style="text-align: right"><?= Html::encode($model->total_empleados) ?></td>
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_pagar') ?>:</th>
                         <td style="text-align: right"><?= Html::encode(''.number_format($model->total_pagar, 0)) ?></td>
                    </tr>  
                    <tr style="font-size: 85%;">
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_tipo_nomina') ?>:</th>
                        <td><?= Html::encode($model->tipoNomina->tipo_pago) ?></td>
                           <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Observacion') ?>:</th>
                           <td colspan="5"><?= Html::encode($model->descripcion) ?></td>  

                    </tr>  
            </table>
        </div>
    </div>
    <!-- INICIO DEL TABAS*-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <?php
             $List = count($listado);
             ?>
            <li role="presentation" class="active"><a href="#listado" aria-controls="listado" role="tab" data-toggle="tab">Listado de pago <span class="badge"><?= $List ?></span></a></li>

        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="listado">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo documento</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Documento</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Nombres</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo transación</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Codigo banco</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Banco</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>No cuenta</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha aplicacion</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Vr. pagar</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'></th> 
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($listado as $listados):?>
                                        <tr style='font-size:85%;'>
                                            <?php if($listados->tipo_pago == 7){?>
                                                  <td><?= $listados->concepto_documento ?></td>
                                                 <td><?= $listados->documento ?></td>
                                                 <td><?= $listados->nombres ?></td>
                                                 <td><?= $listados->tipoTransacion ?></td> 
                                                 <td><?= $listados->codigo_banco ?></td>
                                                 <td><?= $listados->banco ?></td>
                                                 <?php if($listados->numero_cuenta > 0 ){?>
                                                   <td><?= $listados->numero_cuenta ?></td>
                                                 <?php }else{?>
                                                   <td style='background-color:#CBAAE3;'><?= $listados->numero_cuenta ?></td>
                                                 <?php }?>  
                                                <td><?= $listados->fecha_aplicacion ?></td>
                                                <td style="text-align: right"><?=''.number_format($listados->valor_transacion,0) ?></td>
                                            <?php }
                                            if($listados->tipo_pago == 1 || $listados->tipo_pago == 2 || $listados->tipo_pago == 3){
                                                $empleado = \app\models\Empleado::find()->where(['=','identificacion', $listados->documento])->one();
                                                ?>
                                                <td><?= $empleado->tipoDocumento->descripcion ?></td>
                                                <td><?= $listados->documento ?></td>
                                                <td><?= $listados->nombres ?></td>
                                                <td><?= $empleado->tipoTransacion?></td>
                                                <td><?= $listados->codigo_banco ?></td>
                                                <td><?= $empleado->bancoEmpleado->banco ?></td>
                                                <?php if($listados->numero_cuenta > 0 ){?>
                                                     <td><?= $listados->numero_cuenta ?></td>
                                                <?php }else{?>
                                                    <td style='background-color:#CBAAE3;'><?= $listados->numero_cuenta ?></td>
                                                <?php }?>    
                                                <td><?= $listados->fecha_aplicacion ?></td>
                                                <td style="text-align: right"><?=''.number_format($listados->valor_transacion,0) ?></td>
                                            <?php }
                                            if($model->autorizado == 0){?>
                                                <td style= 'width: 25px;' >
                                                    <!-- Eliminar modal detalle -->
                                                    <a href="#" data-toggle="modal" data-target="#iddetalleproducto<?= $listados->id_detalle ?>"><span class="glyphicon glyphicon-trash"></span></a>
                                                    <div class="modal fade" role="dialog" aria-hidden="true" id="iddetalleproducto<?= $listados->id_detalle ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                                    <h4 class="modal-title">Eliminar registro (SYSTIME)</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>¿ESTA SEGURO QUE DESEA ELIMINAR EL REGISTRO Nro : <?= $listados->id_detalle ?>?</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <?= Html::beginForm(Url::toRoute("pago-banco/eliminar_pago_banco"), "POST") ?>
                                                                    <input type="hidden" name="id_detalle" value="<?= $listados->id_detalle ?>">
                                                                    <input type="hidden" name="id_banco" value="<?= $model->id_pago_banco ?>">
                                                                    <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                                    <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Eliminar</button>
                                                                    <?= Html::endForm() ?>
                                                                </div>
                                                            </div><!-- /.modal-content -->
                                                        </div><!-- /.modal-dialog -->
                                                    </div><!-- /.modal -->
                                               </td>   
                                            <?php }else{?>
                                               <td style= 'width: 25px;' ></td> 
                                            <?php }?>   
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                            <?php if($model->autorizado == 0){?>
                                <div class="panel-footer text-right"> 
                                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Buscar registro', ['pago-banco/nuevopagoperario', 'id' => $model->id_pago_banco, 'tipo_proceso' => $model->id_tipo_nomina], ['class' => 'btn btn-success btn-sm']) ?>
                                    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar todo', ['pago-banco/eliminartododetalle', 'id' => $model->id_pago_banco, 'tipo_proceso' => $model->id_tipo_nomina], ['class' => 'btn btn-danger btn-sm']) ?>                    
                                </div> 
                            <?php }?>
                            <?php if($model->cerrar_proceso == 1){?>
                                <div class="panel-footer text-right" >            
                                        <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Exportar pagos', ['exportar_pago_banco', 'tipo_proceso' => $model->id_tipo_nomina, 'id' => $model->id_pago_banco], ['class' => 'btn btn-primary btn-sm'])?>              
                                </div>
                            <?php }?>
                        </div>
                    </div>   
                </div>
            </div>
            <!--INICIO EL OTRO TABS -->
        </div>
    </div>    
</div>    
   
