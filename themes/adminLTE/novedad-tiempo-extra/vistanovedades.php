<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Producto */
$this->title = 'Tiempo extra';
$this->params['breadcrumbs'][] = ['label' => 'Novedades de nomina', 'url' => ['index']];
$this->params['breadcrumbs'][] = $id;
?>

<div class="programacion-nomina-view">
   
 <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
    ]);
    $contador = count($detalle); 
    ?>
    <div class="panel panel-success">
        <div class="panel-heading">
            Informacion: Periodo de pago
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style='font-size: 85%;'>
                    <th><?= Html::activeLabel($model, 'id_grupo_pago') ?>:</th>
                   <td><?= Html::encode($model->grupoPago->grupo_pago) ?></td>  
                 
                   <th><?= Html::activeLabel($model, 'fecha_desde') ?>:</th>
                   <td><?= Html::encode($model->fecha_desde) ?></td>  
                    <th><?= Html::activeLabel($model, 'fecha_hasta') ?>:</th>
                   <td><?= Html::encode($model->fecha_hasta) ?></td> 
                   <th><?= Html::activeLabel($model, 'dias_pago') ?>:</th>
                   <td><?= Html::encode($model->dias_pago) ?></td> 
                </tr>               
            </table>
        </div>
    </div>
   <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#listado_empleados" aria-controls="listado_empleados" role="tab" data-toggle="tab">Listado de empleados <span class="badge"><?= count($detalle) ?></span></a></li>
            <li role="presentation" ><a href="#listado_novedades" aria-controls="listado_novedades" role="tab" data-toggle="tab">Listado de novedades <span class="badge"><?= count($novedades) ?></span></a></li>
        </ul>    
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="listado_empleados">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size: 85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Documento</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Desde</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Hasta</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Inicio Contrato</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Contrato</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo_salario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Salario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($detalle as $val): ?>
                                        <tr style='font-size: 85%;'>
                                            <td><?= $val->cedula_empleado ?></td>
                                            <td><?= $val->empleado->nombrecorto ?></td>
                                            <td><?= $val->fecha_desde ?></td>
                                            <td><?= $val->fecha_hasta ?></td>
                                            <td><?= $val->fecha_inicio_contrato ?></td>
                                            <td><?= $val->id_contrato ?></td>
                                            <td><?= $val->tipo_salario ?></td>
                                            <td><?= '$'.number_format($val->salario_contrato,0) ?></td>
                                            <?php
                                               if($val->tipo_salario == 'VARIABLE'){?>
                                                    <td style="width: 20px; height: 20px">
                                                        <?= Html::a('<span class="glyphicon glyphicon-book"></span>',            
                                                        ['/novedad-tiempo-extra/creartiempoextra','id' => $val->id_periodo_pago_nomina, 'id_programacion'=>$val->id_programacion, 'tipo_salario' => $val->tipo_salario],
                                                            [
                                                                'title' => 'Crear novedades',
                                                                'data-toggle'=>'modal',
                                                                'data-target'=>'#modalcreartiempoextra'.$val->id_periodo_pago_nomina,
                                                                'class' => '',
                                                                'data-backdrop' => 'static',
                                                            ]
                                                        );
                                                        ?>
                                                         <div class="modal remote fade" id="modalcreartiempoextra<?= $val->id_periodo_pago_nomina ?>">
                                                            <div class="modal-dialog modal-lg" style="width:730px">
                                                                <div class="modal-content"></div>
                                                            </div>
                                                         </div>
                                                     </td>
                                                <?php }?>    
                                                <td style="width: 20px; height: 20px">    
                                                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>',            
                                                    ['/novedad-tiempo-extra/editartiempoextra','id_empleado' => $val->id_empleado, 'id' => $val->id_periodo_pago_nomina],
                                                        [
                                                            'title' => 'Editar novedades',
                                                            'data-toggle'=>'modal',
                                                            'data-target'=>'#modaleditartiempoextra'.$val->id_empleado,
                                                            'class' => '',
                                                            'data-backdrop' => 'static',
                                                        ]
                                                    );
                                                    ?>

                                                    <div class="modal remote fade" id="modaleditartiempoextra<?= $val->id_empleado ?>">
                                                        <div class="modal-dialog modal-lg" style="width:730px">
                                                            <div class="modal-content"></div>
                                                        </div>
                                                   </div>
                                                </td>

                                        </tr>     
                                    <?php endforeach; ?>
                                </tbody>        
                            </table>
                        </div>            
                    </div>
                </div>
            </div>
            <!--TERMINA TABS-->
             <div role="tabpanel" class="tab-pane" id="listado_novedades">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size: 85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Documento</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Nombre concepto</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>No horas</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Vr. hora</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Total pagar</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($novedades as $val): ?>
                                        <tr style='font-size: 85%;'>
                                            <td><?= $val->empleado->identificacion?></td>
                                            <td><?= $val->empleado->nombrecorto ?></td>
                                            <td><?= $val->codigo_salario ?></td>
                                            <td><?= $val->concepto ?></td>
                                            <td><?= $val->nro_horas?></td>
                                            <td style ="text-align:right"><?= '$'. number_format($val->vlr_hora,2) ?></td>
                                            <td style="text-align:right"><?= '$'. number_format($val->total_novedad,0 )?></td>
                                            <td style="width: 25px; height: 25px;">
                                                 <?=
                                                    Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['eliminar_detalle', 'id_detalle' => $val->id_novedad, 'id' => $model->id_periodo_pago_nomina], [
                                                        'class' => '',
                                                        'data' => [
                                                            'confirm' => 'Esta seguro de eliminar el registro?',
                                                            'method' => 'post',
                                                        ],
                                                    ])
                                                    ?>
                                            </td>  
                                        </tr>     
                                    <?php endforeach; ?>
                                </tbody>        
                            </table>
                        </div>            
                    </div>
                </div>
            </div>
            <!--TERMINA TABS-->
        </div>    
    </div>
    <?php ActiveForm::end(); ?>
</div>

