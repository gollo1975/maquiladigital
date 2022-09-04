    <?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;

$this->title = 'Gestor documental';
$this->params['breadcrumbs'][] = $this->title;

?>


<?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form', 'enctype' => 'multipart/form-data'],
    "method" => "post",
     "enableClientValidation" => true,
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
]); ?>
    <div class="panel panel-success">
        <div class="panel-heading">
            Información Archivo a subir
        </div>
        <div class="panel-body">
            <div class="row">
                <?= $form->field($model, 'numero')->input("hidden") ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'codigo')->input("hidden") ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'view')->input("hidden") ?>
            </div>
            <div class="row">
              
                <label id="descripcion" for="descripcion" class="col-sm-3 control-label">Descripción:</label>
                <div class="col-sm-5 form-group">
                    <?= Html::textInput('descripcion', '', ['id' => 'descripcion', 'aria-required' => true, 'aria-invalid' => 'false', 'maxlength' => 120, 'class' => 'form-control', 'placeholder' => 'Archivos con extension pdf, docx, txt, png, docx', 'style' => 'width:65%', 'required' => true]) ?>                        
                </div>   
            </div>                
            <div class="row">
                <?= $form->field($model, 'file[]')->fileInput(['multiple' => true]) ?>
            </div>
            <div class="panel-footer text-right">                
                <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['archivodir/index', 'numero' => $model->numero,'codigo' => $model->codigo,'view' => $view], ['class' => 'btn btn-primary btn-sm']); ?>
                <?= Html::submitButton("<span class='glyphicon glyphicon-upload'></span> Subir Archivo", ["class" => "btn btn-success btn-sm",]) ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end() ?>