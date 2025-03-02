<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Municipio */

$this->title = 'Referencias/Productos';
$this->params['breadcrumbs'][] = ['label' => 'Referencias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->codigo;
$tipoOrden = ArrayHelper::map(\app\models\Ordenproducciontipo::find()->all(), 'idtipo', 'tipo');
$listaPrecio = ArrayHelper::map(\app\models\ListaPrecios::find()->all(), 'id_lista', 'nombre_lista');
?>
<div class="referencia-producto-view">

   <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->codigo], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Información de la referencia.
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 90%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo') ?></th>
                    <td>R-<?= Html::encode($model->codigo) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'descripcion_referencia') ?></th>
                    <td><?= Html::encode($model->descripcion_referencia) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_tipo_producto') ?></th>
                    <td><?= Html::encode($model->tipoProducto->concepto) ?></td>                    
                </tr>
                <tr style="font-size: 90%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'F._registro') ?></th>
                    <td><?= Html::encode($model->fecha_registro) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'user_name') ?></th>
                    <td><?= Html::encode($model->user_name) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'costo_producto') ?></th>
                    <td style="text-align: right"><?= Html::encode(''. number_format($model->costo_producto,0)) ?></td>                    
                </tr>    
                 <tr style="font-size: 90%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'descripcion') ?></th>
                    <td colspan="6"><?= Html::encode($model->descripcion) ?></td>                    
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
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#listadoinsumos"aria-controls="listadoinsumos" role="tab" data-toggle="tab">Lista de insumos <span class="badge"><?= count($lista_insumos) ?></span></a></li>
             <li role="presentation"><a href="#listaprecio"aria-controls="listaprecio" role="tab" data-toggle="tab">Lista de precios <span class="badge"><?= count($lista_precio) ?></span></a></li>
            
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="listadoinsumos">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <div class="panel panel-success">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr style="font-size: 90%;">
                                            <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Nombre de insumo</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Tipo servicio</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Maneja unidad</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Valor_Costo</th>
                                            <th scope="col" style='background-color:#B9D5CE;'></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($lista_insumos as $lista):?>
                                            <tr style="font-size: 85%;">
                                                <td><?= $lista->id_detalle?></td>
                                                 <td><?= $lista->insumos->descripcion?></td>
                                                <td style="padding-left: 1;padding-right: 1;"><?= Html::dropDownList('tipo_orden[]', $lista->idtipo, $tipoOrden, ['class' => 'col-sm-10', 'prompt' => 'Seleccione', 'required' => true]) ?></td>
                                                <td align="center"><select name="maneja_unidad[]" style="width: 80px;">
                                                        <?php if ($lista->maneja_unidad == 0){echo $pieza = "NO";}elseif ($lista->maneja_unidad == 1){echo $pieza ="SI";}else {echo $pieza ="MIXTO";}?>
                                                        <option value="<?= $lista->maneja_unidad ?>"><?= $pieza ?></option>
                                                        <option value="0">NO</option>
                                                        <option value="1">SI</option>
                                                        <option value="2">MIXTO</option>

                                                </select> </td>    
                                                 <td style="padding-right: 1;padding-right: 1; text-align: right"> <input type="text" name="unidades[]" value="<?= $lista->total_unidades?>" style="text-align: right" size="7" > </td> 
                                                <td style="padding-right: 1;padding-right: 1; text-align: right"> <input type="text" name="cantidad[]" value="<?= $lista->cantidad?>" style="text-align: right" size="7" required="true"> </td> 
                                                <td style="text-align:right"><?= ''. number_format($lista->costo_producto,0)?></td>
                                                <input type="hidden" name="listado_insumos[]" value="<?= $lista->id_detalle ?>">
                                                <td style="width: 20px; height: 20px">	
                                                    <?= Html::a('', ['eliminar_insumos', 'id' => $model->codigo, 'id_detalle' => $lista->id_detalle], [
                                                      'class' => 'glyphicon glyphicon-trash',
                                                      'data' => [
                                                          'confirm' => 'Esta seguro de eliminar el registro?',
                                                          'method' => 'post',
                                                      ],
                                                    ]) ?>
                                                </td>
                                            </tr>
                                        <?php
                                        endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="panel-footer text-right" >  
                                 <?= Html::a('<span class="glyphicon glyphicon-search"></span> Buscar insumos', ['referencia-producto/search_insumos', 'id' => $model->codigo], ['class' => 'btn btn-success btn-sm']) ?>
                                <?= Html::submitButton("<span class='glyphicon glyphicon-refresh'></span> Actualizar", ["class" => "btn btn-warning btn-sm", 'name' => 'actualizar_insumos']) ?>
                            </div>
                        </div>   
                    </div>
                </div>
            </div>  
             <!--TERMINA TABS-->
            <div role="tabpanel" class="tab-pane" id="listaprecio">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <div class="panel panel-success">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr style="font-size: 90%;">
                                            <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Valor venta</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Listas de precio</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>User name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($lista_precio as $lista):?>
                                            <tr style="font-size: 90%;">
                                                <td><?= $lista->id_detalle?></td>
                                                <td style="padding-right: 1;padding-right: 0; text-align: right"> <input type="text" name="precio_venta_publico[]" value="<?= $lista->valor_venta ?>" style="text-align: right" size="9" required="true"> </td> 
                                                <td style="padding-left: 1;padding-right: 0;"><?= Html::dropDownList('lista_precio[]', $lista->id_lista, $listaPrecio, ['class' => 'col-sm-10', 'prompt' => 'Seleccione', 'required' => true]) ?></td>
                                                <td><?= $lista->user_name?></td>
                                                <input type="hidden" name="listado_precios[]" value="<?= $lista->id_detalle ?>">
                                                 
                                            </tr>
                                        <?php
                                        endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="panel-footer text-right" >  
                                <!-- Inicio Nuevo Detalle proceso -->
                                  <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear precio',
                                      ['/referencia-producto/nuevo_precio_venta','id' => $model->codigo],
                                      [
                                          'title' => 'Crear nuevo precio de venta',
                                          'data-toggle'=>'modal',
                                          'data-target'=>'#modalnuevoprecioventa'.$model->codigo,
                                          'class' => 'btn btn-info btn-sm'
                                      ])    
                                 ?>
                                <div class="modal remote fade" id="modalnuevoprecioventa<?= $model->codigo ?>">
                                    <div class="modal-dialog modal-lg" style ="width: 500px;">
                                         <div class="modal-content"></div>
                                    </div>
                                </div> 
                                <?php if(count($lista_precio)> 0){?>
                                     <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-warning btn-sm", 'name' => 'actualizar_precio_venta']);?>    
                                <?php }?> 
                            </div>
                        </div>   
                        </div>
                    </div>
                </div>    
                <!--TERMINA TABS-->
    </div>  
      <?php ActiveForm::end(); ?>
</div>

