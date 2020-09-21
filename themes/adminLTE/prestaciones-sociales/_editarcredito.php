<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

?>


<?php $form = ActiveForm::begin([
            "method" => "post",
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
        <div class="table table-responsive">
            <div class="panel panel-success ">
                <div class="panel-heading">
                    Deduccion a credito
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?= $form->field($model, 'id_credito')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>
                    </div>    
                    <div class="row">
                       <?= $form->field($model, 'deduccion')->textInput(['maxlength' => true]) ?>                  
                    </div>    
                   
                </div>                
                <div class="panel-footer text-right">			
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-primary btn-sm", 'name' => 'actualizar']) ?>                    
                </div>
            </div>
        </div>
    </div>
<?php $form->end() ?> 