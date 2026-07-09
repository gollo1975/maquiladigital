<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Contrato;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;


$grupos = ArrayHelper::map(app\models\GrupoPago::find()->all(), 'id_grupo_pago', 'grupo_pago');
?>


<?php
$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
            'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
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
                   Acumulado de prestaciones 
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?= $form->field($model, 'id_contrato')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>
                         <?= $form->field($model, 'ibp_prima_inicial')->textInput(['maxlength' => true]) ?>  	
                    </div>    
                    <div class="row">
                         <?= $form->field($model, 'ibp_cesantia_inicial')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'ibp_recargo_nocturno')->textInput(['maxlength' => true]) ?>                 
                    </div>    
                </div>        
            </div>   
            <div class="panel panel-success ">
                <div class="panel-heading">
                    Ultimos pagos
                </div>
                <div class="panel-body"> 
                    <div class="row">
                        <?=
                        $form->field($model, 'ultima_prima')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                            'value' => date('d-M-Y', strtotime('+2 days')),
                            'options' => ['placeholder' => 'Seleccione una fecha ...'],
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'todayHighlight' => true]])
                        ?>  
                         <?=
                        $form->field($model, 'ultima_cesantia')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                            'value' => date('d-M-Y', strtotime('+2 days')),
                            'options' => ['placeholder' => 'Seleccione una fecha ...'],
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'todayHighlight' => true]])
                        ?>                 
                    </div>  
                     <div class="row">
                        <?=
                        $form->field($model, 'ultima_vacacion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                            'value' => date('d-M-Y', strtotime('+2 days')),
                            'options' => ['placeholder' => 'Seleccione una fecha ...'],
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'todayHighlight' => true]])
                        ?>  
                         <?=
                        $form->field($model, 'ultimo_pago')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                            'value' => date('d-M-Y', strtotime('+2 days')),
                            'options' => ['placeholder' => 'Seleccione una fecha ...'],
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'todayHighlight' => true]])
                        ?>                 
                    </div>  
                </div> 
            </div>   
             <div class="panel panel-success ">
                <div class="panel-heading">
                    Fechas del contrato
                </div>
                <div class="panel-body"> 
                    <div class="row">
                         <?= $form->field($model, 'fecha_inicio')->input('text', ['fecha_inicio', 'readonly' => TRUE, ['style' => 'width:15%']]) ?>
                        <?php if($model->fecha_final == '2099-12-31'){?>
                         <?= $form->field($model, 'fecha_final')->input('text', ['fecha_final', 'readonly' => TRUE, ['style' => 'width:15%']]) ?>
                        <?php }else{?>
                            <?=
                            $form->field($model, 'fecha_final')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                                'value' => date('d-M-Y', strtotime('+2 days')),
                                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true]])
                            ?>                 
                        <?php }?> 
                    </div>  
                    <div class="row">
                        <?= $form->field($model, 'id_grupo_pago')->dropdownList($grupos, ['prompt' => 'Seleccione...']) ?>           
                    </div>    
                </div> 
                 <div class="panel-footer text-right">			
                <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-primary", 'name' => 'actualizar']) ?>                    
            </div>
            </div>    
            
        </div>
    </div>
<?php $form->end() ?> 