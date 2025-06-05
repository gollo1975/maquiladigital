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
                   Seguridad social 
                </div>
                <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <tr style="font-size: 85%;">
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($modelo, 'id_entidad_pension') ?></th>
                        <td><?= Html::encode($modelo->entidadPension->entidad) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($modelo, 'id_entidad_salud') ?></th>
                        <td><?= Html::encode($modelo->entidadSalud->entidad) ?></td>
                     </tr>  
                     <tr style="font-size: 85%;">
                     
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($modelo, 'Cesantias') ?></th>
                        <td><?= Html::encode($modelo->cesantia->cesantia) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($modelo, 'id_arl') ?></th>
                        <td><?= Html::encode($modelo->arl->arl) ?></td>
                     </tr>    
                </table>     
            </div>   
            <div class="panel panel-success ">
                <div class="panel-heading">
                    Prestaciones sociales
                </div>
                <div class="panel-body"> 
                    <table class="table table-bordered table-striped table-hover">
                        <tr style="font-size: 85%;">
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($modelo, 'ultimo_pago_nomina') ?></th>
                        <td><?= Html::encode($modelo->ultimo_pago) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($modelo, 'ultima_prima') ?></th>
                        <td><?= Html::encode($modelo->ultima_prima) ?></td>
                     </tr>  
                     <tr style="font-size: 85%;">
                     ultima_cesantia
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($modelo, 'ultima_cesantia') ?></th>
                        <td><?= Html::encode($modelo->ultima_cesantia) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($modelo, 'ultima_vacacion') ?></th>
                        <td><?= Html::encode($modelo->ultima_vacacion) ?></td>
                     </tr>    
                    </table>
                </div> 
            </div>   
        </div>
    </div>
<?php $form->end() ?> 