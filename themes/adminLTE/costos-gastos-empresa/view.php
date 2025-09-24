<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\CostosGastosEmpresa */

$this->title = 'Detalle costo';
$this->params['breadcrumbs'][] = ['label' => 'Costos Gastos Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="costos-gastos-empresa-view">
    <?php if($model->autorizado == 0){?>
        <div class="btn-group" role="group" aria-label="...">
            <button type="button" class="btn btn-default btn"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'],['class' => 'btn btn-primary btn-xs']) ?></button>
           <button type="button" class="btn btn-default btn"> <?= Html::a('<span class="glyphicon glyphicon-send"></span> Generar compras', ['generarcompras','id' =>$model->id_costo_gasto],['class' => 'btn btn-default btn-xs']) ?></button> 
            <button type="button" class="btn btn-default btn"> <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizarcostos','id' =>$model->id_costo_gasto],['class' => 'btn btn-info btn-xs']) ?></button>
            
        </div>
    <?php }else{ ?>
        <div class="btn-group" role="group" aria-label="...">
              <button type="button" class="btn btn-default btn"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'],['class' => 'btn btn-primary btn-xs']) ?></button>
              <button type="button" class="btn btn-default btn"> <?= Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizarcostos','id' =>$model->id_costo_gasto],['class' => 'btn btn-default btn-xs']) ?></button>
        </div>
    <?php } ?>
    <div class="panel panel-success">
        <div class="panel-heading">
            Vista
        </div>
         <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_costo')?>:</th>
                    <td><?= Html::encode($model->id_costo_gasto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_inicio')?>:</th>
                    <td><?= Html::encode($model->fecha_inicio) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_corte')?>:</th>
                    <td><?= Html::encode($model->fecha_corte) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario')?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_proceso')?>:</th>
                    <td><?= Html::encode($model->fecha_proceso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Autorizado')?>:</th>
                    <td><?= Html::encode($model->autorizadoCosto) ?></td>
                </tr>
                  <tr style="font-size: 85%;">
                    <?php if($model->id_planta != null){?>  
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_planta')?>:</th>
                        <td><?= Html::encode($model->planta->nombre_planta) ?></td>
                    <?php }else{?>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_planta')?>:</th>
                        <td><?= Html::encode('PLANTA GENERAL') ?></td>
                    <?php }?>    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Observación')?>:</th>
                    <td colspan="6"><?= Html::encode($model->observacion) ?></td>
                </tr>
            </table>
         </div>     
    </div>
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
        'fieldConfig' => [
            'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-3 control-label'],
            'options' => []
        ],
        ]); ?>
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#nomina" aria-controls="nomina" role="tab" data-toggle="tab">Nómina <span class="badge"><?= 1 ?></span></a></li>
            <li role="presentation"><a href="#gastosfijos" aria-controls="gastosfijos" role="tab" data-toggle="tab">Gastos fijos <span class="badge"><?= count($servicios) ?></span></a></li>
            <li role="presentation"><a href="#prestacionservicio" aria-controls="prestacionservicio" role="tab" data-toggle="tab">Prestacion Servicio <span class="badge"><?= count($prestacionServicio) ?></span></a></li>
            <li role="presentation"><a href="#seguridadsocial" aria-controls="seguridadsocial" role="tab" data-toggle="tab">Seguridad social <span class="badge"><?= count($costoSeguridad) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="nomina">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                               <thead>
                                   <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>No</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Salarios</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cesantias</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Interes</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Prima</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Vacaciones</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Ajuste</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>F. proceso</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                   </tr>
                               </thead>
                               <tbody>
                                   <?php foreach ($costo_nomina as $val):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $val->id_detalle_nomina ?></td>
                                            <td align="right"><?= ''.number_format($val->salarios,0) ?></td>
                                            <td align="right"><?= ''.number_format($val->cesantias,0) ?></td>
                                            <td align="right"><?= ''.number_format($val->intereses,0) ?></td>
                                            <td align="right"><?= ''.number_format($val->primas,0) ?></td>
                                            <td align="right"><?= ''.number_format($val->vacacion,0) ?></td>
                                            <td align="right"><?= ''.number_format($val->ajuste,0) ?></td>
                                             <td><?= $val->fecha_proceso ?></td>
                                            <td><?= $val->usuariosistema ?></td>
                                            <?php if($model->autorizado == 0){?>
                                                <td style='width: 25px;'>
                                                    <?= Html::a('', ['eliminar_registro', 'id' => $model->id_costo_gasto, 'id_detalle' => $val->id_detalle_nomina], [
                                                      'class' => 'glyphicon glyphicon-trash',
                                                      'data' => [
                                                          'confirm' => 'Esta seguro de eliminar el registro?',
                                                          'method' => 'post',
                                                      ],
                                                    ]) ?>
                                                </td>
                                            <?php }else{?>
                                                <td style='width: 25px;'></td>
                                            <?php }?>    
                                        </tr>
                                   <?php endforeach;?>
                               </tbody>
                            </table>
                        </div>    
                        <?php if($model->autorizado == 0){?>
                            <div class="panel-footer text-right">
                                <?= Html::a('<span class="glyphicon glyphicon-floppy-disk"></span> Generar todo', ['generarcostonomina', 'id' => $model->id_costo_gasto,'fecha_inicio' => $model->fecha_inicio,'fecha_corte' => $model->fecha_corte], ['class' => 'btn btn-success btn-sm']) ?>
                                <!-- Inicio Nuevo Detalle proceso -->
                                  <?= Html::a('<span class="glyphicon glyphicon-send"></span> Generar seleccion',
                                      ['/costos-gastos-empresa/generar_seleccion_empleados','id' => $model->id_costo_gasto],
                                      [
                                          'title' => 'Generar las nominas del personal seleccionado',
                                          'data-toggle'=>'modal',
                                          'data-target'=>'#modalgenerarseleccionempleados',
                                          'class' => 'btn btn-info btn-xs'
                                      ])    
                                      ?>
                               <div class="modal remote fade" id="modalgenerarseleccionempleados" data-backdrop="static">
                                      <div class="modal-dialog modal-lg" style ="width: 750px;">
                                           <div class="modal-content"></div>
                                       </div>
                               </div>
                            </div>
                        <?php }?>
                    </div>
                </div>
            </div>
            <!-- TERMINA TABS -->
            <div role="tabpanel" class="tab-pane" id="gastosfijos">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                         <th scope="col" style='background-color:#B9D5CE;'>Concepto</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Valor</th>
                                    </tr>
                                </thead>
                                <body>
                                     <?php
                                         $costoGasto = \app\models\CostosGastosEmpresa::findOne($model->id_costo_gasto);
                                         $total = 0;
                                           foreach ($servicios as $val):
                                               $total += $val->valor;
                                               ?>
                                                <tr style='font-size:85%;'>
                                                    <td><?= $val->descripcion ?></td>
                                                    <td align="right"><?= ''.number_format($val->valor,0) ?></td>
                                                </tr>
                                     <?php endforeach;
                                    if($costoGasto->periodo){
                                        $costoGasto->gastos_fijos = $total / $costoGasto->periodo;
                                    }else{
                                       $costoGasto->gastos_fijos = $total; 
                                    }    
                                        $costoGasto->save(false);
                                     ?>
                                </body>
                            </table>
                        </div>
                    </div>    
                </div>
            </div>    
            <!--TERMINA EL TABS DE SERVICIOS-->
             <div role="tabpanel" class="tab-pane" id="prestacionservicio">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>F. Inicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>F. Corte</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Devengado</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Deducción</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>T. Pagar</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                    </tr>
                                </thead>
                                <body>
                                     <?php
                                         $costoGasto = \app\models\CostosGastosEmpresa::findOne($model->id_costo_gasto);
                                         $total = 0;
                                           foreach ($prestacionServicio as $val):
                                               $total += $val->devengado;
                                               ?>
                                                <tr style='font-size:85%;'>
                                                    <td><?= $val->documento ?></td>
                                                    <td><?= $val->operario ?></td>
                                                    <td><?= $val->fecha_inicio ?></td>
                                                    <td><?= $val->fecha_corte ?></td>
                                                    <td align="right"><?= ''.number_format($val->devengado,0) ?></td>
                                                    <td align="right"><?= ''.number_format($val->deduccion,0) ?></td>
                                                    <td align="right"><?= ''.number_format($val->total_pagar,0) ?></td>
                                                    <td><?= $val->usuariosistema ?></td>
                                                </tr>
                                     <?php endforeach;
                                     $costoGasto->servicios = $total;
                                     $costoGasto->save(false);
                                     ?>
                                </body>
                            </table>
                        </div>
                    </div>    
                </div>
            </div>   
            <div role="tabpanel" class="tab-pane" id="seguridadsocial">
                <?php if(count($costoSeguridad) > 0 and $model->autorizado == 0){?>
                    <div class="panel-footer text-right">
                            <?= Html::a("<span class='glyphicon glyphicon-saved'></span> Actualizar", ['costos-gastos-empresa/actualizar_registros','id' => $model->id_costo_gasto],["class" => "btn btn-info btn-sm",]) ?>
                    </div> 
                <?php }else{
                    if(count($costoSeguridad)<= 0){?>
                        <div class="panel-footer text-right">
                            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear', ['generarcostoseguridad', 'id' => $model->id_costo_gasto,'fecha_inicio' => $model->fecha_inicio,'fecha_corte' => $model->fecha_corte], ['class' => 'btn btn-success btn-sm']) ?>
                        </div>    
                    <?php }
                      
                }?>
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Salario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Pensión</th>
                                        <th scope="col" style='background-color:#B9D5CE;width: 12px;'>%</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Eps</th>
                                        <th scope="col" style='background-color:#B9D5CE;width: 12px;'>%</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Arl</th>
                                        <th scope="col" style='background-color:#B9D5CE;width: 12px;'>%</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Caja</th>
                                        <th scope="col" style='background-color:#B9D5CE;width: 12px;'>%</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>F. proceso</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                         <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                           
                                    </tr>
                                </thead>
                                <body>
                                    <?php
                                    foreach ($costoSeguridad as $val):
                                        ?>
                                         <tr style='font-size:85%;'>
                                             <td><?= $val->documento ?></td>
                                             <td><?= $val->empleado ?></td>
                                             <td align="right"><?= ''.number_format($val->salario_prestacional,0) ?></td>
                                             <td align="right"><?= ''.number_format($val->pension, 0) ?></td>
                                             <td style="padding-right:1;padding-right: 0;"><input type="text" name="porcentaje_pension[]" value="<?= $val->porcentaje_pension ?>" size="2" maxlength="6"> </td>
                                             <td align="right"><?= ''.number_format($val->eps, 0) ?></td>
                                             <td style="padding-right:1;padding-right: 0;"><input type="text" name="porcentaje_eps[]" value="<?= $val->porcentaje_eps ?>" size="2" maxlength="6"> </td>
                                             <td align="right"><?= ''.number_format($val->arl, 0) ?></td>
                                             <td style="padding-right:1;padding-right: 0;"><input type="text" name="porcentaje_arl[]" value="<?= $val->porcentaje_arl ?>" size="2" maxlength="6"> </td>
                                             <td align="right"><?= ''.number_format($val->caja_compensacion, 0) ?></td>
                                             <td style="padding-right:1;padding-right: 0;"><input type="text" name="porcentaje_caja[]" value="<?= $val->porcentaje_caja ?>" size="2" maxlength="6"> </td>
                                             <td><?= $val->fecha_proceso ?></td>
                                             <td><?= $val->usuariosistema ?></td>
                                             <td style="width: 30px;"><input type="checkbox"  name="registro_seleccionados[]" value="<?= $val->id_seguridad_social ?>"></td>
                                              
                                         </tr>
                                     <?php endforeach;
                                     ?>
                                </body>
                            </table>
                        </div>
                        <div class="panel-footer text-right" >  
                           <?php if($model->autorizado == 0){
                                echo Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar todo', ['costos-gastos-empresa/eliminar_todo_seguridad_social', 'id' => $model->id_costo_gasto], ['class' => 'btn btn-danger btn-sm']) ?>
                                <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar seleccion", ["class" => "btn btn-danger btn-sm", 'name' => 'eliminar_seleccion']);
                             } ?>      
                        </div>    
                    </div>    
                </div>
            </div>    
            <!--TERMINA TABS DE SEGURIDAD SOCIAL
        </div>    
        <!-- TERMINA LA CLASE DE TABS-->
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
  