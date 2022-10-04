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
    <div class="table table-responsive">
        <div class="panel panel-success ">
            <div class="panel-heading">
                Proveedores <span class="badge"><?= 1 ?></span>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                           <th scope="col" style='background-color:#B9D5CE;'>Nit</th>
                           <th scope="col" style='background-color:#B9D5CE;'>Proveedor</th>
                           <th scope="col" style='background-color:#B9D5CE;'>Dirección</th>
                          <th scope="col" style='background-color:#B9D5CE;'>Fecha</th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        ?>
                   </tbody>     
                </table>
            </div>   
           
        </div>    
    </div>
</div>     
<?php $form->end() ?> 

