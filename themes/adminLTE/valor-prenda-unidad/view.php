<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Session;
use yii\db\ActiveQuery;
//modelos
use app\models\Operarios;
use app\models\Ordenproduccion;

/* @var $this yii\web\View */
/* @var $model app\models\ConfiguracionSalario */

$this->title = 'Editar';
$this->params['breadcrumbs'][] = ['label' => 'Valor prenda', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_valor;
$this->params['breadcrumbs'][] = $this->title;
$operarios = ArrayHelper::map(Operarios::find()->where(['=','estado', 1])->andWhere(['=','id_planta', $model->id_planta])->orderBy('nombrecompleto asc')->all(), 'id_operario', 'nombrecompleto');
$ordenproduccion = ArrayHelper::map(Ordenproduccion::find()->where(['=','pagada', 0])->orderBy('idordenproduccion desc')->all(), 'idordenproduccion', 'idordenproduccion');

?> 
<div class="valor-prenda-unidad-view">

    <!--<?= Html::encode($this->title) ?>-->

   <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->id_valor], ['class' => 'btn btn-primary btn-sm']) ?>
	<?php if ($model->autorizado == 0) { ?>
                <?= Html::a('<span class="glyphicon glyphicon-ok"></span> autorizado', ['autorizado', 'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion], ['class' => 'btn btn-success btn-sm']);
        } else { 
             if ($model->cerrar_pago == 0) { 
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion], ['class' => 'btn btn-default btn-sm']);
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar pago', ['cerrarpago', 'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion],['class' => 'btn btn-warning btn-xs',
                'data' => ['confirm' => 'Esta seguro de cerrar el proceso de pago Nro : '. $model->id_valor. '', 'method' => 'post']]);
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar pago-Orden', ['cerrarpagoorden', 'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion],['class' => 'btn btn-info btn-xs',
                'data' => ['confirm' => 'Esta seguro de cerrar el proceso de pago Nro : '. $model->id_valor. ' y la orden de producción Nro: '.$model->idordenproduccion.'', 'method' => 'post']]);
             }    
        }?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle del registro  
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?>:</th>
                    <td><?= Html::encode($model->id_valor) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_Orden') ?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion->cliente->nombrecorto) ?></td>
                       <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Activo') ?>:</th>
                    <td><?= Html::encode($model->estadovalor) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Vr_Vinculado') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($model->vlr_vinculado,0)) ?></td>
                   
                </tr>                
                 <tr style="font-size: 85%;">
                
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'F._proceso') ?>:</th>
                    <td><?= Html::encode($model->fecha_proceso) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Servicio') ?>:</th>
                    <td ><?= Html::encode($model->tipo->tipo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario_creador') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Autorizado') ?>:</th>
                    <td><?= Html::encode($model->autorizadoPago) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Vr_contrato') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($model->vlr_contrato,0)) ?></td>
                </tr>   
                 <tr style="font-size: 85%;">
                 
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'F._Editado') ?>:</th>
                    <td><?= Html::encode($model->fecha_editado) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario_editado') ?>:</th>
                    <td><?= Html::encode($model->usuario_editado) ?></td>
                       <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Pago_Cerrado') ?>:</th>
                    <td><?= Html::encode($model->cerradoPago) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Total_Ajuste') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($model->total_ajuste,0)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_confeccion') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($model->total_confeccion,0)) ?></td>
                 </tr>  
                 <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'> <?= Html::activeLabel($model, 'unidades') ?>:</th>
                    <td align="right"><?= Html::encode(''.number_format($model->cantidad,0)) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cantidad_operacion') ?>:</th>
                   <td align="right"><?= Html::encode(''.number_format($model->cantidad_operacion,0)) ?></td>
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cantidad_procesada') ?>:</th>
                   <td align="right"><?= Html::encode(''.number_format($model->cantidad_procesada,0)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_operacion') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($model->total_operacion,0)) ?></td>   
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_pagar') ?>:</th>
                   <td align="right"><?= Html::encode('$'.number_format($model->total_pagar,0)) ?></td>
                 </tr>   
                
            </table>
        </div>
    </div>
</div>   
<?php

$form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
        ]);
?>
    <!--INICIO LOS TABS-->

     <div class="panel-footer text-right">
       
        <?= Html::a('<span class="glyphicon glyphicon-export"></span> Exportar excel', ['generarexcel', 'id' => $model->id_valor], ['class' => 'btn btn-default btn-sm ']); ?>
        <?php if($model->autorizado == 0){?>                
                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nueva-Linea', ['valor-prenda-unidad/nuevodetalle', 'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion], ['class' => 'btn btn-success btn-sm']); ?>   
                <?php if($model->id_proceso_confeccion == 1){?>
                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nuevo-Modular', ['valor-prenda-unidad/nuevodetallemodular', 'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion], ['class' => 'btn btn-info btn-sm']); ?>        
                <?php }?>         
                <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-primary btn-sm, 'name' => 'actualizarlinea'",]) ?>
                <?php if(app\models\Matriculaempresa::find()->where(['=','aplica_regla', 1])->one()){?>
                   <?= Html::submitButton("<span class='glyphicon glyphicon-check'></span> Aplicar regla", ["class" => "btn btn-warning btn-sm", 'name' => 'aplicaregla']) ?>
               <?php } 
         }?>
    </div>
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#pago" aria-controls="pago" role="tab" data-toggle="tab">Detalle de pago: <span class="badge"><?= count($detalles_pago) ?></span></a></li>
       </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="pago">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE; width: 290px;'>Operario</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;width: 75px;'>Valide</th> 
                                        <th scope="col" style='background-color:#B9D5CE;width: 75px;'>Sabado</th> 
                                         <th scope="col" style='background-color:#B9D5CE;width: 150px;'>Operación</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Dia</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cant.</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Valor</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Vr.Pagar</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Costo</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>Meta</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>% Cum.</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Observación</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'></th> 
                                         <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $conMatricula = \app\models\Matriculaempresa::findOne(1);
                                           foreach ($detalles_pago as $val):?>
                                               <tr style='font-size: 85%;'> 
                                                    <td style="padding-left: 1;padding-right: 0;"><?= Html::dropDownList('id_operario[]', $val->id_operario, $operarios, ['class' => 'col-sm-10', 'prompt' => 'Seleccion...', 'required' => true]) ?></td>
                                                     <td style="padding-left: 1;padding-right: 0;"><select name="control_fecha[]">
                                                            <?php if ($val->control_fecha == 0){
                                                                   echo $control_fecha = "NO";
                                                                  }else{
                                                                    echo $control_fecha ="SI";
                                                                  }?>      
                                                            <option value="<?= $val->control_fecha?>"><?= $control_fecha ?></option>
                                                            <option value="0">NO</option>
                                                            <option value="1">SI</option>
                                                    </select></td>  
                                                    <td style="padding-left: 1;padding-right: 0;"><select name="aplica_sabado[]">
                                                            <?php if ($val->aplica_sabado == 0){
                                                                   echo $aplica_sabado = "NO";
                                                                  }else{
                                                                    echo $aplica_sabado ="SI";
                                                                  }?>      
                                                            <option value="<?= $val->aplica_sabado?>"><?= $aplica_sabado ?></option>
                                                            <option value="0">NO</option>
                                                            <option value="1">SI</option>
                                                    </select></td>  
                                                    <td style="padding-left: 1;padding-right: 0;"><select name="operacion[]">
                                                            <?php if ($val->operacion == 0){
                                                                   echo $operacion = "Confeccion";
                                                                  }else{
                                                                      if($val->operacion == 1){
                                                                          echo $operacion ="Operacion";
                                                                      }else{    
                                                                          echo $operacion ="Ajuste";
                                                                      }
                                                                  }?>      
                                                            <option value="<?= $val->operacion ?>"><?= $operacion ?></option>
                                                            <option value="0">Confeccion</option>
                                                            <option value="1">Operacion</option>
                                                            <option value="2">Ajuste</option>
                                                    </select></td>
                                                    <td style="padding-left: 1;padding-right: 0;"><input type="date" name="dia_pago[]" value="<?= $val->dia_pago ?>" size="2" required></td>  
                                                    <td style="padding-left: 1;padding-right: 0;"><input type="text" name="cantidad[]" value="<?= $val->cantidad ?>" size="2" required></td>                        
                                                    <td style="padding-left: 1;padding-right: 0;"><input type="text" name="vlr_prenda[]" value="<?= $val->vlr_prenda ?>" size="4" ></td>                        
                                                    <td style="padding-left: 1;padding-right: 0;"><input type="text" name="vlr_pago[]" value="<?= $val->vlr_pago ?>" size="6"> </td>  
                                                    <td style="padding-left: 1;padding-right: 0; size: 3px"><?= ''.number_format($val->costo_dia_operaria,0)?> </td> 
                                                    <td style="padding-left: 3;padding-right: 0; size: 3px;"> <?= $val->meta_diaria ?> </td>
                                                    <?php if($val->porcentaje_cumplimiento > $conMatricula->porcentaje_empresa){?>
                                                         <td style="padding-left: 1;padding-right: 0;background-color:#45C2CC;"><input type="text" name="porcentaje_cumplimiento[]" value="<?= $val->porcentaje_cumplimiento ?>" size="3" readonly="true"> </td>  
                                                    <?php }else{?>
                                                         <td style="padding-left: 1;padding-right: 0;background-color:#F1C6AC;"><input type="text" name="porcentaje_cumplimiento[]" value="<?= $val->porcentaje_cumplimiento ?>" size="3" readonly="true"> </td>  
                                                    <?php } ?>     
                                                    <td style="padding-left: 1;padding-right: 0;"><input type="text" name="observacion[]" value="<?= $val->observacion ?>" size="16" ></td>  
                                                    <input type="hidden" name="detalle_pago_prenda[]" value="<?= $val->consecutivo ?>">
                                                    <?php if($model->autorizado == 0){?>        
                                                        <td>
                                                              <?php if ($model->estado_valor == 0){ ?>
                                                              <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['eliminar', 'id' => $model->id_valor, 'detalle' => $val->consecutivo, 'idordenproduccion' => $model->idordenproduccion], [
                                                                  'class' => '',
                                                                  'data' => [
                                                                      'confirm' => 'Esta seguro de eliminar el registro?',
                                                                      'method' => 'post',
                                                                  ],
                                                              ])
                                                              ?>
                                                              <?php } ?>
                                                          </td>
                                                    <?php }else{ ?>
                                                          <td></td>
                                                    <?php } 
                                                    if($val->aplica_regla == 0){?> 
                                                           <td><input type="checkbox" name="consecutivo[]" value="<?= $val->consecutivo ?>"></td>
                                                    <?php }else{?>       
                                                           <td style= 'width: 25px;'></td>
                                                    <?php }?>       
                                               </tr>     
                                   <?php   endforeach;?>        
                                </tbody>      
                            </table>
                        </div>
                    </div>   
                </div>
            </div>
            <!--INICIO EL OTRO TABS -->
        </div>
     <?php ActiveForm::end(); ?>
</div> 
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