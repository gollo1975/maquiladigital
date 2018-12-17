<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Arl;

/* @var $this yii\web\View */
/* @var $model app\models\Resumencostos */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Resumen Costos';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php
$form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
        ]);
?>
<?php
$arl = ArrayHelper::map(Arl::find()->all(), 'id_arl', 'arl');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Resumen Costos
    </div>
    <div class="panel-body">
        
        
        <div class="row">
            <?= $form->field($model, 'valor_dia')->textInput(['maxlength' => true, 'readonly' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'valor_hora')->textInput(['maxlength' => true, 'readonly' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'valor_minuto')->textInput(['maxlength' => true, 'readonly' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'valor_segundo')->textInput(['maxlength' => true, 'readonly' => true]) ?>    
        </div>                
        <div class="panel-footer text-right">			                        
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-success",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
