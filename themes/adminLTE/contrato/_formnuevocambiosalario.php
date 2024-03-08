<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\FormatoContenido;
use kartik\date\DatePicker;
use kartik\select2\Select2;

$this->title = 'Cambio de salario';
$this->params['breadcrumbs'][] = ['label' => 'Cambio de salario', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
        'fieldConfig' => [
            'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-4 control-label'],
            'options' => []
        ],
    ]);
$contenido = ArrayHelper::map(FormatoContenido::find()->where(['=','id_configuracion_prefijo', 4])->orderBy('nombre_formato ASC')->all(), 'id_formato_contenido', 'nombre_formato');
?>
        <div class="panel panel-success">
            <div class="panel-heading">
                Información: Cambio salario
            </div>
            <div class="panel-body">
                <div class="row">
                     <?= $form->field($model, 'nuevo_salario')->textInput(['maxlength' => true]) ?>
                      <?=  $form->field($model, 'fecha_aplicacion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true]])
                       ?>
                       <?= $form->field($model, 'id_formato_contenido')->widget(Select2::classname(), [
                        'data' => $contenido,
                        'options' => ['placeholder' => 'Seleccione el formato'],
                        'pluginOptions' => [
                            'allowClear' => true ]]);
                       ?>
                </div>
                <div class="row">
                     <?= $form->field($model, 'observacion')->textarea(['maxlength' => true]) ?>
                </div>
                <div class="panel-footer text-right">			
                    <a href="<?= Url::toRoute(['contrato/view', 'id' => $id, 'token' => $token]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
                </div>
            </div>
        </div>
<?php $form->end() ?>     
