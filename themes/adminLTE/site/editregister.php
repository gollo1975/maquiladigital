<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;


/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Editar Usuario';
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['users']];
$this->params['breadcrumbs'][] = $this->title;
$planta = ArrayHelper::map(\app\models\PlantaEmpresa::find()->all(), 'id_planta', 'nombre_planta');
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

<div class="panel panel-success">
    <div class="panel-heading">
        Información Usuario
    </div>
    <div class="panel-body">        														   		
        <div class="row">
            <?= $form->field($model, "username")->input("text") ?>                
        </div>        
        <div class="row">            
            <?= $form->field($model, 'role')->dropdownList(['1' => 'USUARIO', '2' => 'ADMINISTRADOR'], ['prompt' => 'Seleccione el tipo de usuario para el sistema']) ?>
        </div>
        <div class="row">
            <?= $form->field($model, "emailusuario")->input("email") ?>           
        </div>
        <div class="row">            
            <?= $form->field($model, "nombrecompleto")->input("text") ?>               
        </div>
        <div class="row">
            <?= $form->field($model, "documentousuario")->input("text") ?>    
        </div>
        <div class="row">            
            <?= $form->field($model, 'id_planta')->widget(Select2::classname(), [
             'data' => $planta,
             'options' => ['placeholder' => 'Seleccione.... '],
             'pluginOptions' => [
             'allowClear' => true ]]);
            ?>
        </div>
        <div class="row">            
            <?= $form->field($model, 'activo')->dropdownList(['1' => 'ACTIVO', '0' => 'INACTIVO'], ['prompt' => 'Seleccione el estado del usuario']) ?>
        </div>
        <div class="panel-footer text-right">            
            <a href="<?= Url::toRoute("site/users") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>    
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>    