<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Ordenproduccion;
use app\models\Ordenproducciondetalle;
?>


<?php $form = ActiveForm::begin([

    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
    </div>
    <div class="modal-body">
        
        <div class="table table-responsive">
            <div class="panel panel-success ">
                <div class="panel-heading">
                    Lineas <span class="badge"><?= count($orden)?></span>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive-lg" >
                        <thead>
                            <tr style='font-size:100%;'>
                                <td scope="col" style='background-color:#B9D5CE; '><b>Id</td>
                                <td scope="col" style='background-color:#B9D5CE; '><b>Talla</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Cantidad</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Confección.</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Faltan</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Unidades</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Hora corte</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>No Operarios</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Alimento</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Observacion</td>
    
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $confeccionada = 0; $restar = 0;
                        $unidades = 0; $total_Confeccionada = 0; $unidades_Faltante = 0;
                        foreach ($orden as $val):
                            $unidades += $val->cantidad;
                             $detalle = app\models\CantidadPrendaTerminadas::find()->where(['=','iddetalleorden', $val->iddetalleorden])->all();
                             $confeccionada = 0;
                             if (count($detalle) > 0){
                                 foreach ($detalle as $detalles):
                                   $confeccionada +=$detalles->cantidad_terminada;    
                                 endforeach;
                             }
                             $restar = $val->cantidad - $confeccionada;
                             $total_Confeccionada += $confeccionada;
                             $unidades_Faltante += $restar; 
                            ?>
                            <tr style="font-size: 100%;">
                                <td><?= $val->iddetalleorden ?></td> 
                                <?php if($val->id_planta == $id_planta){?>
                                    
                                    <td style="background-color: #F5DB90"><?= $val->productodetalle->prendatipo->talla->talla ?></td>
                                <?php }else{?>
                                    <td><?= $val->productodetalle->prendatipo->talla->talla ?></td>
                                <?php } ?>    
                                <td style="text-align: right"><?= ''.number_format($val->cantidad, 0) ?></td>                                                              
                                <td style="text-align: right; background-color:#B9D5AA;"><?= ''.number_format($confeccionada, 0) ?></td> 
                                <td style="text-align: right; background-color:#B9D5CE; color: red;"><?= ''.number_format($restar, 0) ?></td> 
                                <?php if($restar > 0){?>
                                     <td style="padding-left: 1;padding-right: 0;"><input type="text" name="nueva_entrada[]" value="0" size="4" maxlength="4"></td>
                                     <td style="padding-left: 1;padding-right: 1;"><input type="time" name="hora_corte[]" value="" size="6"></td>
                                <?php }else{ ?>
                                     <td style="padding-left: 1;padding-right: 0;"><input type="text" name="nueva_entrada[]" readonly="true" value="0" size="4" maxlength="4"></td>
                                     <td style="padding-left: 1;padding-right: 1;"><input type="time" name="hora_corte[]" readonly="true" value="" size="6"></td>
                                <?php } ?>     
                                
                                <td style="padding-left: 1;padding-right: 0;"><input type="text" name="operarios[]" value="<?= $balanceo->cantidad_empleados?>" size="4" maxlength="4"></td>
                                <td style="padding-left: 1;padding-right: 0;"><select name="aplica_alimento[]">
                                    <option value="0">NO</option>
                                    <option value="1">SI</option>
                                </select></td>  
                                <td style="padding-left: 1;padding-right: 0;"><input type="text" name="observacion[]" value="" size="16" maxlength="18"></td>
                                <input type="hidden" name="entrada_diaria[]" value="<?= $val->iddetalleorden ?>">
                            </tr>
                            </tbody>
                            <?php
                        endforeach; ?>    
                            <tr>
                    <td colspan="1"></td>
                    <td align="right"><b>Totales</b></td>
                    <td align="right" ><b><?= ''.number_format($unidades,0); ?></b></td>
                    <td align="right" style="text-align: right; background-color:#B9D5AA;" ><b><?= ''.number_format($total_Confeccionada,0); ?></b></td>
                    <td align="right" style="text-align: right; background-color:#B9D5CE; color: red;"><b><?= ''.number_format($unidades_Faltante,0); ?></b></td>
                    <td colspan="4"></td>
                </tr>
                    </table>
                    <div class="panel-footer text-right">			
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar", ["class" => "btn btn-primary", 'name' => 'unidadespordia']) ?>                    
                   </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
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