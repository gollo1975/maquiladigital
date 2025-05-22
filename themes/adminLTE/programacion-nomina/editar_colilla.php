<?php
//modelos

//clases
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
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
            'template' => '{label}<div class="col-sm-8 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-4 control-label'],
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
                <div class="panel-heading" style="text-align: left ">
                  EDITAR CONCEPTOS
                </div>
                <div class="panel-body">
                     
                    <?php if($table->codigoSalario->devengado_deduccion == 1) {?> 
                        <div class="row">
                          <?= $form->field($model, 'devengado')->textInput(['maxlength' => true]) ?>
                        </div> 
                    <?php }else{?>
                        <div class="row">
                           <?= $form->field($model, 'deduccion')->textInput(['maxlength' => true]) ?>
                       </div>
                    <?php }?>
                </div>  
                    <div class="panel-footer text-right">
                       <?= Html::submitButton("<span class='glyphicon glyphicon-eye-close'></span> Cerrar", ["class" => "btn btn-success", 'name' => 'cerrar_ventana']) ?> 
                       <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-primary", 'name' => 'actualizar_conceptos']) ?>                    
                                            
                   </div>
                
            </div>
           
        </div>
    </div>
<?php $form->end() ?> 
