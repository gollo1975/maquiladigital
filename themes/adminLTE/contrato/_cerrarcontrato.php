<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Contrato;
use app\models\MotivoTerminacion;
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
    <?php
$motivos = ArrayHelper::map(MotivoTerminacion::find()->all(), 'id_motivo_terminacion', 'motivo');
?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
    </div>
    <div class="modal-body">        
        <div class="table table-responsive">
            <div class="panel panel-success ">
                <div class="panel-heading">
                    Terminacion del contrato de trabajo 
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?= $form->field($model, 'id_contrato')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>
                    </div>    
                    <div class="row">
                        <?= $form->field($model, 'id_motivo_terminacion')->dropDownList($motivos, ['prompt' => 'Seleccione...']) ?>                    
                    </div>    
                    <div class="row">
                        <?=
                        $form->field($model, 'fecha_final')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                            'value' => date('d-M-Y', strtotime('+2 days')),
                            'options' => ['placeholder' => 'Seleccione una fecha ...'],
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'todayHighlight' => true]])
                        ?>                        
                    </div>  
                    <div class="row" col>
                        <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-8 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
                    </div>
                   <div class="checkbox checkbox-success" align ="center">  
                        <?= $form->field($model, 'generar_liquidacion')->checkBox(['name'=>'generar_liquidacion[]', 'checked'=>'true', 'label' => 'Genera liquidación',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'generar_liquidacion']) ?> 
                    </div>     
                </div>                
                <div class="panel-footer text-right">			
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-primary", 'name' => 'actualizar']) ?>                    
                </div>
            </div>
        </div>
    </div>
<?php $form->end() ?> 