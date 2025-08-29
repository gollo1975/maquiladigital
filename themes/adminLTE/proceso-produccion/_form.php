<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use app\models\TipoProducto;
/* @var $this yii\web\View */
/* @var $model app\models\ProcesoProduccion */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
]);
//vectores
$tipoProducto = yii\helpers\ArrayHelper::map(TipoProducto::find()->orderBy(['concepto' => SORT_ASC])->all(), 'id_tipo_producto', 'concepto')
?>


<div class="panel panel-success">
    <div class="panel-heading">
        <h4>Operaciones de prendas</h4>
    </div>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'proceso')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="row">
           <?= $form->field($model, 'estado')->dropDownList(['1' => 'SEGUNDOS', '2' => 'MINUTOS'],['prompt' =>'Seleccione...','onchange' => 'mostrarVariable()', 'id' => 'estado'])?>
        </div>
        <div class="row">
             <div id="segundos" style="display:none"> <?= $form->field($model, 'segundos')->textInput(['maxlength' => true]) ?></div>
             <div id="minutos" style="display:none"> <?= $form->field($model, 'minutos')->textInput(['maxlength' => true]) ?></div>
        </div>      
        <div class="row">
            <?= $form->field($model, 'estandarizado')->dropDownList(['1'=> 'SI', '0'=>'NO'], ['prompt' => 'Seleccione una opcion...']) ?>
        </div>
         <div class="row">
                <?= $form->field($model, 'id_tipo_producto')->widget(Select2::classname(), [
                'data' => $tipoProducto,
                'options' => ['placeholder' => 'Seleccion el producto'],
                'pluginOptions' => [
                    'allowClear' => true ],
                 ]); ?>
        </div>		
        <div class="panel-footer text-right">            
            <a href="<?= Url::toRoute("proceso-produccion/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    function mostrarVariable(){
        let segundo = document.getElementById('estado').value;
        if(segundo === '1'){
           segundos.style.display = "block";
            minutos.style.display = "none";
        } else {
            minutos.style.display = "block";
            segundos.style.display = "none";
           
        }
    }
</script> 
