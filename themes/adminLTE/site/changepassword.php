<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Cambio Clave';
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['users']];
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

<div class="panel panel-success">
    <div class="panel-heading">
        Información Usuario
    </div>
    <div class="panel-body">        														   		        
        <div class="row">
            <?= $form->field($model, "password")->input("password") ?>                
        </div>
        <div class="row">
            <?= $form->field($model, "password_repeat")->input("password") ?>                
        </div>        
        <div class="panel-footer text-right">            
            <a href="<?= Url::toRoute("site/users") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>    
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>    