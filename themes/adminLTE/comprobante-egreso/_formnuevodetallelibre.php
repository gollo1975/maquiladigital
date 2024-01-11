<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>


<?php $form = ActiveForm::begin([

    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-8 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-4 control-label'],
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
                <div class="panel-heading" align="left">
                    Nuevo detalle
                </div>
                <div class="panel-body">
                    <div class="row">            
                        <?= $form->field($model, 'vlr_abono')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="row">            
                        <?= $form->field($model, 'subtotal')->textInput(['maxlength' => true, 'value' => 0]) ?>
                    </div>
                    <div class="row">            
                        <?= $form->field($model, 'iva')->textInput(['maxlength' => true, 'value' => 0]) ?>
                    </div>
                    <div class="row">            
                        <?= $form->field($model, 'retefuente')->textInput(['maxlength' => true, 'value' => 0]) ?>
                    </div>
                    <div class="row">            
                        <?= $form->field($model, 'reteiva')->textInput(['maxlength' => true, 'value' => 0]) ?>
                    </div>
                    <div class="row">            
                        <?= $form->field($model, 'base_aiu')->textInput(['maxlength' => true, 'value' => 0]) ?>
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>                    
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-primary btn-sm", 'name' => 'Guardar']) ?>                    
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>