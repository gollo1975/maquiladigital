<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Licencia;
use app\models\ConfiguracionLicencia;
use app\models\Empleado;
use kartik\select2\Select2;
use kartik\date\DatePicker;

$this->title = 'Licencias';
$this->params['breadcrumbs'][] = ['label' => 'Licencias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
$configuracionlicencia = ArrayHelper::map(ConfiguracionLicencia::find()->all(), 'codigo_licencia', 'concepto');
?>
<?php
    if ($mensaje != ""){
        ?> <div class="alert alert-danger"><?= $mensaje ?></div> <?php
    }
?>
          
        <div class="panel panel-success">
            <div class="panel-heading">
                Información: Licencias
            </div>
            <div class="panel-body">
                <div class="row">

                    <?= $form->field($model, 'codigo_licencia')->dropDownList($configuracionlicencia, ['prompt' => 'Seleccione...']) ?>
                     <?= $form->field($model, 'id_empleado')->widget(Select2::classname(), [
                    'data' => $empleado,
                    'options' => ['placeholder' => 'Seleccione el empleado'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
                    ?>
                </div>        
                <div class="row">
                        <?=  $form->field($model, 'fecha_desde')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true,
                               'orientation' => 'bottom']
                            ])
                       ?>
                       <?=  $form->field($model, 'fecha_hasta')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true,
                               'orientation' => 'bottom']
                           ])
                       ?>
                </div>
                <div class="row">
                    <?=  $form->field($model, 'fecha_aplicacion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true,
                               'orientation' => 'bottom']
                        ])
                       ?>
                    <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
                </div>
                 <div class="row" col>
                
                <div class="panel panel-success">
                    <div class="panel-heading">
                        Seleccione la configuración..
                    </div>
                    <div class="panel-body">
                        <div class="checkbox checkbox-success" align ="left">
                          <?= $form->field($model, 'afecta_transporte')->checkBox(['label' => 'Afecta transporte','' =>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'afecta_transporte']) ?>
                            <?= $form->field($model, 'cobrar_administradora')->checkBox(['label' => 'Cobrar administradora',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'cobrar_administradora']) ?>
                            <?= $form->field($model, 'aplicar_adicional')->checkBox(['label' => 'Aplicar adicional',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplicar_adicional']) ?>
                            <?= $form->field($model, 'pagar_empleado')->checkbox(['label' => 'Pagar empleado',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'pagar_empleado']) ?>
                            <?= $form->field($model, 'pagar_parafiscal')->checkbox(['label' => 'Pagar parafiscal',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'pagar_parafiscal']) ?>
                            <?= $form->field($model, 'pagar_arl')->checkbox(['label' => 'Pagar Arl',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'pagar_arl']) ?>
                        </div>
                </div>   
                <div class="panel-footer text-right">			
                    <a href="<?= Url::toRoute("licencia/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
                </div>
            </div>
        </div>
<?php $form->end() ?>     


