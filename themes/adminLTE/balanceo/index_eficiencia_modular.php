<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Cliente;
use app\models\PlantaEmpresa;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;


$this->title = 'Eficiencia modular';
$this->params['breadcrumbs'][] = $this->title;

?>


<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("balanceo/index_eficiencia_modular"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$cliente = ArrayHelper::map(Cliente::find()->where(['=','proceso', 1])->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto');
$planta = ArrayHelper::map(PlantaEmpresa::find()->orderBy('nombre_planta ASC')->all(), 'id_planta', 'nombre_planta');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading">
        Parametros de entrada
    </div>
	
    <div class="panel-body" id="index_eficiencia_modular">
        <div class="row" >
             <?= $formulario->field($form, 'cliente')->widget(Select2::classname(), [
                'data' => $cliente,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
              <?= $formulario->field($form, "orden_produccion")->input("search") ?>
        </div>
         <div class="row" >
             <?=  $formulario->field($form, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
             <?=  $formulario->field($form, 'fecha_corte')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
                 
        </div>
         <div class="row" >
               <?= $formulario->field($form, 'planta')->widget(Select2::classname(), [
                'data' => $planta,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
              <?= $formulario->field($form, "nro_balanceo")->input("search") ?>
           
       </div>
    </div>    
    <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar registros", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("balanceo/index_eficiencia_modular") ?>" class="btn btn-default btn-sm"><span class='glyphicon glyphicon-refresh'></span> Limpiar</a>
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
                    <th scope="col" style='background-color:#B9D5CE;'>No balanceo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Op Interna</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Total dias</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Proceso</th>
                    <th scope="col" style='background-color:#B9D5CE;'>No operarios</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Sam balanceo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Eficiencia</th>
                     <th style='background-color:#B9D5CE;'></th>
                    
                </tr>
            </thead>
            <tbody>
                <?php 
                if($model <> 0){
                    foreach ($model as $val):
                        ?>
                        <tr style='font-size:85%;'>             
                            <td><?= $val->id_balanceo ?></td>    
                            <td><?= $val->idordenproduccion ?></td>    
                            <td><?= $val->cliente->nombrecorto ?></td>
                            <td><?= $val->fecha_inicio ?></td>
                            <td style="text-align: right"><?= ''.number_format($val->numero_dias_balanceo,0) ?></td>
                            <td style="text-align: right"><?= ''.number_format($val->ordenproduccion->cantidad,0) ?></td>
                            <td><?= $val->plantaempresa->nombre_planta ?></td>
                            <?php if($val->id_proceso_confeccion == 1){?>
                              <td style='background-color:#B9CDD1;'><?= $val->procesoconfeccion->descripcion_proceso ?></td>
                            <?php } else {
                                if($val->id_proceso_confeccion == 2){?>
                                   <td style='background-color:#CDB9D1;'><?= $val->procesoconfeccion->descripcion_proceso ?></td>
                                <?php }else{?>
                                   <td style='background-color:#B4C585;'><?= $val->procesoconfeccion->descripcion_proceso ?></td>
                                <?php }
                            }?>  
                            <td style="text-align: right"><?= ''.number_format($val->cantidad_empleados,0) ?></td>
                            <td style="text-align: right"><?= ''.number_format($val->tiempo_balanceo,2) ?></td>  
                            <td style="text-align: right; size:120%; background-color:#3AAFC4; color: #070808"><b><?= ''.number_format($val->total_eficiencia,2)?>%</b></td>
                            <td style="width: 20px; height: 20px;">
                                 <?= Html::a('<span class="glyphicon glyphicon-th-list"></span>', ['/orden-produccion/eficienciamodulo', 'id_balanceo' => $val->id_balanceo], ['target' => '_blank']) ?>
                            </td>
                        </tr>  
                    <?php endforeach;
                }?>
            </tbody> 
        </table>   
    </div>
</div>
<?php $formulario->end() ?>
<?= LinkPager::widget(['pagination' => $pagination]) ?>




