<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use app\models\Operarios;
use kartik\select2\Select2;
use app\models\ProcesoProduccion;
$balanceo = \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])
                                                      ->andWhere(['=','id_proceso_confeccion', 2])->one();

$balanceo1 = \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])
                                                      ->andWhere(['=','id_proceso_confeccion', 3])->one();
if ($balanceo){
   $detalle_balanceo = \app\models\BalanceoDetalle::find()->where(['=','id_balanceo', $balanceo->id_balanceo])
                                                       ->andWhere(['=','estado_operacion', 0])
                                                       ->orderBy('id_operario desc')->all();
}else{
   $detalle_balanceo = \app\models\BalanceoDetalle::find()->where(['=','id_balanceo', $balanceo1->id_balanceo])
                                                       ->andWhere(['=','estado_operacion', 0])
                                                       ->orderBy('id_operario desc')->all();  
}   
$empresa = app\models\Matriculaempresa::findOne(1);
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
                    Modulo de preparación <span class="badge"><?= count($detalle_balanceo)?></span>
                </div>
                <div class="panel-body">
                   <div class="panel-body">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                     <th scope="col" style='background-color:#B9D5CE;'>Operación</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Minutos</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Valor Pagar</th>
                                    <th scope="col" style='background-color:#B9D5CE;'></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $auxiliar = 0;
                                 foreach ($detalle_balanceo as $val):
                                     $auxiliar = $val->id_operario;
                                     $operario = Operarios::find()->where(['=','id_operario', $auxiliar])->orderBy('nombrecompleto ASC')->one();
                                     $proceso = ProcesoProduccion::find()->where(['=','idproceso', $val->id_proceso])->one();
                                     if($val->id_operario == $operario->id_operario){?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $val->id_operario ?></td>
                                            <td><?= $operario->nombrecompleto ?></td>
                                            <td><?= $proceso->proceso?></td>
                                            <td><?= $val->minutos?></td>
                                            <?php if($operario->vinculado == 0){?>
                                            <td style="text-align: right"><?= ''.number_format(($val->minutos * $empresa->vlr_minuto_contrato),0)?></td>
                                            <?php }else{?>
                                            <td style="text-align: right"><?= ''.number_format(($val->minutos * $empresa->vlr_minuto_vinculado),0)?></td>
                                            <?php }?>  
                                             <td align="center"><input type="checkbox"  name="idoperario[]" value="<?= $operario->id_operario ?>"></td>
                                           
                                        </tr> 
                                    <?php } 
                                endforeach; ?>
                                
                            </tbody>

                        </table>
                    </div>   
                </div>        
            </div>   
            
                 <div class="panel-footer text-right">
                   <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar", ["class" => "btn btn-primary", 'name' => 'validaroperario']) ?>                    
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