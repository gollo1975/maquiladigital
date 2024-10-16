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
                   Listado de horas de corte
                </div>
                <div class="panel-body">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr style='font-size:85%;'>
                                    <th scope="col" style='background-color:#B9D5CE;'>Id pago</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Op</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>Referencia</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>Hora inicio</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>Hora corte</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($model as $val):?>
                                    <tr style='font-size: 85%;'>
                                        <td><?= $val->id_valor?></td>
                                        <td><?= $val->idordenproduccion ?></td>
                                        <td><?= $val->ordenproduccion->codigoproducto ?></td>
                                        <td><?= $val->hora_inicio ?></td>
                                        <td><?= $val->hora_corte ?></td>
                                        <td><?= $val->fecha_proceso ?></td>
                                        
                                    </tr>    
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>     
                </div>
           
            </div>
        </div>
    </div>    
<?php $form->end() ?> 

