<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\PagoAdicionalPermanente;
use app\models\ConceptoSalarios;
use app\models\Empleado;
use kartik\select2\Select2;
use kartik\date\DatePicker;

$this->title = 'Adicion x fecha';
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
$empleado = ArrayHelper::map(Empleado::find()->where(['=','contrato',1])->orderBy('nombrecorto ASC')->all(), 'id_empleado', 'nombrecorto');
$conceptosalario = ArrayHelper::map(ConceptoSalarios::find()->where(['adicion'=> 1])
                    ->andWhere(['=','tipo_adicion', 1]) 
                    ->orderBy('nombre_concepto ASC')->all(), 'codigo_salario', 'nombre_concepto');
$conceptosalarioHolologar = ArrayHelper::map(ConceptoSalarios::find()->where(['=','inicio_nomina', 1])
                                                                ->orWhere(['=','auxilio_transporte', 1])->orderBy('nombre_concepto ASC')->all(), 'codigo_salario', 'nombre_concepto');
?>
        <div class="panel panel-success">
            <div class="panel-heading">
                Información: Adiciona x fecha
            </div>
            <div class="panel-body">
                <div class="row">

                     <?= $form->field($model, 'id_empleado')->widget(Select2::classname(), [
                    'data' => $empleado,
                    'options' => ['placeholder' => 'Seleccione el empleado'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
                    ?>
                       <?= $form->field($model, 'codigo_salario')->widget(Select2::classname(), [
                    'data' => $conceptosalario,
                    'options' => ['placeholder' => 'Seleccione el concepto'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
                    ?>
                </div>        
                <div class="row">
                    <?= $form->field($model, 'vlr_adicion')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'homologar')->dropdownList(['0' => 'NO', '1' => 'SI']) ?>
                </div>
                <div class="row" col>
                    <?= $form->field($model, 'codigo_homologado')->widget(Select2::classname(), [
                            'data' => $conceptosalarioHolologar,
                            'options' => ['placeholder' => 'Seleccione el concepto'],
                            'pluginOptions' => [
                                'allowClear' => true ]]);
                            ?>
                    <?= $form->field($model, 'dias_contrato')->textInput(['maxlength' => true]) ?>
                  
               </div>
                <div class="row" col>
                  <?= $form->field($model, 'detalle')->textarea(['maxlength' => true]) ?>
                  
               </div>
                <div class="panel panel-success">
                    <div class="panel-heading">
                        Aplicar a:
                    </div>
                    <div class="panel-body">
                        <div class="checkbox checkbox-success" align ="left">
                                <?= $form->field($model, 'aplicar_dia_laborado')->checkBox(['label' => 'Dia laborado','1' =>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplicar_dia_laborado']) ?>
                                <?= $form->field($model, 'aplicar_prima')->checkBox(['label' => 'Primas semestrales',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplicar_prima']) ?>
                                <?= $form->field($model, 'aplicar_cesantias')->checkBox(['label' => 'Cesantias',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplicar_cesantias']) ?>
                     </div>
                </div>   
                <div class="panel-footer text-right">			
                    <a href="<?= Url::toRoute(["pago-adicional-fecha/view", 'id'=>$id, 'fecha_corte' => $fecha_corte]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
                </div>
            </div>
        </div>
<?php $form->end() ?>     


