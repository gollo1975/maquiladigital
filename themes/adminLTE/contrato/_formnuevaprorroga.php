<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\FormatoContenido;
use kartik\date\DatePicker;
use kartik\select2\Select2;

$this->title = 'Prorrogas';
$this->params['breadcrumbs'][] = ['label' => 'Prorrogas al contrato', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$contenido = ArrayHelper::map(FormatoContenido::find()->where(['ver_listado' => 1])->orderBy('nombre_formato ASC')->all(), 'id_formato_contenido', 'nombre_formato');
?>
<?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
        'fieldConfig' => [
            'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-4 control-label'],
            'options' => []
        ],
    ]);
?>
        <div class="panel panel-success">
            <div class="panel-heading">
                Información: Prorrogas al contrato
            </div>
            <div class="panel-body">
                <div class="row">
                      <?= $form->field($modeloprorroga, 'fecha_desde')->input('text', ['fecha_desde', 'readonly' => TRUE, ['style' => 'width:15%']]) ?> 
                </div>
                <div class="row">
                    <?= $form->field($modeloprorroga, 'fecha_ultima_contrato')->input('text', ['fecha_ultima_contrato', 'readonly' => TRUE, ['style' => 'width:15%']]) ?>
                </div>
                <div class="row">
                       <?= $form->field($modeloprorroga, 'id_formato_contenido')->widget(Select2::classname(), [
                        'data' => $contenido,
                        'options' => ['placeholder' => 'Seleccione el formato'],
                        'pluginOptions' => [
                            'allowClear' => true ]]);
                       ?>
                </div>
                <div class="row">
                   <?= $form->field($modeloprorroga, 'fecha_nueva_renovacion')->input('text', ['fecha_nueva_renovacion', 'readonly' => TRUE, ['style' => 'width:15%']]) ?>
                
                </div>    
                <div class="panel-footer text-right">			
                    <a href="<?= Url::toRoute(['contrato/view', 'id' => $id,'token' => $token]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Generar", ["class" => "btn btn-success btn-sm",]) ?>
                </div>
            </div>
        </div>
<?php $form->end() ?>     
