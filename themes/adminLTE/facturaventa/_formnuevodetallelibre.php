<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
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
        <div class="table table-responsive">
            <div class="panel panel-success ">
                <div class="panel-heading" align="left">
                    Nuevo detalle factura venta
                </div>
                <div class="panel-body">
                    <div class="row">                                    
                        <?= $form->field($model, 'idproducto')->dropDownList($productos,['prompt' => 'Seleccione un producto...']) ?>
                    </div>
                    <div class="row">            
                        <?= $form->field($model, 'valor')->textInput(['maxlength' => true]) ?>
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