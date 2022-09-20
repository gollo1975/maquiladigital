<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\TiposMaquinas;
use app\models\MarcaMaquinas;
use kartik\date\DatePicker;
use kartik\select2\Select2;

?>

<?php
$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
            'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
            'options' => []
        ],
        ]);
?>

<?php
$marcas= ArrayHelper::map(\app\models\MarcaMaquinas::find()->orderBy('descripcion ASC')->all(), 'id_marca', 'descripcion');
$tipos= ArrayHelper::map(\app\models\TiposMaquinas::find()->orderBy('descripcion ASC')->all(), 'id_tipo', 'descripcion');
$bodegas= ArrayHelper::map(\app\models\Bodega::find()->orderBy('descripcion ASC')->all(), 'id_bodega', 'descripcion');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Maquinas de confeccion
    </div>
    
    <div class="panel-body">
        <div class="row">
             <?= $form->field($model, 'id_tipo')->widget(Select2::classname(), [
                    'data' => $tipos,
                    'options' => ['placeholder' => 'Seleccione...'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
            ?>
            <?= $form->field($model, 'codigo_maquina')->textInput(['maxlength' => true]) ?>
        </div>
        
        <div class="row">
            <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'serial')->textInput(['maxlength' => true]) ?>    
           
        </div>        
       
        <div class="row">
             <?= $form->field($model, 'id_marca')->widget(Select2::classname(), [
                    'data' => $marcas,
                    'options' => ['placeholder' => 'Seleccione...'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
            ?>
            <?= $form->field($model, 'modelo')->textInput(['maxlength' => true]) ?>    
           
        </div>
        <div  class="row">
             <?=  $form->field($model, 'fecha_compra')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true]])
            ?>
             <?= $form->field($model, 'id_bodega')->widget(Select2::classname(), [
                    'data' => $bodegas,
                    'options' => ['placeholder' => 'Seleccione...'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
            ?>
            
        </div>
        
        <div class="panel-footer text-right">			
            <a href="<?= Url::toRoute("maquinas/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>
<?php $form->end() ?>     

</div>
