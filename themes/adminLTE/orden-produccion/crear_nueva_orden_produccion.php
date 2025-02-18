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
$tipoServicio = ArrayHelper::map(\app\models\Ordenproducciontipo::find()->all(), 'idtipo','tipo');
?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
    </div>
    <div class="modal-body">        
        <div class="table table-responsive">
            <div class="panel panel-success ">
                <div class="panel-heading" style="text-align: left ">
                   Crear nueva OP
                </div>
                <div class="panel-body">
                   <div class="row">
                        <?= $form->field($model, 'tipo_servicio')->dropdownList($tipoServicio, ['prompt' => 'Seleccione...', 'required' => true]) ?>
                    </div>    
                    <div class="row">
                      <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-8 form-group">{input}{error}</div>'])->textarea(['rows' => 3, 'maxlength' => true, 'required' => true]) ?>
                    </div>    
                    </div>  
                    <div class="panel-footer text-right">
                       <?= Html::submitButton("<span class='glyphicon glyphicon-send'></span> Crear", ["class" => "btn btn-primary", 'name' => 'nueva_orden_produccion']) ?>                    
                   </div>
            </div>
           
        </div>
    </div>
<?php $form->end() ?> 

