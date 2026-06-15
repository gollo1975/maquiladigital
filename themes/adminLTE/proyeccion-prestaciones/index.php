<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use kartik\date\DatePicker;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;


$this->title = 'Proyección de prestaciones';
$this->params['breadcrumbs'][] = $this->title;
?>
  
<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute(["proyeccion-prestaciones/index"]),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading">
        Parametros de entrada
    </div>
	
    <div class="panel-body" id="importardocumentocontable">
       <div class="row">
            <?= $formulario->field($form, 'desde')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'orientation' => 'bottom'
                ]
            ]) ?>

            <?= $formulario->field($form, 'hasta')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'orientation' => 'bottom'
                ]
            ]) ?>
        </div>
    </div>    
    <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar documentos", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute(["proyeccion-prestaciones/index"]) ?>" class="btn btn-default btn-sm"><span class='glyphicon glyphicon-refresh'></span> Limpiar</a>
    </div>
    
</div>
<?php $formulario->end() ?>
<?php
    $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
         Registros <span class="badge"> <?= $pagination->totalCount ?></span>
   </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>  
                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha corte</th>
                    <th scope="col" style='background-color:#B9D5CE;'>T. Primas</th>
                    <th scope="col" style='background-color:#B9D5CE;'>T. Cesantias</th>
                    <th scope="col" style='background-color:#B9D5CE;'>T. Intereses</th>
                    <th scope="col" style='background-color:#B9D5CE;'>T. Vacacaiones</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Total generado</th>
                    <th scope="col" style='background-color:#B9D5CE;'></th>
                    <th scope="col" style='background-color:#B9D5CE;'></th> 
                    
                </tr>
            </thead>
            <tbody>
                <?php 
                
                foreach ($model as $val):?>
                    <tr style='font-size:85%;'> 
                        <td><?= $val->id_proyeccion?></td>  
                        <td><?= $val->fecha_inicio ?></td>    
                        <td><?= $val->fecha_corte ?></td>
                        <td style="text-align: right"><?= ''.number_format($val->total_primas,0)?></td>
                        <td style="text-align: right"><?= ''.number_format($val->total_cesantias,0)?></td>
                        <td style="text-align: right"><?= ''.number_format($val->total_intereses,0)?></td>
                        <td style="text-align: right"><?= ''.number_format($val->total_vacaciones,0)?></td>
                        <td style="text-align: right"><?= ''.number_format($val->gran_total,0)?></td>
                        <td style= 'width: 25px;'>
                            <a href="<?= Url::toRoute(["proyeccion-prestaciones/view", "id" => $val->id_proyeccion]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                        </td>
                        <td style='width: 25px; height: 25px;'>
                            <?php if(!\app\models\ProyeccionPrestacionesDetalle::find()->where(['id_proyeccion' => $val->id_proyeccion])->one()){?>
                                <?= Html::a('<span class="glyphicon glyphicon-user"></span> Cargar', ['generar_proyeccion_prestacional', 'id' => $val->id_proyeccion, 'fecha_inicio' => $val->fecha_inicio, 'fecha_corte' => $val->fecha_corte], [
                                    'class' => 'btn btn-success btn-xs', // <-- Clases de botón de Bootstrap
                                    'title' => 'Proceso que permite cargar todos los contratos activos de acuerdo al rango de fecha.', 
                                    'data' => [
                                        'confirm' => '¿Está seguro de CARGAR todos los contratos activos a ' . date('d \d\e F \d\e Y', strtotime($val->fecha_corte)) . '?',
                                        'method' => 'post',
                                    ],
                                ]);
                            } ?>
                            
                        </td>
                    </tr>  
                <?php endforeach;?>
               
            </tbody> 
        </table>   
            <div class="panel-footer text-right" >  
                   <!-- Inicio Nuevo Detalle proceso -->
                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Generar proyecciones',
                    ['/proyeccion-prestaciones/crear_proyeccion_prestaciones'],
                    [
                        'title' => 'Permite generar la proyeccion de prestaciones sociales',
                        'data-toggle'=>'modal',
                        'data-target'=>'#modalcrearproyeccionprestaciones',
                        'class' => 'btn btn-info btn-xs'
                       
                     ])    
                    ?>
             <div class="modal remote fade" id="modalcrearproyeccionprestaciones" data-backdrop="static" data-keyboard="false">
                     <div class="modal-dialog modal-lg-centered">
                         <div class="modal-content"></div>
                     </div>
             </div> 
            </div>
    </div>
</div>
<?php $form->end() ?>
<?= LinkPager::widget(['pagination' => $pagination]) ?>




