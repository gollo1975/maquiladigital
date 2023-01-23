<?php
//modelos
use app\models\Ordenproducciondetalle;
use app\models\Ordenproducciondetalleproceso;
//clases
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
$modeldetalles = Ordenproducciondetalle::find()->Where(['=', 'idordenproduccion', $idordenproduccion])->all();
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
                    Entrada de prendas terminadas: 
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?= $form->field($model, 'cantidad_terminada')->textInput(['maxlength' => true])  ?>
                    </div>   
                    <div class="row">
                        <?= $form->field($model, 'fecha_entrada')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                              'value' => date('d-M-Y', strtotime('+2 days')),
                              'options' => ['placeholder' => 'Seleccione una fecha ...'],
                              'pluginOptions' => [
                                  'format' => 'yyyy-m-d',
                                  'todayHighlight' => true]])
                        ?>
                    </div>    
                     <div class="row">
                        <?= $form->field($model, 'nro_operarios')->textInput(['maxlength' => true])  ?>
                    </div>    
                    <div class="row">
                        <?= $form->field($model, 'hora_corte_entrada')->input('time'); ?>
                    </div>
                    <div class="row" col>
                        <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-6 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
                    </div>
                    
                       
                </div>                
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                           <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                           <th scope="col" style='background-color:#B9D5CE;'>Producto / Talla</th>
                           <th scope="col" style='background-color:#B9D5CE;'>Unidades x Talla</th>
                           <th scope="col" style='background-color:#B9D5CE;'>Unidades confeccionadas</th>
                          <th scope="col" style='background-color:#B9D5CE;'></th>
                        </tr>
                        
                    </thead>
                    <tbody>
                        <?php
                        foreach ($modeldetalles as $val): ?>
                        <tr >
                            <td><?= $val->iddetalleorden ?></td>
                            <td><?= $val->productodetalle->prendatipo->prenda.' / '.$val->productodetalle->prendatipo->talla->talla ?></td>
                            <td align="center"><?= $val->cantidad ?></td>
                            <td style="font-size: 100%; width: 170px; text-align: center; background: #C7F1DB; color: #005F80;"><?= $val->faltante ?></td>
                            <?php if($val->faltante < $val->cantidad){?>
                               <td style="width: 30px;"><input type="checkbox" name="id_detalle_orden[]" value="<?= $val->iddetalleorden ?>"></td>
                            <?php }else{?>
                               <td style="width: 30px;"><input type="checkbox" disabled="true" name="id_detalle_orden[]" value="<?= $val->iddetalleorden ?>"></td>
                            <?php } ?>   
                        </tr>
                        <?php
                        endforeach; ?>
                   </tbody>     
                </table>
                <div class="panel-footer text-right">			
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar cantidad", ["class" => "btn btn-primary", 'name' => 'enviarcantidad']) ?>                    
                </div>
            </div>    
        </div>
    </div>
<?php $form->end() ?> 

<script type="text/javascript">
	function marcar(source) 
	{
		checkboxes=document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
		for(i=0;i<checkboxes.length;i++) //recoremos todos los controles
		{
			if(checkboxes[i].type == "checkbox") //solo si es un checkbox entramos
			{
				checkboxes[i].checked=source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
			}
		}
	}
</script>