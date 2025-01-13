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


$this->title = 'Documentos electronicos';
$this->params['breadcrumbs'][] = $this->title;

?>
<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['documento_electronico'], ['class' => 'btn btn-primary btn-sm']) ?>
</p>    
<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute(["programacion-nomina/vista_empleados",'id_periodo' => $id_periodo]),
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
            <?= $formulario->field($form, "documento")->input("search") ?>
            <?= $formulario->field($form, "empleado")->input("search") ?>
       </div>
    </div>    
    <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar documentos", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute(["programacion-nomina/vista_empleados",'id_periodo' => $id_periodo]) ?>" class="btn btn-default btn-sm"><span class='glyphicon glyphicon-refresh'></span> Limpiar</a>
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
         Registros <span class="badge"> <?= count($model)?></span>
   </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>  
                    <th scope="col" style='background-color:#B9D5CE;'>Consecutivo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha corte</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Devengado</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Deduccion</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Total pagar</th>
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                    
                </tr>
            </thead>
            <tbody>
                <?php 
                 $periodo = app\models\PeriodoNominaElectronica::findOne($id_periodo);
                foreach ($model as $val):?>
                    <tr style='font-size:85%;'> 
                        <td><?= $val->consecutivo?> - <?= $val->numero_nomina_electronica?></td>  
                        <td><?= $val->documento_empleado ?></td>    
                        <td><?= $val->empleado->nombrecorto ?></td>
                        <td><?= $val->fecha_inicio_nomina ?></td>
                        <td><?= $val->fecha_final_nomina ?></td>
                        <td style="text-align: right"><?= ''.number_format($val->total_devengado,0)?></td>
                        <td style="text-align: right"><?= ''.number_format($val->total_deduccion,0)?></td>
                        <td style="text-align: right"><?= ''.number_format($val->total_pagar,0)?></td>
                        <td style= 'width: 25px; height: 25px;'><input type="checkbox" name="documento_electronico[]" value="<?= $val->id_nomina_electronica ?>"></td> 
                    </tr>  
                <?php endforeach;?>
               
            </tbody> 
        </table>   
        <?php if($periodo->cerrar_proceso == 0){?>
            <div class="panel-footer text-right" >  
                    <?= Html::submitButton("<span class='glyphicon glyphicon-plus'></span> Crear documentos", ["class" => "btn btn-success btn-sm", 'name' => 'crear_documento_electronico']) ?>

            </div>
        <?php }?>

    </div>
</div>
<?php $formulario->end() ?>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

<script type="text/javascript">
	function marcar(source) 
	{
		checkboxes=document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
		for(i=0;i<checkboxes.length;i++) //recoremos todos los controles
		{
			if(checkboxes[i].type == "checkbox") //solo si es un checkbox entramos
			{
				checkboxes[i].checked=source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
			}
		}
	}
</script>



