<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;


$this->title = 'Proyección de prestaciones';
$this->params['breadcrumbs'][] = $this->title;
$grupoPago = ArrayHelper::map(\app\models\GrupoPago::find()->orderBy ('grupo_pago ASC')->all(), 'id_grupo_pago', 'grupo_pago');
$empleado = ArrayHelper::map(\app\models\Empleado::find()->orderBy ('nombrecorto ASC')->all(), 'id_empleado', 'nombrecorto');
?>
<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['documento_electronico'], ['class' => 'btn btn-primary btn-sm']) ?>
</p>    
<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute(["programacion-nomina/proyeccion_prestaciones_sociales"]),
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
        <div class="row" >
          <?= $formulario->field($form, 'empleado')->widget(Select2::classname(), [
                'data' => $empleado,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'grupo_pago')->widget(Select2::classname(), [
                'data' => $grupoPago,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
       </div>
    </div>    
    <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar documentos", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute(["programacion-nomina/proyeccion_prestaciones_sociales"]) ?>" class="btn btn-default btn-sm"><span class='glyphicon glyphicon-refresh'></span> Limpiar</a>
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
                    <th scope="col" style='background-color:#B9D5CE;'>No contrato</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                    <th scope="col" style='background-color:#B9D5CE;'>F. inicio contrato</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Salario</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Grupo de pago</th>
                   
                    
                </tr>
            </thead>
            <tbody>
                <?php 
                
                foreach ($model as $val):?>
                    <tr style='font-size:85%;'> 
                        <td><?= $val->id_contrato?></td>  
                        <td><?= $val->identificacion ?></td>    
                        <td><?= $val->empleado->nombrecorto ?></td>
                        <td><?= $val->fecha_inicio ?></td>
                        <td style="text-align: right"><?= ''.number_format($val->salario,0)?></td>
                        <td><?= $val->grupoPago->grupo_pago ?></td>
                        
                       
                    </tr>  
                <?php endforeach;?>
               
            </tbody> 
        </table>   
            <div class="panel-footer text-right" >  
                   <!-- Inicio Nuevo Detalle proceso -->
                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Generar proyecciones',
                    ['/programacion-nomina/crear_proyeccion_prestaciones'],
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




