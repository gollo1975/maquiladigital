<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use kartik\select2\Select2;
?>
<?php
$agrupados = ArrayHelper::map(\app\models\AgruparConceptoSalario::find()->all(), 'id_agrupado', 'concepto');
$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
            'template' => '{label}<div class="col-sm-3 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
            'options' => []
        ],
        ]);
?>
        <div class="panel panel-success">
            <div class="panel-heading">
                Información concepto de salarios
            </div>
            <div class="panel-body">
                <div class="row">
                    <?= $form->field($model, 'codigo_salario')->textInput(['maxlength' => true]) ?>    
                    <?= $form->field($model, 'nombre_concepto')->textInput(['maxlength' => true]) ?>
                </div>        
                <div class="row">
                   <?= $form->field($model, 'porcentaje')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'porcentaje_tiempo_extra')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="row">
                    <?= $form->field($model, 'debito_credito')->dropDownList(['1'=> 'SUMA', '2'=>'RESTA', '0'=>'NEUTRO'], ['prompt' => 'Seleccione una opcion...']) ?>
                    <?= $form->field($model, 'tipo_adicion')->dropDownList(['1'=> 'BONIFICACION', '2'=>'DESCUENTO', '0'=>'NO APLICA'], ['prompt' => 'Seleccione una opcion...']) ?>
                </div>
                 <div class="row">
                    <?= $form->field($model, 'devengado_deduccion')->dropDownList(['1'=> 'DEVENGADO', '2'=>'DEDUCCION'], ['prompt' => 'Seleccione una opcion...']) ?>
                     <?= $form->field($model, 'id_agrupado')->widget(Select2::classname(), [
                        'data' => $agrupados,
                        'options' => ['placeholder' => 'Seleccione.... '],
                        'pluginOptions' => [
                        'allowClear' => true ]]);
                       ?>
                </div>
                <div class="panel panel-success">
                    <div class="panel-heading">
                        Seleccione la opción..
                    </div>
                    <div class="panel-body">
                        <div class="checkbox checkbox-success" align ="left">
                                <?= $form->field($model, 'compone_salario')->checkBox(['label' => 'Compone salario','1' =>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'compone_salario']) ?>
                                <?= $form->field($model, 'aplica_porcentaje')->checkBox(['label' => 'Aplica_porcentaje',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplica_porcentaje']) ?>
                                <?= $form->field($model, 'prestacional')->checkBox(['label' => 'Prestacional',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'prestacional']) ?>
                                <?= $form->field($model, 'ingreso_base_cotizacion')->checkBox(['label' => 'Ingreso base cotización',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'ingreso_base_cotizacion']) ?>
                        </div>
                         <div class="checkbox checkbox-success" align ="left">
                                <?= $form->field($model, 'adicion')->checkBox(['label' => 'Adición',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'adicion']) ?>
                                <?= $form->field($model, 'auxilio_transporte')->checkBox(['label' => 'Auxilio transporte',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'auxilio_transporte']) ?>
                                <?= $form->field($model, 'concepto_incapacidad')->checkBox(['label' => 'Concepto incapacidad',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'concepto_incapacidad']) ?>
                                <?= $form->field($model, 'concepto_pension')->checkBox(['label' => 'Concepto pensión',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'concepto_pension']) ?>
                        </div>
                        <div class="checkbox checkbox-success" align ="left">
                                <?= $form->field($model, 'concepto_salud')->checkBox(['label' => 'Concepto_salud',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'concepto_salud']) ?>
                                <?= $form->field($model, 'concepto_vacacion')->checkBox(['label' => 'Concepto_vacacion',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'concepto_vacacion']) ?>
                                <?= $form->field($model, 'recargo_nocturno')->checkBox(['label' => 'Recargo nocturno',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'recargo_nocturno']) ?>
                                <?= $form->field($model, 'ingreso_base_prestacional')->checkBox(['label' => 'Ingreso base prestacional',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'ingreso_base_prestacional']) ?>
                        </div>
                         <div class="checkbox checkbox-success" align ="left">
                                <?= $form->field($model, 'provisiona_vacacion')->checkBox(['label' => 'Provisiona vacaciones',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'provisiona_vacacion']) ?>
                                <?= $form->field($model, 'provisiona_indemnizacion')->checkBox(['label' => 'Provisiona indemnización',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'provisiona_indemnizacion']) ?>
                                <?= $form->field($model, 'inicio_nomina')->checkBox(['label' => 'Inicia_nomina',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'inicio_nomina']) ?>
                                <?= $form->field($model, 'hora_extra')->checkBox(['label' => 'Hora extra',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'hora_extra']) ?>
                         </div>     
                        <div class="checkbox checkbox-success" align ="left">
                               
                               <?= $form->field($model, 'concepto_comision')->checkBox(['label' => 'Concepto_comisión',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'concepto_comision']) ?>
                               <?= $form->field($model, 'concepto_licencia')->checkBox(['label' => 'Concepto licencia',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'concepto_licencia']) ?>
                              <?= $form->field($model, 'fsp')->checkBox(['label' => 'Concepto FSP',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'fsp']) ?>
                              <?= $form->field($model, 'concepto_prima')->checkBox(['label' => 'Concepto prima',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'concepto_prima']) ?>
                        </div>
                         <div class="checkbox checkbox-success" align ="left">     
                            <?= $form->field($model, 'concepto_cesantias')->checkBox(['label' => 'Concepto censantias',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'concepto_cesantias']) ?> 
                            <?= $form->field($model, 'intereses')->checkBox(['label' => 'Intereses',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'intereses']) ?> 
                        </div>     
                        </div>
                     </div>
                </div>   
                <div class="panel-footer text-right">			
                    <a href="<?= Url::toRoute("concepto-salarios/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
                </div>
            </div>
        </div>
<?php $form->end() ?>     


