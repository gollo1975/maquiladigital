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

//models

use app\models\PlantaEmpresa;

$this->title = 'Aplicar porcentaje';
$this->params['breadcrumbs'][] = $this->title;
?>
<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("valor-prenda-unidad/aplicarporcentajeprenda"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$bodega = ArrayHelper::map(PlantaEmpresa::find()->all(), 'id_planta', 'nombre_planta');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading">
        Parametros de entrada
    </div>
	
    <div class="panel-body" id="importardocumentocontable">
        <div class="row" >
             <?= $formulario->field($form, "porcentaje")->input("search") ?>
             <?= $formulario->field($form, 'planta')->widget(Select2::classname(), [
                'data' => $bodega,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
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
            <?= $formulario->field($form, 'tipo_empleado')->dropDownList(['1' => 'VINCULADO', '0' => 'POR SERVICIOS'],['prompt' => 'Seleccione el estado ...']) ?> 
                 
        </div>
         
    </div>    
    <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar pagos", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/aplicarporcentajeprenda") ?>" class="btn btn-default btn-sm"><span class='glyphicon glyphicon-refresh'></span> Limpiar</a>
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
                <tr style ='font-size:95%;'>                
                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha pago</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Vr pagado</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                     <th scope="col" style='background-color:#B9D5CE;'>Aplico</th>
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                    
                </tr>
            </thead>
            <tbody>
                <?php 
                if($model <> 0){
                    foreach ($model as $val):
                        $operario = \app\models\Operarios::find()->where(['=','id_operario', $val->id_operario])
                                                                 ->andWhere(['=','vinculado', $tipo])->one();
                        if($operario){
                            ?>
                            <tr style='font-size:85%;'>             
                                <td><?= $val->consecutivo ?></td>    
                                <td><?= $val->operarioProduccion->documento ?></td>    
                                <td><?= $val->operarioProduccion->nombrecompleto ?></td> 
                                <td><?= $val->dia_pago ?></td>
                                <td style="text-align: right"><?= ''.number_format($val->vlr_pago,0)?></td>
                                <td><?= $val->observacion ?></td>
                                <td><?= $val->aplicaPorcentaje ?></td>
                                <td style= 'width: 25px; height: 25px;'><input type="checkbox" name="consecutivo[]" value="<?= $val->consecutivo ?>"></td> 
                            </tr>  
                        <?php }    
                     endforeach;
                }?>
            </tbody> 
        </table>   
       <?php  if($model <> 0){?>
            <div class="panel-footer text-right" >  
                <?= Html::submitButton("<span class='glyphicon glyphicon-eject'></span> Aplicar porcentaje", ["class" => "btn btn-success btn-sm", 'name' => 'aplicarporcentaje']) ?>
                                

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



