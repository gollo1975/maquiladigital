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
$documentos = ArrayHelper::map(app\models\ConceptoDocumentoSoporte::find()->orderBy('concepto ASC')->all(), 'id_concepto', 'concepto');
$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
            'template' => '{label}<div class="col-sm-10 form-group">{input}{error}</div>',
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
        <div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#listado_operarias" aria-controls="listado_operarias" role="tab" data-toggle="tab">Listado de operarios <span class="badge"><?= count($model) ?></span></a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="listado_operarias">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Operacion</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Eficiencia</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                             <div class="panel-footer text-right">
                                <?= Html::submitButton("<span class='glyphicon glyphicon-send'></span> Crear documento", ["class" => "btn btn-primary", 'name' => 'actualizar_concepto']) ?>                    
                            </div>
                        </div>
                    </div>    
                </div>  
                <!--termina el tabs-->
            </div>
        </div>
    </div>
<?php $form->end() ?> 

