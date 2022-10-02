<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\ComprobanteEgreso */

$this->title = 'Detalle costo';
$this->params['breadcrumbs'][] = ['label' => 'Detalle producto', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_producto;
$view = 'costo-producto';
?>
<div class="costo-producto-view-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->id_producto], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php if ($model->autorizado == 0) { ?>

            <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->id_producto], ['class' => 'btn btn-default btn-sm']); }
        else {
            echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->id_producto], ['class' => 'btn btn-default btn-sm']);
            echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimir', 'id' => $model->id_producto], ['class' => 'btn btn-default btn-sm']);            
            echo Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 15, 'codigo' => $model->id_producto,'view' => $view], ['class' => 'btn btn-default btn-sm']);                                                         
            
        }
        ?>
        
    </p>
    <?php
    if ($mensaje != ""){
        ?> <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo $mensaje ?>
    </div> <?php
    }
    ?>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h5><?= Html::encode($this->title) ?></h5>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_producto') ?>:</th>
                    <td><?= Html::encode($model->id_producto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_producto') ?></th>
                    <td><?= Html::encode($model->codigo_producto) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Costo_sin_iva') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($model->costo_sin_iva,0)) ?></td>
                   
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Tipo_Producto') ?>:</th>
                    <td><?= Html::encode($model->tipoProducto->concepto) ?></td>
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'descripcion') ?></th>
                    <td><?= Html::encode($model->descripcion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Costo_con_iva') ?>:</th>
                    <td align="right"><?= Html::encode('$ '.number_format($model->costo_con_iva,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Autorizado') ?>:</th>
                    <td><?= Html::encode($model->autorizadocosto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_creacion') ?>:</th>
                    <td ><?= Html::encode($model->fecha_creacion) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'porcentaje_iva') ?>:</th>
                    <td align="right"><?= Html::encode($model->porcentaje_iva) ?>%</td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan="3"><?= Html::encode($model->observacion) ?></td>
                </tr>
              
            </table>
        </div>
    </div>
    <!--INICIOS DE TABS-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#insumos" aria-controls="insumos" role="tab" data-toggle="tab">Insumos <span class="badge"><?= count($costo_producto_detalle) ?></span></a></li>
            <li role="presentation"><a href="#tallas" aria-controls="tallas" role="tab" data-toggle="tab">Tallas <span class="badge"><?= count($costo_producto_detalle) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="insumos">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Insumo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Vlr_Unitario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Total</th>
                                        <th style='background-color:#B9D5CE;'></th>
                                        <th style='background-color:#B9D5CE;'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($costo_producto_detalle as $val): ?>
                                       <tr style="font-size: 85%;">
                                            <td><?= $val->codigo_insumo ?></td>
                                            <td><?= $val->insumos->descripcion ?></td>
                                            <td style="text-align: right"><?= ''.number_format($val->cantidad,2) ?></td>
                                            <td style="text-align: right"><?= '$'.number_format($val->vlr_unitario,2) ?></td>
                                            <td style="text-align: right"><?= '$'.number_format($val->total,0) ?></td>

                                        <?php if ($model->autorizado == 0) { ?>
                                            <td style="width: 30px;">
                                                <a href="#" data-toggle="modal" data-target="#iddetalleproducto2<?= $val->id ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                                                <!-- Editar modal detalle -->
                                                <div class="modal fade" role="dialog" aria-hidden="true" id="iddetalleproducto2<?= $val->id ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                                <h4 class="modal-title">Editar detalle <?= $val->id ?></h4>
                                                            </div>
                                                            <?= Html::beginForm(Url::toRoute("costo-producto/editardetalle"), "POST") ?>
                                                            <div class="modal-body">
                                                                <div class="panel panel-success">
                                                                    <div class="panel-heading">
                                                                       Detalle insumo
                                                                    </div>
                                                                    <div class="panel-body">
                                                                        <div class="col-lg-2">
                                                                            <label>Cantidad:</label>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <input type="text" name="cantidad" value="<?= $val->cantidad ?>" class="form-control" required>
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            <label>Vlr_Unitario:</label>
                                                                        </div>
                                                                        <div class="col-lg-3">
                                                                            <input type="text" name="vlrunitario" value="<?=  $val->vlr_unitario ?>" class="form-control" required>
                                                                        </div>

                                                                        <input type="hidden" name="iddetalle" value="<?= $val->id ?>">
                                                                        <input type="hidden" name="idproducto" value="<?= $model->id_producto ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                                <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Guardar</button>
                                                            </div>
                                                            <?= Html::endForm() ?>
                                                        </div><!-- /.modal-content -->
                                                    </div><!-- /.modal-dialog -->
                                                </div><!-- /.modal -->
                                            </td>
                                            <td style="width: 30px;">
                                                <!-- Eliminar modal detalle -->
                                                <a href="#" data-toggle="modal" data-target="#iddetalleproducto<?= $val->id ?>"><span class="glyphicon glyphicon-trash"></span></a>
                                                <div class="modal fade" role="dialog" aria-hidden="true" id="iddetalleproducto<?= $val->id ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                                <h4 class="modal-title">Eliminar Detalle</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>¿Realmente deseas eliminar el registro con ID:  <?= $val->id ?>?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <?= Html::beginForm(Url::toRoute("costo-producto/eliminardetalle"), "POST") ?>
                                                                <input type="hidden" name="iddetalle" value="<?= $val->id ?>">
                                                                <input type="hidden" name="idproducto" value="<?= $model->id_producto ?>">
                                                                <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                                <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Eliminar</button>
                                                                <?= Html::endForm() ?>
                                                            </div>
                                                        </div><!-- /.modal-content -->
                                                    </div><!-- /.modal-dialog -->
                                                </div><!-- /.modal -->
                                            </td>
                                        <?php }else{ ?>
                                            <td></td>   
                                            <td></td>   
                                        <?php } ?>     
                                       </tr>                    
                                        <?php endforeach; ?>
                                </<body>
                            </table>
                        </div>
                        <div class="panel-footer text-right"> 
                            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nuevo', ['costo-producto/nuevodetalle', 'id' => $model->id_producto], ['class' => 'btn btn-success btn-sm']) ?>
                            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['costo-producto/editartododetalle', 'id' => $model->id_producto],[ 'class' => 'btn btn-success btn-sm']) ?>
                            <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['costo-producto/eliminartododetalle', 'id' => $model->id_producto], ['class' => 'btn btn-danger btn-sm']) ?>                    
                        </div> 
                    </div>    
                </div>
            </div> 
            <!-- TERMINA TABS-->
            <div role="tabpanel" class="tab-pane" id="tallas">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                 <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidades</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha registro</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                        <th style='background-color:#B9D5CE;'></th>
                                        <th style='background-color:#B9D5CE;'></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- TERMINA TABS-->
        </div>
    </div>
</div>
