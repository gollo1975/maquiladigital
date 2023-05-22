<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
$colores = ArrayHelper::map(\app\models\Color::find()->orderBy('color ASC')->all(),'id','color');
?>


<?php $form = ActiveForm::begin([

    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
    </div>
    <div class="modal-body">        
        <div class="table table-responsive" style ="width: 630px;" >
            <div class="panel panel-success ">
                <div class="panel-heading">
                    Seleccione el color 
                </div>
                <div class="panel-body">
                    <div class="row">
                       <?= $form->field($model, 'color')->dropDownList($colores, ['prompt' => 'Seleccione un color...']) ?>
                   </div>    
                    <div class="panel-footer text-right">			
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Crear", ["class" => "btn btn-primary", 'name' => 'crearemision']) ?>                    
                   </div>
                </div>
            </div>
        </div>
    </div>    

<?php ActiveForm::end(); ?>
