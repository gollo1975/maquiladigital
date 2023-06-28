<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproducciontipo */

$this->title = 'Detalle registro';
$this->params['breadcrumbs'][] = ['label' => 'Eficiencia diario', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_eficiencia;
?>
<div class="eficiencia-modulo-diario-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']);?>
        <?php if($model->proceso_cerrado == 0){?>
           <?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar proceso', ['cerrar_proceso_eficiencia', 'id' => $model->id_eficiencia],['class' => 'btn btn-info btn-xs',
                'data' => ['confirm' => 'Esta seguro de cerrar el proceso Nro  '. $model->id_eficiencia. ' del dia  '.$model->fecha_actual.'', 'method' => 'post']]);
        }?>          
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle del registro
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?></th>
                    <td><?= Html::encode($model->id_eficiencia) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Planta') ?></th>
                    <td><?= Html::encode($model->planta->nombre_planta) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_actual') ?></th>
                    <td><?= Html::encode($model->fecha_actual) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Total_eficiencia') ?></th>
                    <td style="text-align: right; color: blue;"><b><?= Html::encode(''.number_format($model->total_eficiencia_planta,0)) ?>%</b></td>
                </tr>
                  <tr style="font-size: 85%;">
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario_creador') ?>:</th>
                    <td><?= Html::encode($model->usuario_creador) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario_editor') ?>:</th>
                     <td><?= Html::encode($model->usuario_editor) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_proceso') ?></th>
                    <td><?= Html::encode($model->fecha_proceso) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cerrado') ?></th>
                      <td><?= Html::encode($model->procesoCerrado) ?></td>
                </tr>       
            </table>
        </div>
    </div>
     <!-- INICIO DEL TABAS*-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <?php
             $List = count($modulos);
             $Detalle = count($EntradaDia);
             ?>
            <li role="presentation" class="active"><a href="#modulos" aria-controls="modulos" role="tab" data-toggle="tab">Modulos <span class="badge"><?= $List ?></span></a></li>
            <li role="presentation" ><a href="#detalle_entrada" aria-controls="detalle_entrada" role="tab" data-toggle="tab">Detalle <span class="badge"><?= $Detalle ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="modulos">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>No balanceo</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Modulo</th>   
                                        <th scope="col" style='background-color:#B9D5CE;'>T. Balanceo</th>   
                                        <th scope="col" style='background-color:#B9D5CE;'>Op Interna</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha actual</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Hora inicio</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Eficiencia diaria</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>U. Confección</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Proceso</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($modulos as $modulo):?>
                                        <tr style='font-size:95%;'>
                                            <td><?= $modulo->id_carga ?></td>
                                            <td><?= $modulo->id_balanceo ?></td>
                                            <td><?= $modulo->balanceo->modulo ?></td>
                                            <td><?= $modulo->balanceo->tiempo_balanceo ?></td>
                                            <td><?= $modulo->idordenproduccion ?></td>
                                            <td><?= $modulo->fecha_carga ?></td>
                                            <td><?= $modulo->hora_inicio_modulo ?></td>
                                            <td style="text-align: right;color: blue;"><b><?=''.number_format($modulo->total_eficiencia_diario, 0) ?>%</b></td>
                                            <td style="text-align: right"><?=''.number_format($modulo->total_unidades, 0) ?></td>
                                            <td><?= $modulo->balanceo->procesoconfeccion->descripcion_proceso ?></td>
                                            <td><?= $modulo->usuario ?></td>
                                            <?php if($model->proceso_cerrado == 0){?>
                                               
                                                <td style="width: 25px; height: 25px;">
                                                      <!-- Inicio Nuevo Detalle proceso -->
                                                        <?= Html::a('<span class="glyphicon glyphicon-list"></span> ',
                                                            ['/eficiencia-modulo-diario/eficiencia_modulo_diario', 'id' => $modulo->id_eficiencia, 'orden_produccion' => $modulo->idordenproduccion, 'id_balanceo' => $modulo->id_balanceo, 'id_carga' => $modulo->id_carga],
                                                            [
                                                                'title' => 'Subir unidades por hora',
                                                                'data-toggle'=>'modal',
                                                                'data-target'=>'#modalunidadesporhora'.$modulo->id_balanceo,
                                                            ])    
                                                       ?>
                                                    <div class="modal remote fade" id="modalunidadesporhora<?= $modulo->id_balanceo ?>">
                                                        <div class="modal-dialog modal-lg" style ="width: 1050px;">
                                                            <div class="modal-content"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!<!-- proceso que modfica la hora -->
                                                <td style="width: 25px; height: 25px;">
                                                      <!-- Inicio Nuevo Detalle proceso -->
                                                        <?= Html::a('<span class="glyphicon glyphicon-time"></span> ',
                                                            ['/eficiencia-modulo-diario/modificarhorainicio', 'id' => $modulo->id_eficiencia, 'id_planta' => $model->id_planta,  'id_detalle' => $modulo->id_carga],
                                                            [
                                                                'title' => 'Modificar horario de inicio',
                                                                'data-toggle'=>'modal',
                                                                'data-target'=>'#modalmodificarhorainicio'.$modulo->id_carga,
                                                            ])    
                                                       ?>
                                                    <div class="modal remote fade" id="modalmodificarhorainicio<?= $modulo->id_carga ?>">
                                                        <div class="modal-dialog modal-lg" style ="width: 500px;">
                                                            <div class="modal-content"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width: 25px; height: 25px;">
                                                 <?=
                                                    Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['eliminar', 'id_carga' => $modulo->id_carga, 'id' => $model->id_eficiencia, 'id_planta' => $model->id_planta], [
                                                        'class' => '',
                                                        'data' => [
                                                            'confirm' => 'Esta seguro de eliminar el registro?',
                                                            'method' => 'post',
                                                        ],
                                                    ])
                                                    ?>
                                                </td>  
                                             <?php }else{?>
                                                <td style="width: 25px; height: 25px;">
                                                <td style="width: 25px; height: 25px;">
                                                <td style="width: 25px; height: 25px;">
                                             <?php }?>   
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                            <?php if($model->proceso_cerrado == 0){?>
                                <div class="panel-footer text-right"> 
                                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Cargar modulos', ['eficiencia-modulo-diario/listar_modulos', 'id' => $model->id_eficiencia, 'id_planta' => $model->id_planta], ['class' => 'btn btn-success btn-sm']) ?>
                                </div> 
                            <?php }else{?>
                                <div class="panel-footer text-right" >            
                                        <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Exportar modulos', ['exportar_modulos', 'id' => $model->id_eficiencia], ['class' => 'btn btn-primary btn-sm'])?>              
                                </div>
                            <?php }?>
                        </div>
                    </div>   
                </div>
            </div>
            <!--INICIO EL OTRO TABS -->
             <div role="tabpanel" class="tab-pane" id="detalle_entrada">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>No balanceo</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>No Carga</th>   
                                        <th scope="col" style='background-color:#B9D5CE;'>Op Interna</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha actual</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Hora inicio</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Hora corte</th> 
                                         <th scope="col" style='background-color:#B9D5CE;'>% Eficiencia</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Confección</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Reales</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>No Operarios</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($EntradaDia as $entrada):?>
                                        <tr style='font-size:95%;'>
                                            <td><?= $entrada->id_entrada ?></td>
                                            <td><?= $entrada->id_balanceo ?></td>
                                            <td><?= $entrada->id_carga?></td>
                                            <td><?= $entrada->idordenproduccion ?></td>
                                            <td><?= $entrada->fecha_dia_confeccion ?></td>
                                            <td><?= $entrada->hora_inicio_dia ?></td>
                                            <td><?= $entrada->hora_corte ?></td>
                                            <td style="text-align: right; color: blue;"><b><?= ''.number_format($entrada->porcentaje_hora_corte, 0) ?>%</b></td>
                                            <td style="text-align: right"><?= ''.number_format($entrada->unidades_confeccionadas, 0) ?></td>
                                            <td style="text-align: right"><?= ''.number_format($entrada->real_confeccion, 0) ?></td>
                                            <td style="text-align: right"><?= $entrada->numero_operarios ?></td>
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                                <div class="panel-footer text-right" >            
                                        <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Exportar entrada', ['exportar_entradas', 'id' => $model->id_eficiencia], ['class' => 'btn btn-primary btn-sm'])?>              
                                </div>
                        </div>
                    </div>   
                </div>
            </div>
            <!-- INICIO DE TABAS-->
        </div>
    </div>    

</div>
<script type="text/javascript">
    function esInteger(e) {
        var charCode
        charCode = e.keyCode
        status = charCode
        if (charCode != 46 && charCode > 31 
 
      && (charCode < 48 || charCode > 57)) {
            return false
        }
        return true
    }
</script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>
