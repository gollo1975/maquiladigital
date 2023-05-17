<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\ComprobanteEgresoTipo;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;


$this->title = 'Crear documentos contables';
$this->params['breadcrumbs'][] = $this->title;

?>


<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("comprobante-egreso/importardocumento"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$comprobante = ArrayHelper::map(ComprobanteEgresoTipo::find()->where(['=','permite_importar', 1])->orderBy('concepto ASC')->all(), 'id_comprobante_egreso_tipo', 'concepto');
$banco = ArrayHelper::map(app\models\Banco::find()->where(['=','activo', 1])->orderBy('entidad ASC')->all(), 'idbanco', 'entidad');?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading">
        Parametros de entrada
    </div>
	
    <div class="panel-body" id="importardocumentocontable">
        <div class="row" >
            <?= $formulario->field($form, 'tipo_proceso')->dropDownList(['1' => 'NOMIMAS', '2' => 'SERVICIOS','3' => 'CESANTIAS'],['prompt' =>'Seleccione...']) ?>
             <?= $formulario->field($form, 'tipo_comprobante')->widget(Select2::classname(), [
                'data' => $comprobante,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?=  $formulario->field($form, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
             <?=  $formulario->field($form, 'fecha_final')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
                 
            <?=  $formulario->field($form, 'fecha_pago')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                   'todayHighlight' => true]])
            ?>
            <?= $formulario->field($form, 'banco')->widget(Select2::classname(), [
                'data' => $banco,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
           
       </div>
    </div>    
    <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar documentos", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("comprobante-egreso/importardocumento") ?>" class="btn btn-default btn-sm"><span class='glyphicon glyphicon-refresh'></span> Limpiar</a>
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
        <?php if($model <> 0){?>
            Registros <span class="badge"> <?= count($model)?></span>
        <?php }?>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>                
                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha corte</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Valor pagado</th>
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                    
                </tr>
            </thead>
            <tbody>
                <?php 
                if($model <> 0){
                    foreach ($model as $val):
                        if($tipo_proceso == 1 or $tipo_proceso == 3){ ?>
                            <tr style='font-size:85%;'>             
                                <td><?= $val->cedula_empleado ?></td>    
                                 <td><?= $val->empleado->nombrecorto ?></td>
                                <td><?= $val->fecha_desde ?></td>
                                <td><?= $val->fecha_hasta ?></td>
                                <td style="text-align: right"><?= ''.number_format($val->total_pagar,0)?></td>
                                <td style= 'width: 25px; height: 25px;'><input type="checkbox" name="documento_identidad[]" value="<?= $val->id_programacion ?>"></td> 
                            </tr>  
                        <?php }else { ?>
                            <tr style='font-size:85%;'>             
                                <td><?= $val->documento ?></td>    
                                 <td><?= $val->operario ?></td>
                                <td><?= $val->fecha_inicio ?></td>
                                <td><?= $val->fecha_corte ?></td>
                                <td style="text-align: right"><?= ''.number_format($val->Total_pagar,0)?></td>
                                <td style= 'width: 25px; height: 25px;'><input type="checkbox" name="documento_identidad[]" value="<?= $val->id_pago ?>"></td> 

                            </tr>  
                        <?php }    
                    endforeach;
                }?>
            </tbody> 
        </table>   
       <?php  if($model <> 0){?>
            <div class="panel-footer text-right" >  
                <?= Html::submitButton("<span class='glyphicon glyphicon-plus'></span> Crear documentos", ["class" => "btn btn-success btn-sm", 'name' => 'creardocumento']) ?>
                                

            </div>
       <?php }?> 
    </div>
</div>
<?php $formulario->end() ?>

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



