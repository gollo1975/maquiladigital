<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Producto */

$this->title = 'Documento soporte electronico';
$this->params['breadcrumbs'][] = ['label' => 'Documento electronico', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_nomina_electronica;
?>
<div class="programacion-nomina-view_detalle">

 <!--<h1><?= Html::encode($this->title) ?></h1>-->
                <div class="btn-group" role="group" aria-label="...">
                <?php if($token == 0){?>
                    <button type="button" class="btn btn-default btn-sm"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['vista_empleados','id_periodo' => $id_periodo, 'token' =>$token],['class' => 'btn btn-primary btn-xs']) ?></button>
                <?php }else{
                    if($token == 1){?>
                        <button type="button" class="btn btn-default btn-sm"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['listar_nomina_electronica'],['class' => 'btn btn-primary btn-xs']) ?></button>
                    <?php }else{ ?>
                        <button type="button" class="btn btn-default btn-sm"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['search_documentos_electronicos'],['class' => 'btn btn-primary btn-xs']) ?></button>
                    <?php }    
                }
                if($model->cune <> ''){?>
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-default btn-sm">  <?= Html::a('<span class="glyphicon glyphicon-print"></span>Visualizar PDF', ['imprimir_detalle_documento', 'id_nomina' => $id_nomina],['class' => 'btn btn-default btn-xs']) ?></button>
                    </div>
                <?php }?>    
            </div>
     
        <div class="panel panel-success">
            <div class="panel-heading">
                Detalle
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <tr style='font-size:85%;'>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'consecutivo') ?>:</th>
                        <td><?= Html::encode($model->consecutivo) ?> - <?= Html::encode($model->numero_nomina_electronica) ?></td>                                                            
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'documento_empleado') ?>:</th>
                        <td><?= Html::encode($model->documento_empleado) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nombre_completo') ?>:</th>
                        <td><?= Html::encode($model->nombre_completo) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Devengado') ?>:</th>
                        <td style="text-align: right"><?= Html::encode(''.number_format($model->total_devengado,2)) ?></td>
                    </tr>  
                    <tr style='font-size:85%;'>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Salario') ?>:</th>
                        <td style="text-align: right"><?= Html::encode(''.number_format($model->salario_contrato,0)) ?></td>  
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Banco') ?>:</th>
                        <td><?= Html::encode($model->nombre_banco) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Numero_cuenta') ?>:</th>
                        <td><?= Html::encode($model->numero_cuenta) ?></td>
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Deduccion') ?>:</th>
                        <td style="text-align: right"><?= Html::encode(''.number_format($model->total_deduccion,2)) ?></td>
                    </tr>  
                     <tr style='font-size:85%;'>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cune') ?>:</th>
                        <td colspan="5"><?= Html::encode($model->cune) ?></td>                                                            
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Neto_pagar') ?>:</th>
                        <td style="text-align: right"><?= Html::encode(''.number_format($model->total_pagar,2)) ?></td>
                    </tr>  
                     <tr style='font-size:85%;'>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nuevo_cune') ?>:</th>
                        <td colspan="5"><?= Html::encode($model->nuevo_cune) ?></td>                                                            
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_eliminacion') ?>:</th>
                        <td style="text-align: right"><?= Html::encode($model->fecha_hora_eliminacion) ?></td>
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
            <li role="presentation" class="active"><a href="#detalle_documento" aria-controls="detalle_documento" role="tab" data-toggle="tab">Detalle del documento <span class="badge"><?= count($detalle_documento)?></span></a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="detalle_documento">
                <div class="table-responsive">
                   <div class="panel panel-success ">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr style='font-size:85%;'>
                                    <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Concepto</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Agrupado</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Inicio</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Fin</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Total dias</th>                        
                                    <th scope="col" style='background-color:#B9D5CE;'>Porcentaje</th>    
                                    <th scope="col" style='background-color:#B9D5CE;'>Devengado</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Deduccion</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     foreach ($detalle_documento as $val): 
                                        if($val->devengado_deduccion == 1){?>
                                            <tr style='font-size:85%;'>
                                                <td><?= $val->codigo_salario ?></td>  
                                                <td><?= $val->descripcion ?></td>
                                                <td><?= $val->agrupado->concepto ?></td>
                                                <?php if($val->id_agrupado == 8 || $val->id_agrupado == 10 || $val->id_agrupado == 21){?>
                                                    <td><?= $val->inicio_licencia?></td>
                                                    <td><?= $val->final_licencia ?></td>
                                                <?php }elseif($val->id_agrupado == 20) {?>
                                                     <td><?= $val->fecha_inicio_vacaciones?></td>
                                                    <td><?= $val->fecha_final_vacaciones ?></td>
                                                <?php }else {?>    
                                                    <td><?= $val->inicio_incapacidad?></td>
                                                    <td><?= $val->final_incapacidad ?></td>
                                                <?php }
                                                if($val->id_agrupado == 11){ ?> 
                                                    <td style="text-align: right"><?= $val->dias_prima ?></td>
                                                <?php }else {?>    
                                                    <td style="text-align: right"><?= $val->total_dias ?></td>
                                                <?php }    
                                                 if($val->porcentaje == ''){?>
                                                    <td><?= $val->porcentaje ?></td>
                                                <?php }else{?>
                                                     <td style="text-align: right"><?= $val->porcentaje ?> %</td>
                                                <?php }?>    
                                                <td  style="text-align: right"><?= ''. number_format($val->devengado,2) ?></td>
                                                 <td style="text-align: right"></td>
                                            </tr>
                                        <?php }else{?>
                                            <tr style='font-size:85%;'>
                                                <td><?= $val->codigo_salario ?></td>  
                                                <td><?= $val->descripcion ?></td>
                                                <td><?= $val->agrupado->concepto ?></td>
                                                <td style="text-align: right"></td>
                                                <td style="text-align: right"></td>
                                                <td style="text-align: right"><?= $val->total_dias ?></td>
                                                <?php if($val->porcentaje == ''){?>
                                                    <td><?= $val->porcentaje ?></td>
                                                <?php }else{?>
                                                     <td style="text-align: right"><?= $val->porcentaje ?> %</td>
                                                <?php }?>    
                                                 <td style="text-align: right"></td>
                                                <td style="text-align: right"><?= ''. number_format($val->deduccion,2) ?></td>
                                            </tr>
                                        <?php }    
                                    endforeach; ?>
                                </tbody>
                            </table>
                       </div>    
                    </div>  
                </div>
            </div>
        </div>
   </div>
   <?php $form->end() ?>
</div>
