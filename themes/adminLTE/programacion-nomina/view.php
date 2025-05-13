<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Producto */

$this->title = 'Programación de Nómina';
$this->params['breadcrumbs'][] = ['label' => 'Programación Nómina', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_periodo_pago_nomina;
$view = 'programacion nomina';
?>
<div class="programacion-nomina-view">

 <!--<h1><?= Html::encode($this->title) ?></h1>-->
       <?php
       if($model->estado_periodo == 0){?>
            <div class="btn-group" role="group" aria-label="...">
                <button type="button" class="btn btn-default btn-sm"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'],['class' => 'btn btn-primary btn-xs']) ?></button>
                <button type="button" class="btn btn-default btn-sm"><?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['editar', 'id' => $model->id_periodo_pago_nomina], ['class' => 'btn btn-success btn-xs']) ?></button>
                <button type="button" class="btn btn-default btn-sm"><?= Html::a('<span class="glyphicon glyphicon-export"></span> Cargar Empleados', ['cargar', 'id' => $model->id_periodo_pago_nomina, 'tipo_nomina' => $model->id_tipo_nomina , 'id_grupo_pago' =>$model->id_grupo_pago, 'fecha_desde' => $model->fecha_desde, 'fecha_hasta' =>$model->fecha_hasta], ['class' => 'btn btn-default btn-xs']) ?></button>
                <button type="button" class="btn btn-default btn-sm"><?= Html::a('<span class="glyphicon glyphicon-plus"></span> Agregar Empleado', ['agregarempleado', 'id' => $model->id_periodo_pago_nomina, 'tipo_nomina' => $model->id_tipo_nomina , 'id_grupo_pago' =>$model->id_grupo_pago, 'fecha_desde' => $model->fecha_desde, 'fecha_hasta' =>$model->fecha_hasta], ['class' => 'btn btn-green btn-xs']) ?></button>

                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-info btn-lg dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Novedades
                      <span class="caret"></span>
                    </button>

                      <ul class="dropdown-menu">
                       <?php
                       $count = count($detalles);
                       if ($count == 0){

                           ?> <li><?= Html::a('<span class="glyphicon glyphicon-time "></span>Tiempo extra', ['novedadeserror', 'id' => $model->id_periodo_pago_nomina, 'id_grupo_pago' =>$model->id_grupo_pago, 'fecha_desde' => $model->fecha_desde, 'fecha_hasta' =>$model->fecha_hasta]) ?></li> <?php
                       }else{
                          if($model->id_tipo_nomina == 3 || $model->id_tipo_nomina == 2){?>
                           <li><?= Html::a('<span class="glyphicon glyphicon-time "></span>Tiempo extra', ['novedadeserror', 'id' => $model->id_periodo_pago_nomina, 'id_grupo_pago' =>$model->id_grupo_pago, 'fecha_desde' => $model->fecha_desde, 'fecha_hasta' =>$model->fecha_hasta]) ?></li>
                          <?php }else{?>
                            <li><?= Html::a('<span class="glyphicon glyphicon-time"></span>Tiempo extra', ['/novedad-tiempo-extra/novedades', 'id' => $model->id_periodo_pago_nomina], ['target' => '_blank']) ?></li>
                          <?php }
                       }
                       ?>
                      </ul>
                </div>
            </div>
       <?php }else{?>
            <div class="btn-group" role="group" aria-label="...">
                <button type="button" class="btn btn-default btn-sm"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'],['class' => 'btn btn-primary btn-xs']) ?></button>
            </div>
       <?php }?>    
        <div class="panel panel-success">
            <div class="panel-heading">
                Detalle
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <tr style='font-size:85%;'>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_periodo_pago_nomina') ?>:</th>
                        <td><?= Html::encode($model->id_periodo_pago_nomina) ?></td>                                                            
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_tipo_nomina') ?>:</th>
                        <td><?= Html::encode($model->tipoNomina->tipo_pago) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_grupo_pago') ?>:</th>
                        <td><?= Html::encode($model->grupoPago->grupo_pago) ?></td>
                    </tr>               
                    <tr style='font-size:85%;'>                                        
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_periodo_pago') ?>:</th>
                        <td><?= Html::encode($model->periodoPago->nombre_periodo) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_desde') ?>:</th>
                        <td><?= Html::encode($model->fecha_desde) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_hasta') ?>:</th>
                        <td><?= Html::encode($model->fecha_hasta) ?></td>
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
   <?php $con = count($detalles);
   $estado_generado = 0; $estado_liquidado = 0; $estado_cerrado = 0; $cont = 0;
   if($con > 0){
        foreach ($detalles as $dato):
             if($dato->estado_generado == 0){
                 $estado_generado = 1;
                 $cont = 1;
             }else{
                if($dato->estado_generado == 1 && $dato->estado_liquidado == 0){ 
                    $estado_generado = 2;
                    $estado_liquidado = 1;
                }else{
                     if($dato->estado_liquidado == 1 && $dato->estado_cerrado == 0){
                        $estado_cerrado = 1;
                        $estado_liquidado = 2;
                     }else{
                            $estado_cerrado = 2;
                     }
                }
            }
        endforeach;  
   }    
    if($estado_generado == 1){?>                     
        <div class="panel-footer text-center"> 
            <?= Html::a('<span class="glyphicon glyphicon-file"></span> Procesar_información', ['procesarregistros', 'id' => $model->id_periodo_pago_nomina, 'tipo_nomina' =>$model->id_tipo_nomina, 'id_grupo_pago' =>$model->id_grupo_pago, 'fecha_desde' => $model->fecha_desde, 'fecha_hasta' =>$model->fecha_hasta], ['class' => 'btn btn-success btn-sm']) ?>
        </div>     
    <?php }   
     if($estado_generado == 2 && $estado_liquidado == 1){?>                     
        <div class="panel-footer text-center"> 
            <?= Html::a('<span class="glyphicon glyphicon-triangle-left"></span> Deshacer_proceso', ['deshacer', 'id' => $model->id_periodo_pago_nomina, 'id_grupo_pago' =>$model->id_grupo_pago, 'fecha_desde' => $model->fecha_desde, 'fecha_hasta' =>$model->fecha_hasta], ['class' => 'btn btn-primary btn-sm']) ?>    
            <?= Html::a('<span class="glyphicon glyphicon-level-up"></span> Validar_registros', ['validarregistros', 'id' => $model->id_periodo_pago_nomina, 'tipo_nomina' =>$model->id_tipo_nomina, 'id_grupo_pago' =>$model->id_grupo_pago, 'fecha_desde' => $model->fecha_desde, 'fecha_hasta' =>$model->fecha_hasta],['class' => 'btn btn-warning btn-sm',
                      'data' => ['confirm' => 'Este proceso valida todos los registros de las '. $model->tipoNomina->tipo_pago. ' y actualiza licencias e incapacidades. Esta seguro de validar los registros?', 'method' => 'post']]) ?>
        </div>     
    <?php }
    if($estado_cerrado == 1 && $estado_liquidado == 2){?>                     
        <div class="panel-footer text-center"> 
            <div>
                <?= Html::a('<span class="glyphicon glyphicon-open-file"></span> Aplicar_pagos', ['aplicarpagos', 'id' => $model->id_periodo_pago_nomina, 'tipo_nomina' =>$model->id_tipo_nomina,'id_grupo_pago' =>$model->id_grupo_pago, 'fecha_desde' => $model->fecha_desde, 'fecha_hasta' =>$model->fecha_hasta],['class' => 'btn btn-info btn-lg',
                      'data' => ['confirm' => 'Esta función cierra todos los procesos del pago de las ' . $model->tipoNomina->tipo_pago. '. Esta seguro de ejecutar este proceso?', 'method' => 'post']]) ?>
            </div>

        </div>     
    <?php }
     if($estado_cerrado == 2){?>
        <div class="panel-footer text-right">
            <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle"
                                data-toggle="dropdown">
                            <span class="glyphicon glyphicon-export btn-small"></span> Exportar
                        </button>
                        <ul class="dropdown-menu" role="menu">
                          <li><?= Html::a('<span class="glyphicon glyphicon-check"></span> Excel', ['excelpago', 'id' => $model->id_periodo_pago_nomina]) ?></li>
                          <li><?= Html::a('<span class="glyphicon glyphicon-check"></span> Excel detalle', ['exceldetallepago', 'id' => $model->id_periodo_pago_nomina]) ?></li>
                        </ul>
            </div>
        </div> 
    <?php }?>  
  <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <?php
            $conIncapacidad = count($incapacidad);
            $conLicencia = count($licencia);
            $conTiempo_extra = count($novedad_tiempo);
            $conCredito = count($credito_empleado);
            $conInteres = count($intereses);
            if($conInteres == 0){?>
                <li role="presentation" class="active"><a href="#empleado" aria-controls="empleado" role="tab" data-toggle="tab">Empleados <span class="badge"><?= $con ?></span></a></li>
                <li role="presentation"><a href="#incapacidad" aria-controls="incapacidad" role="tab" data-toggle="tab">Incapacidades <span class="badge"><?= $conIncapacidad ?></span></a></li>
                <li role="presentation"><a href="#licencia" aria-controls="licencia" role="tab" data-toggle="tab">Licencias <span class="badge"><?= $conLicencia ?></span></a></li>
                <li role="presentation"><a href="#novedades" aria-controls="novedades" role="tab" data-toggle="tab">Novedades <span class="badge"><?= $conTiempo_extra ?></span></a></li>
                <li role="presentation"><a href="#credito" aria-controls="credito" role="tab" data-toggle="tab">Créditos <span class="badge"><?= $conCredito ?></span></a></li>
            <?php }else{?>
                <li role="presentation" class="active"><a href="#empleado" aria-controls="empleado" role="tab" data-toggle="tab">Empleados <span class="badge"><?= $con ?></span></a></li>
                <li role="presentation"><a href="#incapacidad" aria-controls="incapacidad" role="tab" data-toggle="tab">Incapacidades <span class="badge"><?= $conIncapacidad ?></span></a></li>
                <li role="presentation"><a href="#licencia" aria-controls="licencia" role="tab" data-toggle="tab">Licencias <span class="badge"><?= $conLicencia ?></span></a></li>
                <li role="presentation"><a href="#novedades" aria-controls="novedades" role="tab" data-toggle="tab">Novedades <span class="badge"><?= $conTiempo_extra ?></span></a></li>
                <li role="presentation"><a href="#credito" aria-controls="credito" role="tab" data-toggle="tab">Créditos <span class="badge"><?= $conCredito ?></span></a></li>
                <li role="presentation"><a href="#intereses" aria-controls="intereses" role="tab" data-toggle="tab">Intereses <span class="badge"><?= $conInteres ?></span></a></li>
                
            <?php }?>    
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="empleado">
                <div class="table-responsive">
                   <div class="panel panel-success ">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr style='font-size:85%;'>
                                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Nro_pago</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>                        
                                    <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>    
                                    <th scope="col" style='background-color:#B9D5CE;'>Inicio Contrato</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Fecha final</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Contrato</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Salario</th>    
                                    <th scope="col"style='background-color:#B9D5CE;'>Devengado</th>                        
                                    <th scope="col"style='background-color:#B9D5CE;'>Deducción</th>   
                                    <th scope="col" style='background-color:#B9D5CE;'>D.pagos</th>   
                                    <th scope="col" style='background-color:#B9D5CE;'>H.pagos</th>
                                    <th scope="col" style='background-color:#B9D5CE;'><span title="Tiempo servicio">Ts</span></th>
                                    <th scope="col" style='background-color:#B9D5CE;'></th>
                                    <th scope="col" style='background-color:#B9D5CE;'></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                      $c =''; $m = ''; $s = '';
                                      $c = 'C'; $m = 'M'; $s = 'S';
                                     foreach ($detalles as $val): 
                                        $contrato = \app\models\Contrato::find()->where(['=','id_contrato', $val->id_contrato])->one();
                                        ?>
                                    <tr style='font-size:85%;'>
                                        <?php
                                          if($val->fecha_inicio_vacacion <> ''){?>
                                             <td style="background-color:#DCFFFC;"><?= $val->id_programacion ?></td>  
                                             <td style="background-color:#DCFFFC;"><?= $val->nro_pago ?></td>
                                            <td style="background-color:#DCFFFC;"><?= $val->cedula_empleado ?></td>
                                            <td style="background-color:#DCFFFC;"><?= $val->empleado->nombrecorto ?></td>
                                            <td style="background-color:#DCFFFC;"><?= $val->fecha_inicio_contrato ?></td>
                                            <td style="background-color:#DCFFFC;"><?= $val->fecha_final_contrato ?></td>
                                             <td style="background-color:#DCFFFC;"><?= $val->id_contrato ?></td>
                                            <td style="background-color:#DCFFFC;"><?= '$'.number_format($val->salario_contrato,0) ?></td>
                                            <td style="background-color:#DCFFFC;"><?= '$'.number_format($val->total_devengado,0) ?></td>
                                            <td style="background-color:#DCFFFC;"><?= '$'.number_format($val->total_deduccion,0) ?></td>
                                            <td style="background-color:#DCFFFC;"><?= $val->dia_real_pagado ?></td>
                                            <td style="background-color:#DCFFFC;"><?= $val->horas_pago ?></td>
                                          <?php }else{?>    
                                            <td ><?= $val->id_programacion ?></td>  
                                             <td><?= $val->nro_pago ?></td>
                                            <td><?= $val->cedula_empleado ?></td>
                                            <td><?= $val->empleado->nombrecorto ?></td>
                                            <td><?= $val->fecha_inicio_contrato ?></td>
                                            <td><?= $val->fecha_final_contrato ?></td>
                                             <td><?= $val->id_contrato ?></td>
                                            <td><?= '$'.number_format($val->salario_contrato,0) ?></td>
                                            <td><?= '$'.number_format($val->total_devengado,0) ?></td>
                                            <td><?= '$'.number_format($val->total_deduccion,0) ?></td>
                                            <td><?= $val->dia_real_pagado ?></td>
                                            <td><?= $val->horas_pago ?></td>
                                          <?php }?>  
                                        <td>
                                            <?php if($contrato->id_tiempo == 1){
                                              echo $c;
                                            }else{
                                                if($contrato->id_tiempo == 2){
                                                   echo $m;
                                                }else{
                                                    echo $s;
                                                }
                                            }?></td>
                                        
                                            <?php
                                            if($val->estado_generado == 1 and $val->estado_cerrado == 0){?>
                                                <td style="width: 0.5%; height: 0.5%; ">  
                                                    <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span>',            
                                                    ['programacion-nomina/vernomina','id_programacion'=>$val->id_programacion, 'id_empleado' => $val->id_empleado, 'id_grupo_pago' => $val->id_grupo_pago, 'id_periodo_pago_nomina' => $val->id_periodo_pago_nomina],
                                                        [
                                                            'title' => 'Comprobante de pago',
                                                            'data-toggle'=>'modal',
                                                            'data-target'=>'#modalvernomina'.$val->id_programacion,
                                                            'class' => ''
                                                        ]
                                                    );
                                                    ?>
                                                     <div class="modal remote fade" id="modalvernomina<?= $val->id_programacion ?>">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content"></div>
                                                        </div>
                                                     </div>
                                               </td>
                                                <td style="width: 25px;">				
                                                    <a href="<?= Url::toRoute(["programacion-nomina/editarcolillapagonomina", 'id' => $model->id_periodo_pago_nomina,"id_programacion" => $val->id_programacion, 'id_grupo_pago' => $val->id_grupo_pago, 'id' => $val->id_periodo_pago_nomina, 'fecha_desde' => $val->fecha_desde, 'fecha_hasta' => $val->fecha_hasta]) ?>" ><span class="glyphicon glyphicon-book"></span></a>
                                                </td>
                                            <?php }else{?>
                                                   <td style="width: 0.5%; height: 0.5%; ">  
                                                        <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span>',            
                                                        ['programacion-nomina/vernomina','id_programacion'=>$val->id_programacion, 'id_empleado' => $val->id_empleado, 'id_grupo_pago' => $val->id_grupo_pago, 'id_periodo_pago_nomina' => $val->id_periodo_pago_nomina],
                                                            [
                                                                'title' => 'Comprobante de pago',
                                                                'data-toggle'=>'modal',
                                                                'data-target'=>'#modalvernomina'.$val->id_programacion,
                                                                'class' => ''
                                                            ]
                                                        );
                                                        ?>
                                                         <div class="modal remote fade" id="modalvernomina<?= $val->id_programacion ?>">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content"></div>
                                                            </div>
                                                         </div>
                                                    </td>
                                                    <td></td>    
                                            <?php }?>         
                                            
                                            <?php //este condicion permite saber si es sabatino
                                            if($contrato->id_tiempo == 3 && $val->estado_liquidado == 1 && $val->estado_cerrado == 0){?> 
                                                <td style="width: 0.5%; height: 0.5%; ">
                                                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>',            
                                                    ['/programacion-nomina/editarcolillapagosabatino','id_programacion' => $val->id_programacion, 'id_grupo_pago' => $val->id_grupo_pago, 'id' => $val->id_periodo_pago_nomina, 'fecha_desde' => $val->fecha_desde, 'fecha_hasta' => $val->fecha_hasta],
                                                        [
                                                            'title' => 'Modificar colilla de pago',
                                                            'data-toggle'=>'modal',
                                                            'data-target'=>'#modaleditarcolillapagosabatino'.$val->id_programacion,
                                                            'class' => ''
                                                        ]
                                                    );
                                                    ?>
                                                    <div class="modal remote fade" id="modaleditarcolillapagosabatino<?= $val->id_programacion ?>">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content"></div>
                                                        </div>
                                                   </div>
                                                </td>     
                                            <?php }?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                       </div>    
                    </div>  
                </div>
                <?php
                if($model->estado_periodo == 0){?>
                    <div class="panel-footer text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle"
                                    data-toggle="dropdown">
                                <span class="glyphicon glyphicon-export"></span> Exportar
                            </button>
                            <ul class="dropdown-menu" role="menu">
                              <li><?= Html::a('<span class="glyphicon glyphicon-check"></span> Excel', ['excelpago', 'id' => $model->id_periodo_pago_nomina]) ?></li>
                              <li><?= Html::a('<span class="glyphicon glyphicon-check"></span> Excel detalle', ['exceldetallepago', 'id' => $model->id_periodo_pago_nomina]) ?></li>
                            </ul>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-danger btn-xs dropdown-toggle"
                                    data-toggle="dropdown">
                                <span class="glyphicon glyphicon-trash"></span> Eliminar
                            </button>
                            <ul class="dropdown-menu" role="menu">
                              <li><?= Html::a('<span class="glyphicon glyphicon-user"></span> Empleado', ['programacion-nomina/eliminar_empleado', 'id' => $model->id_periodo_pago_nomina, 'id_grupo_pago' => $model->id_grupo_pago, 'fecha_desde' => $model->fecha_desde, 'fecha_hasta' => $model->fecha_hasta]) ?></li>
                              <li><?= Html::a('<span class="glyphicon glyphicon-home"></span> Detalle', ['programacion-nomina/eliminar_todo', 'id' => $model->id_periodo_pago_nomina, 'id_grupo_pago' => $model->id_grupo_pago, 'fecha_desde' => $model->fecha_desde, 'fecha_hasta' => $model->fecha_hasta]) ?></li>
                            </ul>
                        </div>
                    </div> 
               <?php } ?>
            </div>  
          
            <div role="tabpanel" class="tab-pane" id="incapacidad">
                <div class="table-responsive">
                   <div class="panel panel-success ">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr style='font-size:85%;'>
                                     <th scope="col" style='background-color:#B9D5CE;'>Id</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Nro incapacidad</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Tipo incapacidad</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>                        
                                     <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>    
                                    <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>  
                                     <th scope="col" style='background-color:#B9D5CE;'>Fecha final</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Salario</th>    
                                    <th scope="col" style='background-color:#B9D5CE;'>Dias</th>                        
                                    <th scope="col" style='background-color:#B9D5CE;'>Nro contrato</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($incapacidad as $val): ?>
                                    <tr style='font-size:85%;'>
                                          <td><?= $val->id_incapacidad ?></td>
                                        <td><?= $val->numero_incapacidad ?></td>  
                                        <td><?= $val->codigoIncapacidad->nombre ?></td>   
                                        <td><?= $val->identificacion ?></td>   
                                        <td><?= $val->empleado->nombrecorto ?></td>   
                                        <td><?= $val->fecha_inicio ?></td>   
                                        <td><?= $val->fecha_final ?></td>   
                                        <td><?= '$'.number_format($val->salario,0) ?></td>
                                        <td><?= $val->dias_incapacidad ?></td>   
                                         <td><?= $val->id_contrato ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                       </div>    
                    </div>  
                </div>
            </div>
          <div role="tabpanel" class="tab-pane" id="licencia">
              <div class="table-responsive">
                   <div class="panel panel-success ">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr style='font-size:85%;'>
                                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Tipo licencia</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>                        
                                     <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>    
                                    <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>  
                                     <th scope="col" style='background-color:#B9D5CE;'>Fecha final</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Salario</th>    
                                    <th scope="col" style='background-color:#B9D5CE;'>Dias</th>                        
                                    <th scope="col" style='background-color:#B9D5CE;'>Nro Contrato</th>                        
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($licencia as $val): ?>
                                    <tr style='font-size:85%;'>
                                        <td><?= $val->id_licencia_pk ?></td>  
                                        <td><?= $val->codigoLicencia->concepto ?></td>   
                                        <td><?= $val->identificacion ?></td>   
                                        <td><?= $val->empleado->nombrecorto ?></td>   
                                        <td><?= $val->fecha_desde ?></td>   
                                        <td><?= $val->fecha_hasta ?></td>   
                                        <td><?= '$'.number_format($val->salario,0) ?></td>
                                        <td><?= $val->dias_licencia ?></td>   
                                        <td><?= $val->id_contrato ?></td>   
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                       </div>    
                    </div>  
                </div>
          </div>
            <div role="tabpanel" class="tab-pane" id="novedades">
                <div class="table-responsive">
                   <div class="panel panel-success ">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr style='font-size:85%;'>
                                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>                        
                                     <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>    
                                    <th scope="col" style='background-color:#B9D5CE;'>Código</th>  
                                     <th scope="col" style='background-color:#B9D5CE;'>Concepto salario</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Nro_Horas</th>    
                                    <th scope="col" style='background-color:#B9D5CE;'>Vlr_Hora</th>                        
                                    <th scope="col" style='background-color:#B9D5CE;'>Total_Novedad</th>                        
                                    <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th> 
                                     <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($novedad_tiempo as $val): ?>
                                    <tr style='font-size:85%;'>
                                        <td><?= $val->id_novedad ?></td>  
                                        <td><?= $val->empleado->identificacion ?></td>   
                                        <td><?= $val->empleado->nombrecorto ?></td>
                                        <td><?= $val->codigo_salario ?></td>   
                                        <td><?= $val->concepto ?></td>   
                                        <td><?= $val->nro_horas ?></td>   
                                        <td><?= $val->vlr_hora ?></td>   
                                        <td><?= '$'.number_format($val->total_novedad,0) ?></td>
                                        <td><?= $val->fecha_creacion ?></td> 
                                        <td><?= $val->usuariosistema ?></td>   
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                       </div>    
                    </div>  
                </div>
            </div>
            
            <div role="tabpanel" class="tab-pane" id="credito">
                <div class="table-responsive">
                   <div class="panel panel-success ">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr style='font-size:85%;'>                
                                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Tipo crédito</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Vlr_Credito</th>                
                                    <th scope="col" style='background-color:#B9D5CE;'>Cuota</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Saldo</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'><span title="Numero de cuotas">N_C</span></th>
                                    <th scope="col" style='background-color:#B9D5CE;'><span title="Cuota actual">C_A</span></th>
                                    <th scope="col" style='background-color:#B9D5CE;'><span title="Estado crédito activo">E_C_A</span></th>
                                    <th scope="col" style='background-color:#B9D5CE;'><span title="Registro activo periodo">R_A_P</span></th>
                           
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($credito_empleado as $val): ?>
                                    <tr style='font-size:85%;'>
                                         <td><?= $val->id_credito?></td>
                                         <td><?= $val->codigoCredito->nombre_credito?></td>
                                         <td><?= $val->empleado->identificacion?></td>
                                         <td><?= $val->empleado->nombrecorto?></td>
                                         <td><?= '$'.number_format($val->vlr_credito,0)?></td>
                                         <td><?= '$'.number_format($val->vlr_cuota,0)?></td>
                                         <td><?= '$'.number_format($val->saldo_credito,0)?></td>
                                         <td><?= $val->numero_cuotas?></td>
                                         <td><?= $val->numero_cuota_actual?></td>
                                         <td><?= $val->Estadocredito?></td>
                                         <td><?= $val->estadoperiodo?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                       </div>    
                    </div>  
                </div>
            </div>
            <!-- TABS DE INTERESES -->
            <div role="tabpanel" class="tab-pane" id="intereses">
                <div class="table-responsive">
                   <div class="panel panel-success ">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr style='font-size:85%;'>                
                                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Id Prog.</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Tipo pago</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>                
                                    <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>  
                                    <th scope="col" style='background-color:#B9D5CE;'>Pago cesantia</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>pago interes</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>% pago</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>Nro dias</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($intereses as $val): ?>
                                    <tr style='font-size:85%;'>
                                         <td><?= $val->id_interes?></td>
                                         <td><?= $val->id_programacion?></td>
                                         <td><?= $val->tipoNomina->tipo_pago?></td>
                                         <td><?= $val->documento?></td>
                                         <td><?= $val->empleado->nombrecorto?></td>
                                         <td><?= '$'.number_format($val->vlr_cesantia,0)?></td>
                                         <td><?= '$'.number_format($val->vlr_intereses,0)?></td>
                                         <td><?= $val->porcentaje?></td>
                                         <td><?= $val->dias_generados?></td>
                                         <td><?= $val->usuariosistema?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                       </div>    
                    </div>  
                </div>
            </div>
        </div>

   </div>
    <?php ActiveForm::end(); ?>
</div>

   