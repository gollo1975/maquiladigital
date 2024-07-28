<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ComprobanteEgreso */

$this->title = 'Salida de insumos (Detalle)';
$this->params['breadcrumbs'][] = ['label' => 'Salida bodega', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_salida_bodega;
$view = 'salida-bodega';
?>
<div class="salida-bodega-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <div class="btn-group btn-sm" role="group">
            <?php if($token == 0){
                echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']);
            }else{
                echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['search_detalle_insumos'], ['class' => 'btn btn-primary btn-sm']);
            }
            if ($model->autorizado == 0 && $model->numero_salida == 0) {

               echo Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->id_salida_bodega, 'token' => $token], ['class' => 'btn btn-default btn-sm']); 
            }else {   
                if ($model->autorizado == 1 && $model->numero_salida == 0){
                    echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->id_salida_bodega, 'token' => $token], ['class' => 'btn btn-default btn-sm']);
                    echo Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar salida', ['cerrar_despacho', 'id' => $model->id_salida_bodega, 'token' => $token],['class' => 'btn btn-warning btn-sm',
                               'data' => ['confirm' => 'Esta seguro de CERRAR y CREAR el consecutivo a la salida de insumos de la referencia ('.$model->orden->referencia->referencia.')', 'method' => 'post']]);
                }else{ 
                    if($model->exportar_inventario == 0){
                        echo Html::a('<span class="glyphicon glyphicon-import"></span> Descargar inventario', ['enviar_inventario', 'id' => $model->id_salida_bodega, 'token' => $token],['class' => 'btn btn-success btn-sm',
                             'data' => ['confirm' => 'Esta seguro de ENVIAR este inventario de la referencia ('.$model->orden->referencia->referencia.') para ser descargado del modulo de insumos', 'method' => 'post']]);
                    }else{
                       echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir insumos', ['imprimir_insumos', 'id' => $model->id_salida_bodega], ['class' => 'btn btn-default btn-sm']);            
                       echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir operaciones', ['imprimir_operaciones', 'id' => $model->id_salida_bodega], ['class' => 'btn btn-default btn-sm']);            
                       echo Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 20, 'codigo' => $model->id_salida_bodega,'view' => $view, 'token' => $token], ['class' => 'btn btn-default btn-sm']);                                                              
                    }
                }    
            }?>

        </div>    
    </p>
    <?php
    
    ?>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h5><?= Html::encode($this->title) ?></h5>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 90%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_salida') ?></th>
                    <td><?= Html::encode($model->numero_salida) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_producto') ?>:</th>
                    <td><?= Html::encode($model->codigo_producto) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Referencia') ?>:</th>
                    <td><?= Html::encode($model->orden->referencia->referencia) ?> (Cant. lote: <?= Html::encode($model->orden->cantidades) ?>) Unidades</td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Total_insumos') ?>:</th>
                    <td align="right"><?= Html::encode(''.number_format($model->unidades,0)) ?></td>
                   
                </tr>
                <tr style="font-size: 90%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_salida') ?></th>
                    <td><?= Html::encode($model->fecha_salida) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'autorizado') ?></th>
                    <td><?= Html::encode($model->autorizadoSalida) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'responsable') ?></th>
                    <td><?= Html::encode($model->responsable) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'proceso_cerrado') ?></th>
                    <td ><?= Html::encode($model->cerradoSalida) ?></td>
                  
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
    ]);?>
    <!--INICIOS DE TABS-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#listadoinsumos" aria-controls="listadoinsumos" role="tab" data-toggle="tab">Insumos <span class="badge"><?= count($listado_insumos) ?></span></a></li>
            <li role="presentation"><a href="#listadoperaciones" aria-controls="listadoperaciones" role="tab" data-toggle="tab">Operaciones <span class="badge"><?= count($listado_operaciones) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="listadoinsumos">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style="font-size: 90%;">
                                        <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Nombre de insumo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Medida</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad a despachar</th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 30px;'>Nota</th>
                                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listado_insumos as $val): ?>
                                       <tr style="font-size: 90%;">
                                            <td><?= $val->codigo_insumo ?></td>
                                            <td><?= $val->insumo->descripcion ?></td>
                                            <td><?= $val->insumo->tipomedida->medida?></td>
                                            <?php if($model->autorizado == 0){?>
                                                <td style="padding-right:1;padding-right: 1; text-align: right"><input type="text" name="cantidad_despachar[]" value="<?= $val->cantidad_despachar ?>"  style='text-align: right'  size="10" required = true></td>
                                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="observacion[]" value="<?= $val->nota ?>" size="70" maxlength="60" ></td>
                                            <?php }else{?>
                                                <td style="text-align: right" ><?= ''.number_format($val->cantidad_despachar,0) ?></td>
                                                <td style="width: 350px;"><?= $val->nota ?></td>
                                            <?php }?> 
                                            <input type="hidden" name="materia_prima[]" value="<?= $val->id?>"> 
                                            <td style="width: 30px;"><input type="checkbox" name="listado_eliminar[]" value="<?= $val->id ?>"></td>
                                       </tr>  
                                    <?php endforeach;?>   
                                </<body>
                            </table>
                        </div>
                        <?php
                        if($model->autorizado == 0){
                            if(count($listado_insumos) == null){?>
                                <div class="panel-footer text-right"> 
                                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Cargar insumo', ['salida-bodega/cargar_nuevo_insumo', 'id' => $model->id_salida_bodega, 'token' => $token], ['class' => 'btn btn-success btn-sm']) ?>
                                </div> 
                            <?php
                            }else{?>
                                <div class="panel-footer text-right"> 
                                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Cargar insumo', ['salida-bodega/cargar_nuevo_insumo', 'id' => $model->id_salida_bodega, 'token' => $token], ['class' => 'btn btn-success btn-sm']) ?>
                                    <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Actualizar", ["class" => "btn btn-warning btn-sm", 'name' => 'actualizar_inventario']) ?>
                                    <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar", ["class" => "btn btn-danger btn-sm", 'name' => 'eliminar_todo']) ?>
                                </div>     
                            <?php }
                        } else {?>
                              <div class="panel-footer text-right"> 
                                <?= Html::a('<span class="glyphicon glyphicon-export"></span> Exportar a excel', ['salida-bodega/exportar_detalle', 'id' => $model->id_salida_bodega], ['class' => 'btn btn-primary btn-sm']) ?>
                              </div>    
                        <?php } ?>
                    </div>    
                </div>
            </div> 
            <!--TERMINA TABS DE INSUMOSS-->
            <div role="tabpanel" class="tab-pane" id="listadoperaciones">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style="font-size: 90%;">
                                        <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo proceso</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo maquina</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Segundos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Minutos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>User name</th>
                                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listado_operaciones as $val): ?>
                                        <tr style="font-size: 90%;">                                    
                                            <td><?= $val->idproceso ?></td>
                                                 <td><?= $val->proceso->proceso ?></td>
                                                 <?php if($val->idtipo == ''){?>
                                                       <td><?= 'No found' ?></td>
                                                 <?php }else{?>
                                                       <td style='background-color:<?= $val->tipo->color?>;'><?= $val->tipo->tipo ?></td>
                                                 <?php }?>      
                                                 <td><?= $val->tipoMaquinas->descripcion ?></td>
                                                 <td><?= $val->segundos?></td>
                                                 <td><?= $val->minutos?></td>
                                             <td><?= $val->user_name?></td>
                                             <td style="width: 30px;"><input type="checkbox" name="listado_operaciones[]" value="<?= $val->id_operacion ?>"></td>
                                        </tr>      
                                    <?php endforeach;?>   
                                </<body>
                            </table>
                        </div>
                        <?php
                        if($model->autorizado == 0){
                            if(count($listado_operaciones) == null){?>
                                <div class="panel-footer text-right"> 
                                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Cargar operaciones', ['salida-bodega/cargar_operaciones', 'id' => $model->id_salida_bodega, 'token' => $token], ['class' => 'btn btn-success btn-sm']) ?>
                                </div> 
                            <?php
                            }else{?>
                                <div class="panel-footer text-right"> 
                                   <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Cargar operaciones', ['salida-bodega/cargar_operaciones', 'id' => $model->id_salida_bodega, 'token' => $token], ['class' => 'btn btn-success btn-sm']) ?>
                                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['salida-bodega/editar_operaciones', 'id' => $model->id_salida_bodega, 'token' => $token], ['class' => 'btn btn-primary btn-sm']) ?>                    
                                   <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar", ["class" => "btn btn-danger btn-sm", 'name' => 'eliminar_operacion']) ?>
                                </div>     
                            <?php }
                        } else {?>
                              <div class="panel-footer text-right"> 
                                <?= Html::a('<span class="glyphicon glyphicon-export"></span> Exportar a excel', ['salida-bodega/exportar_detalle', 'id' => $model->id_salida_bodega], ['class' => 'btn btn-primary btn-sm']) ?>
                              </div>    
                        <?php } ?>
                    </div>    
                </div>
            </div> 
            <!--TEMRINA TABSA DE OPERACIONES-->
        </div>
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

