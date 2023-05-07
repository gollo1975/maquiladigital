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
    <div class="table table-responsive" style="width: 900px;">
        <div class="panel panel-success ">
            <div class="panel-heading">
                Proveedor
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover" style="width: 900px;">
                    <thead>
                        <tr>
                           <th scope="col" style='background-color:#B9D5CE;'>Nit</th>
                           <th scope="col" style='background-color:#B9D5CE;'>Proveedor</th>
                           <th scope="col" style='background-color:#B9D5CE;'>Fecha_asignacion</th>
                           <th scope="col" style='background-color:#B9D5CE;'>Proceso</th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php $table = \app\models\AsignacionProducto::findOne($detalle->id_asignacion);?>
                        <tr>
                            <td> <?= $table->documento ?></td>
                            <td><?= $table->razon_social?></td>
                            <td><?= $table->fecha_asignacion?></td>
                            <td><?= $table->tipo->tipo?></td>
                        </tr>
                   </tbody>     
                </table>
            </div>   
           
        </div>    
    </div>
</div>     
<?php $form->end() ?> 

