<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Cliente;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */
/* @var $form yii\widgets\ActiveForm */
$tipoProducto = ArrayHelper::map(app\models\TipoProducto::find()->orderBy('concepto ASC')->all(), 'id_tipo_producto', 'concepto');
?>

<?php
$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
            'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
            'options' => []
            ],
        ]);
?>
<?php ?>
<div class="panel panel-success">
    <div class="panel-heading">
        Registro
    </div>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'idcliente')->widget(Select2::classname(), [
            'data' => $clientes,
            'options' => ['placeholder' => 'Seleccione un cliente...'],
            'pluginOptions' => ['allowClear' => true],
            'pluginEvents' => [
                "change" => 'function() { $.get( "' . Url::toRoute('orden-produccion/productos') . '", { id: $(this).val() } )
                        .done(function( data ) {
                            $( "#' . Html::getInputId($model, 'codigoproducto') . '" ).html( data );
                        });
                }',
            ],
            ]); ?>

            <?= $form->field($model, 'codigoproducto')->widget(Select2::classname(), [
                'data' => $codigos,
                'options' => ['placeholder' => 'Seleccione un producto'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]); ?>  

        </div>
        <div class="row">
            <?= $form->field($model, 'idtipo')->dropDownList($ordenproducciontipos, ['prompt' => 'Seleccione un tipo...']) ?>
            <?= $form->field($model, 'ordenproduccion')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'ordenproduccionext')->textInput(['maxlength' => true, 'value' => 0]) ?>
            <?= $form->field($model, 'duracion')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="row">
             <?=
            $form->field($model, 'fechallegada')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']
                ])
            ?>
            <?=
            $form->field($model, 'fechaprocesada')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']
                ])
            ?>
        </div>
        <div class="row">
             <?=
            $form->field($model, 'fechaentrega')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']
                ])
            ?>
            <?= $form->field($model, 'ponderacion')->textInput(['maxlength' => true, 'value' => 0]) ?>
           
        </div>
        <div class="row">
            <?= $form->field($model, 'aplicar_balanceo')->dropDownList(['1'=> 'SI', '0'=> 'NO'], ['prompt' => 'Seleccione']) ?>
            <?= $form->field($model, 'exportacion')->dropDownList(['1' => 'NO', '2' => 'SI'],['prompt' =>'Seleccione...','onchange' => 'mostrarcampo()', 'id' => 'exportacion'])?>
        </div>
        <div class="row">
            <div id="porcentaje_exportacion" style="display:block"> <?= $form->field($model, 'porcentaje_exportacion')->textInput(['maxlength' => true]) ?></div>
            <?= $form->field($model, 'id_tipo_producto')->widget(Select2::classname(), [
                'data' => $tipoProducto,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'orientation' => 'bottom'
                ],
            ]); ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
        </div>
         <div class="checkbox checkbox-success" align ="center">
               <?= $form->field($model, 'pagada')->checkBox(['label' => 'Pagada',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'pagada']) ?>
              <?= $form->field($model, 'lavanderia')->checkBox(['label' => 'Lavanderia',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'lavanderia']) ?>

         </div>
        
         <div class="panel-footer text-right">				
            <a href="<?= Url::toRoute("orden-produccion/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm"]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    function mostrarcampo(){
        let exportacion = document.getElementById('exportacion').value;
        if(exportacion === '1'){
           porcentaje_exportacion.style.display = "none";
        } else {
            porcentaje_exportacion.style.display = "block";
           
        }
    }
</script>    
