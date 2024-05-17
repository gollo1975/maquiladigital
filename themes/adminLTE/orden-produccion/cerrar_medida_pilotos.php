<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Contrato;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
?>


<?php
$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
            'template' => '{label}<div class="col-sm-6 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-5 control-label'],
            'options' => []
        ],
        ]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
    </div>
    <div class="modal-body">        
        <div class="table table-responsive">
            <div class="panel panel-success ">
                <div class="panel-heading">
                   Cerrar medidas de pilotos
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?= $form->field($model, 'proceso_lavanderia')->dropDownList(['0'=> 'ABIERTO', '1'=> 'CERRADO'], ['prompt' => 'Seleccione ...']) ?>
                    </div>    
                     <div class="row"> 
                        <?= $form->field($model, 'proceso_sin_lavanderia')->dropDownList(['0'=> 'ABIERTO', '1'=> 'CERRADO'], ['prompt' => 'Seleccione...']) ?>
                    </div>  
                    
                </div>        
            </div>   
            
                 <div class="panel-footer text-right">			
                <?= Html::submitButton("<span class='glyphicon glyphicon-eye-close'></span> Cerrar proceso", ["class" => "btn btn-primary", 'name' => 'enviar_proceso']) ?>                    
            </div>
            </div>    
            
        </div>
    </div>
<?php $form->end() ?> 