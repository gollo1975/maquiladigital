<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\Session;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $model app\models\Producto */
$this->title = 'Detalle crédito';
$this->params['breadcrumbs'][] = ['label' => 'Detalle credito', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_credito;
?>
<div class="credito-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?php if($model->saldo_credito <= 0){?>
        <p>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
        </p>
    <?php }else{ ?>    
          <p>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_credito], ['class' => 'btn btn-success btn-sm']) ?>
        </p>
    <?php }?>    
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle crédito
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style= 'font-size:85%;'>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_credito') ?></th>
                    <td><?= Html::encode($model->id_credito) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_credito') ?></th>
                    <td><?= Html::encode($model->codigoCredito->nombre_credito) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_tipo_pago') ?></th>
                    <td><?= Html::encode($model->tipoPago->descripcion) ?></td>
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'vlr_credito') ?></th>
                    <td><?= Html::encode('$'. number_format($model->vlr_credito,0)) ?></td>  
                </tr>   
                 <tr style= 'font-size:85%;'>
                    
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'saldo_credito') ?></th>
                     <td><?= Html::encode('$'.number_format($model->saldo_credito,0)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'vlr_cuota') ?></th>
                    <td><?= Html::encode('$'.number_format($model->vlr_cuota,0)) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_cuotas') ?></th>
                     <td><?= Html::encode($model->numero_cuotas) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_cuota_actual') ?></th>
                     <td><?= Html::encode($model->numero_cuota_actual) ?></td>
                 </tr>
                 <tr style= 'font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'validar_cuota')?>:</th>
                    <td><?= Html::encode($model->validarcuota) ?></td> 
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_empleado') ?></th>
                    <td><?= Html::encode($model->empleado->identificacion),'-',($model->empleado->nombrecorto) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_inicio') ?></th>
                     <td><?= Html::encode($model->fecha_inicio) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_creacion') ?></th>
                     <td><?= Html::encode($model->fecha_creacion) ?></td>
                </tr>    
                <tr style= 'font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'seguro') ?></th>
                     <td><?= Html::encode($model->seguro) ?></td> 
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_libranza') ?></th>
                     <td><?= Html::encode($model->numero_libranza) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'usuariosistema') ?></th>
                     <td><?= Html::encode($model->usuariosistema) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'estado_periodo') ?>:</th>
                     <td><?= Html::encode($model->estadoperiodo) ?></td>
                     
                </tr>
                
                 <tr style= 'font-size:85%;'>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'estado_credito')?>:</th>
                     <td><?= Html::encode($model->estadocredito) ?></td>
                       <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'aplicar_prima')?>:</th>
                     <td><?= Html::encode($model->aplicarprima) ?></td>
                       <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'vlr_aplicar')?></th>
                       <td colspan="4"><?= Html::encode('$'.number_format($model->vlr_aplicar,0)) ?></td>
                </tr>      
                <tr style= 'font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?></th>
                    <td colspan="8"><?= Html::encode($model->observacion) ?></td>
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
    ]); ?>
     <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#listadoabonos" aria-controls="listadoabonos" role="tab" data-toggle="tab">Listado de abonos <span class="badge"><?= count($abonos) ?></span></a></li>
            <li role="presentation"><a href="#refinanciacion" aria-controls="refinanciacion" role="tab" data-toggle="tab">Refinanciacion<span class="badge"><?= count($refinanciacion) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="listadoabonos">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead >
                                    <tr>
                                        <td scope="col" align="center" style='background-color:#B9D5CE;'><b>Id_Abono</b></td>                        
                                        <td scope="col" align="center" style='background-color:#B9D5CE;'><b>Forma pago</b></td>                        
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'>Valor abono</th>                        
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'>Saldo</th>       
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'>Cuota pendiente</th>                        
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'>Fecha proceso</th>                        
                                        <td scope="col" align="center" style='background-color:#B9D5CE;'><b>Usuario</b></td> 
                                        <td scope="col" align="center" style='background-color:#B9D5CE;'><b>Observación</b></td> 

                                    </tr>
                                </thead>
                                <tbody>
                                     <?php foreach ($abonos as $val): ?>
                                        <tr style= 'font-size:85%;'>
                                            <td><?= $val->id_abono ?></td>
                                            <td><?= $val->tipoPago->descripcion ?></td>
                                            <td><?= '$'.number_format($val->vlr_abono,0) ?></td>
                                            <td><?= '$'.number_format($val->saldo,0) ?></td>
                                            <td><?= $val->cuota_pendiente ?></td>
                                            <td><?= $val->fecha_proceso ?></td>
                                            <td><?= $val->usuariosistema ?></td>
                                              <td><?= $val->observacion ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>  
                            </table>
                        </div>            
                        <div class="panel-footer text-right">  
                            <?php 
                            if($model->saldo_credito > 0){?>

                                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nuevo abono', ['credito/nuevoabono', 'id_credito' => $model->id_credito], ['class' => 'btn btn-info btn-sm']) ?>                    
                            <?php }?>
                        </div>   
                    </div>            
                </div>
            </div>  
            <!--TERMINA TABS-->
            <div role="tabpanel" class="tab-pane" id="refinanciacion">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thea>
                                    <tr>
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'>Nuevo valor</th>                        
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'>Total cuotas</th>                        
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'>Numero cuota inicial</th>       
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'>Nuevo valor cuota</th>                        
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'>Fecha proceso</th>                        
                                        <td scope="col" align="center" style='background-color:#B9D5CE;'><b>Usuario</b></td> 
                                        <td scope="col" align="center" style='background-color:#B9D5CE;'><b>Observación</b></td> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($refinanciacion as $val): ?>
                                        <tr style= 'font-size:85%;'>
                                            <td style="text-align: right"><?= ''.number_format($val->adicionar_valor,0) ?></td>
                                            <td style="text-align: right"><?= '$'.number_format($val->numero_cuotas,0) ?></td>
                                            <td style="text-align: right"><?= ''.number_format($val->numero_cuota_actual,0) ?></td>
                                           <td style="text-align: right"><?= '$'.number_format($val->valor_cuota,0) ?></td>
                                            <td><?= $val->fecha_registro ?></td>
                                            <td><?= $val->user_name ?></td>
                                              <td><?= $val->nota ?></td>
                                        </tr>
                                     <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>    
                        <div class="panel-footer text-right">  
                            <?php 
                            if($model->saldo_credito > 0){?>
                                <?= Html::a('<span class="glyphicon glyphicon-list"></span> Refinaciar', ['credito/refinanciar_credito', 'id_credito' => $model->id_credito], ['class' => 'btn btn-primary btn-sm']) ?>                    
                            <?php }?>
                        </div>   
                    </div>            
                </div>            
            </div>
            <!-- TERMINA TABAS-->
        </div>   
    </div>    
    <?php ActiveForm::end(); ?>
</div>

