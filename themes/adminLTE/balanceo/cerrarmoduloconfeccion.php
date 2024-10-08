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
            'labelOptions' => ['class' => 'col-sm-3 control-label'],
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
                   Cerrar modulo
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?= $form->field($model, 'fecha_cierre')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                              'value' => date('d-M-Y', strtotime('+2 days')),
                              'options' => ['placeholder' => 'Seleccione una fecha ...'],
                              'pluginOptions' => [
                                  'format' => 'yyyy-m-d',
                                  'todayHighlight' => true]])
                        ?>
                    </div>
                    <div class="row">
                          <?= $form->field($model, 'hora_cierre')->input('time'); ?>
                    </div>    
                     <div class="checkbox checkbox-success" align ="center"> 
                        <?= $form->field($model, 'option')->checkBox(['label' => 'Cerrar orden de producción',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'option']) ?> 
                    </div>  
                     <td style="background-color:#BFB66B;"><?php echo 'Favor chequear esta opción si no quedan mas módulos en la orden de produccion.'?></td>
                </div>        
            </div>   
            
                 <div class="panel-footer text-right">			
                <?= Html::submitButton("<span class='glyphicon glyphicon-eye-close'></span> Cerrar modulo", ["class" => "btn btn-primary", 'name' => 'cerrarmodulo']) ?>                    
            </div>
            </div>    
            
        </div>
    </div>
<?php $form->end() ?> 