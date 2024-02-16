<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Maestro operaciones (Consulta)';
$this->params['breadcrumbs'][] = ['label' => 'Maestro operaciones', 'url' => ['maestro_operaciones']];
?>
<?php $form = ActiveForm::begin([
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

<div class="panel panel-success">
    <div class="panel-heading">
        SELECCIONE EL PROCESO
    </div>
    <div class="panel-body">
        <div class="row">
             <?= $form->field($model, 'id_operario')->widget(Select2::classname(), [
            'data' => $operarios,
            'options' => ['placeholder' => 'Seleccione la orden'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>
            <?= $form->field($model, 'idordenproduccion')->dropDownList($orden,['prompt'=>'Seleccione la OP...', 'onchange'=>' $.get( "'.Url::toRoute('valor-prenda-unidad/llenar_tallas').'", { id: $(this).val() } ) .done(function( data ) {
            $( "#'.Html::getInputId($model, 'iddetalleorden',['required', 'class' => 'select-2']).'" ).html( data ); });']); ?>
            <?= $form->field($model, 'iddetalleorden')->dropDownList(['prompt' => 'Seleccione...']) ?>
        </div>
        
        <div class="panel-footer text-right">
                <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Search", ["class" => "btn btn-success btn-sm", 'name' => 'cambiar_posicion']) ?>     
            </div>  
    </div> 
</div>    

<div class="panel panel-success">
        <div class="panel-heading">
            LISTADO DE REGISTROS <span class="badge"> <?= count($modelo)?></span>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr style="font-size: 90%;">
                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Operacion</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Op</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Fecha</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Vr. unidad</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Total pago</th>
                        
                    </tr>
                </thead>
                <?php foreach ($modelo as $val):?>
                    <tr style="font-size: 90%;">
                        <td><?= $val->idproceso?></td>
                        <td><?= $val->operaciones->proceso?></td>
                        <td><?= $val->detalleOrdenProduccion->productodetalle->prendatipo->talla->talla ?></td>    
                        <td><?= $val->idordenproduccion?></td>
                        <td><?= $val->dia_pago?></td>
                        <td><?= $val->cantidad?></td>
                        <td><?= $val->vlr_prenda?></td>
                        <td style="text-align: right"><?= ''.number_format($val->vlr_pago,0)?></td>
                    </tr>
                    
                <?php endforeach;?>
            </table> 
              
        </div>
    </div>       
<?php $form->end() ?>

