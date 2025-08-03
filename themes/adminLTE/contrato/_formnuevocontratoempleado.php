<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Municipio;
use app\models\Departamento;
use app\models\Empleado;
use app\models\Contrato;
use app\models\TipoCargo;
use app\models\TipoContrato;
use app\models\TipoCotizante;
use app\models\SubtipoCotizante;
use app\models\Cesantia;
use app\models\CajaCompensacion;
use app\models\Arl;
use app\models\CentroTrabajo;
use app\models\TiempoServicio;
use app\models\GrupoPago;
use app\models\Cargo;
use app\models\EntidadPension;
use app\models\EntidadSalud;
use app\models\ConfiguracionEps;
use app\models\ConfiguracionPension;
use kartik\date\DatePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Contrato */
/* @var $form yii\widgets\ActiveForm */
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
$ciudadLaboral = ArrayHelper::map(Municipio::find()->orderBy('municipio ASC')->all(), 'idmunicipio', 'municipio');
$ciudadContratado = ArrayHelper::map(Municipio::find()->orderBy('municipio ASC')->all(), 'idmunicipio', 'municipio');
$entidadSalud = ArrayHelper::map(EntidadSalud::find()->orderBy('entidad ASC')->all(), 'id_entidad_salud', 'entidad');
$entidadPension = ArrayHelper::map(EntidadPension::find()->orderBy('entidad ASC')->all(), 'id_entidad_pension', 'entidad');
$arl = ArrayHelper::map(Arl::find()->all(), 'id_arl', 'arl');
$cargo = ArrayHelper::map(Cargo::find()->orderBy('cargo ASC')->all(), 'id_cargo', 'cargo');
$tipocotizante = ArrayHelper::map(TipoCotizante::find()->all(), 'id_tipo_cotizante', 'tipo');
$subtipocotizante = ArrayHelper::map(SubtipoCotizante::find()->all(), 'id_subtipo_cotizante', 'subtipo');
$caja = ArrayHelper::map(CajaCompensacion::find()->orderBy('caja ASC')->all(), 'id_caja_compensacion', 'caja');
$cesantia = ArrayHelper::map(Cesantia::find()->orderBy('cesantia ASC')->all(), 'id_cesantia', 'cesantia');
$centroTrabajo = ArrayHelper::map(CentroTrabajo::find()->all(), 'id_centro_trabajo', 'centro_trabajo');
$grupopago = ArrayHelper::map(GrupoPago::find()->orderBy('grupo_pago ASC')->all(), 'id_grupo_pago', 'grupo_pago');
$tipocontrato = ArrayHelper::map(TipoContrato::find()->all(), 'id_tipo_contrato', 'contrato');
$tiempo = ArrayHelper::map(TiempoServicio::find()->all(), 'id_tiempo', 'tiempo_servicio');
$eps = ArrayHelper::map(ConfiguracionEps::find()->all(), 'id_eps', 'concepto_eps');
$pension = ArrayHelper::map(ConfiguracionPension::find()->all(), 'id_pension', 'concepto');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Información Contrato
    </div>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'id_tipo_contrato')->dropDownList($tipocontrato, ['prompt' => 'Seleccione el contrato', 'onchange' => 'tipocontrato()' ,'id' => 'id_tipo_contrato']) ?>
            <?= $form->field($model, 'id_tiempo')->dropDownList($tiempo, ['prompt' => 'Seleccione el servicio...']) ?>
        </div>        
        <div class="row">
            <?=
            $form->field($model, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']
                ])
            ?>    
            <?=
            $form->field($model, 'fecha_final')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['id' => 'fecha_final','placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']
                ])
            ?>
        </div>                        
        <div class="row">
            <?= $form->field($model, 'id_cargo')->widget(Select2::classname(), [
            'data' => $cargo,
            'options' => ['placeholder' => 'Seleccione una opción..'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
            <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>
        </div>        
        <div class="row">
            <?= $form->field($model, 'funciones_especificas', ['template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>'])->textarea(['rows' => 3]) ?>
            <?= $form->field($model, 'id_centro_trabajo')->dropDownList($centroTrabajo, ['prompt' => 'Seleccione una opcion...']) ?>
            <?= $form->field($model, 'id_grupo_pago')->dropDownList($grupopago, ['prompt' => 'Seleccione una opcion...']) ?>
        </div>        
        <div class="row">
            <?= $form->field($model, 'tipo_salario')->dropDownList(['FIJO' => 'FIJO', 'VARIABLE' => 'VARIABLE','INTEGRAL' => 'INTEGRAL'], ['prompt' => 'Seleccione una opcion...']) ?>    
            <?= $form->field($model, 'salario')->textInput(['maxlength' => true]) ?>
        </div>                
        <div class="row">
            <?= $form->field($model, 'ciudad_laboral')->widget(Select2::classname(), [
            'data' => $ciudadLaboral,
            'options' => ['placeholder' => 'Seleccione una opción..'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
            <?= $form->field($model, 'ciudad_contratado')->widget(Select2::classname(), [
            'data' => $ciudadContratado,
            'options' => ['placeholder' => 'Seleccione una opción..'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'auxilio_transporte')->dropDownList(['0' => 'NO', '1' => 'SI'], ['prompt' => 'Seleccione una opcion...']) ?>    
            <?= $form->field($model, 'horario_trabajo')->textInput(['maxlength' => true]) ?>
        </div>              
        <div class="row" col>
            <?= $form->field($model, 'comentarios', ['template' => '{label}<div class="col-sm-10 form-group">{input}{error}</div>'])->textarea(['rows' => 3]) ?>
        </div>        
    </div>
</div>

<div class="panel panel-success">
    <div class="panel-heading">
        Información Seguridad Social
    </div>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'id_tipo_cotizante')->dropDownList($tipocotizante, ['prompt' => 'Seleccione una opcion...']) ?>
            <?= $form->field($model, 'id_subtipo_cotizante')->dropDownList($subtipocotizante, ['prompt' => 'Seleccione una opcion...']) ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'id_eps')->dropDownList($eps, ['prompt' => 'Seleccione tipo salud']) ?>
            <?= $form->field($model, 'id_entidad_salud')->widget(Select2::classname(), [
            'data' => $entidadSalud,
            'options' => ['placeholder' => 'Seleccione una opción..'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
        </div>        
        <div class="row">
             <?= $form->field($model, 'id_pension')->dropDownList($pension, ['prompt' => 'Seleccione tipo pension...']) ?>
            <?= $form->field($model, 'id_entidad_pension')->widget(Select2::classname(), [
            'data' => $entidadPension,
            'options' => ['placeholder' => 'Seleccione una opción..'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
            </div>        
        <div class="row">
            <?= $form->field($model, 'id_caja_compensacion')->widget(Select2::classname(), [
            'data' => $caja,
            'options' => ['placeholder' => 'Seleccione una opción..'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
            <?= $form->field($model, 'id_cesantia')->dropDownList($cesantia, ['prompt' => 'Seleccione una opcion...']) ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'id_arl')->dropDownList($arl, ['prompt' => 'Seleccione una opcion...']) ?>            
        </div>         
        <div class="panel-footer text-right">			
            <a href="<?= Url::toRoute("contrato/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>
        </div>
    </div>
<?php $form->end() ?>     

