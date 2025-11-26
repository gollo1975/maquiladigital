<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Municipio */

$this->title = '(' .$model->nombre_producto. ')';
$this->params['breadcrumbs'][] = ['label' => 'inventario-punto-venta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_inventario;
$listaPrecio = ArrayHelper::map(\app\models\ListaPrecios::find()->all(), 'id_lista', 'nombre_lista');
?>
<div class="referencia-producto-view">

   <p>
        <?php if($token == 0){?>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary']) ?>
        <?php } else { ?>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['search_inventario'], ['class' => 'btn btn-primary']) ?>
        <?php }?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Información de la referencia.
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 90%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_inventario') ?></th>
                    <td><?= Html::encode($model->id_inventario) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_producto') ?></th>
                    <td><?= Html::encode($model->codigo_producto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nombre_producto') ?></th>
                    <td><?= Html::encode($model->nombre_producto) ?></td>                    
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
            <li role="presentation" class="active"><a href="#listaprecio"aria-controls="listaprecio" role="tab" data-toggle="tab">Lista de precios <span class="badge"><?= count($lista_precio) ?></span></a></li>
            <li role="presentation"><a href="#listadescuento"aria-controls="listadescuento" role="tab" data-toggle="tab">Descuentos <span class="badge"><?= count($descuentos) ?></span></a></li>
            
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="listaprecio">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <div class="panel panel-success">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr style="font-size: 90%;">
                                            <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Valor venta</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Listas de precio</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Predeterminado</th>
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
                                                <td align="center"><select name="predeterminado[]" style="width: 100px;">
                                                 <?php if ($lista->predeterminado == 0){echo $predeterminado = "NO";} else{ echo $predeterminado ="SI";}?>
                                                        <option value="<?= $lista->predeterminado ?>"><?= $predeterminado ?></option>
                                                        <option value="0">NO</option>
                                                        <option value="1">SI</option>

                                                </select> </td>  
                                                <td><?= $lista->user_name?></td>
                                                <input type="hidden" name="listado_precios[]" value="<?= $lista->id_detalle ?>">
                                                 
                                            </tr>
                                        <?php
                                        endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if($token == 0){?>
                                <div class="panel-footer text-right" >  
                                    <!-- Inicio Nuevo Detalle proceso -->
                                      <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear precio',
                                          ['/inventario-punto-venta/nuevo_precio_venta','id' => $model->id_inventario,'token' => $token],
                                          [
                                              'title' => 'Crear nuevo precio de venta',
                                              'data-toggle'=>'modal',
                                              'data-target'=>'#modalnuevoprecioventa'.$model->id_inventario,
                                              'class' => 'btn btn-info btn-sm'
                                          ])    
                                     ?>
                                    <div class="modal remote fade" id="modalnuevoprecioventa<?= $model->id_inventario ?>" data-backdrop="static">
                                        <div class="modal-dialog modal-lg" style ="width: 500px;">
                                             <div class="modal-content"></div>
                                        </div>
                                    </div> 
                                    <?php if(count($lista_precio)> 0){?>
                                         <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-warning btn-sm", 'name' => 'actualizar_precio_venta']);?>    
                                    <?php }?> 
                                </div>
                            <?php }?>
                        </div>   
                    </div>
                </div>
            </div>    
            <!--TERMINA TABS-->
            <div role="tabpanel" class="tab-pane " id="listadescuento">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <div class="panel panel-success">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr style="font-size: 90%;">
                                            <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Fecha vencimiento</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Tipo descuento</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Valor descuento</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Activo</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>User name</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>F. registro</th>
                                             <th scope="col" style='background-color:#B9D5CE;'></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($descuentos as $val):?>
                                            <tr style="font-size: 85%;">
                                                <td><?= $val->id_regla?></td>
                                                <td><?= $val->fecha_inicio?></td>
                                                <td><?= $val->fecha_final?></td>
                                                <td><?= $val->tipoDescuento ?? 'NO FOUNT'?></td>
                                                <td style="text-align: right"><?= $val->nuevo_valor?></td>
                                                <td><?= $val->estadoRegla?></td>
                                                <td><?= $val->user_name?></td>
                                                <td><?= $val->fecha_registro?></td>
                                                <?php if($token == 0){?>
                                                    <td style= 'width: 25px; height: 25px;'>
                                                        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ',
                                                            ['/inventario-punto-venta/editar_regla_comercial','id' => $model->id_inventario,'id_detalle' => $val->id_regla, 'token' => $token],
                                                            [
                                                                'title' => 'Editar regla comercial',
                                                                'data-toggle'=>'modal',
                                                                'data-target'=>'#modaleditarreglacomercial'.$model->id_inventario,
                                                            ])    
                                                        ?>
                                                        <div class="modal remote fade" id="modaleditarreglacomercial<?= $model->id_inventario ?>" data-backdrop="static">
                                                            <div class="modal-dialog modal-lg" style ="width: 500px;">
                                                                 <div class="modal-content"></div>
                                                            </div>
                                                        </div> 
                                                    </td>  
                                                <?php }else{?>    
                                                    <td style= 'width: 25px; height: 25px;'></td>
                                                 <?php }?>   
                                            </tr>
                                        <?php
                                        endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if(!$descuentos){?> 
                            <div class="panel-footer text-right">
                                <!-- Inicio Nuevo Detalle proceso -->
                                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear descuento',
                                        ['/inventario-punto-venta/crear_regla_comercial','id' => $model->id_inventario],
                                        [
                                            'title' => 'Crear la nueva regla comercial de descuento',
                                            'data-toggle'=>'modal',
                                            'data-target'=>'#modalnuevoreglacomercial'.$model->id_inventario,
                                            'class' => 'btn btn-success btn-sm'
                                        ])    
                                   ?>
                                <div class="modal remote fade" id="modalnuevoreglacomercial<?= $model->id_inventario ?>" data-backdrop="static">
                                    <div class="modal-dialog modal-lg" style ="width: 500px;">
                                         <div class="modal-content"></div>
                                    </div>
                                </div> 
                            </div>
                        <?php }?>
                        </div>
                    </div>
                </div>
            </div>    
            <!--TERMINA TABS-->                            
        </div>  
       <?php ActiveForm::end(); ?>
    </div>
</div>
