<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\FormatoContenido;
use app\models\ConceptoSalarios;
use kartik\date\DatePicker;
use kartik\select2\Select2;

$this->title = 'Adición salarial';
$this->params['breadcrumbs'][] = ['label' => 'Adición salarial', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$contenido = ArrayHelper::map(FormatoContenido::find()->where(['=','id_configuracion_prefijo', 3])->orderBy('nombre_formato ASC')->all(), 'id_formato_contenido', 'nombre_formato');
$conceptosalario = ArrayHelper::map(ConceptoSalarios::find()->where(['tipo_adicion'=> 1])->andWhere(['=','prestacional', 0])->orderBy('nombre_concepto ASC')->all(), 'codigo_salario', 'nombre_concepto');
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
        <div class="panel panel-success">
            <div class="panel-heading">
                Información: Adicion al contrato
            </div>
            <div class="panel-body">
                <div class="row">
                       <?= $form->field($modeloadicion, 'id_formato_contenido')->widget(Select2::classname(), [
                        'data' => $contenido,
                        'options' => ['placeholder' => 'Seleccione el formato'],
                        'pluginOptions' => [
                        'allowClear' => true ]]);
                       ?>
                      <?= $form->field($modeloadicion, 'codigo_salario')->widget(Select2::classname(), [
                        'data' => $conceptosalario,
                        'options' => ['placeholder' => 'Seleccione el concepto'],
                        'pluginOptions' => [
                        'allowClear' => true ]]);
                       ?>
                </div>
                <div class="row">
                        <?=
                        $form->field($modeloadicion, 'fecha_aplicacion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                            'value' => date('d-M-Y', strtotime('+2 days')),
                            'options' => ['placeholder' => 'Seleccione una fecha ...'],
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'todayHighlight' => true]])
                        ?>  
                         <?= $form->field($modeloadicion, 'vlr_adicion')->textInput(['maxlength' => true]) ?>
                    </div>  
                <div class="row">
                       
                    </div>
                
                <div class="panel-footer text-right">			
                    <a href="<?= Url::toRoute(['contrato/view', 'id' => $id, 'token' => $token]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
                </div>
            </div>
        </div>
<?php $form->end() ?>     
