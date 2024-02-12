<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproducciontipo */

$this->title = 'Detalle insumos';
$this->params['breadcrumbs'][] = ['label' => 'Insumos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_insumos;
?>
<div class="insumos-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

  
        <?php if($token == 0){?>
            <p>
               <?php  echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']);
                 echo Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->id_insumos], [
                  'class' => 'btn btn-danger btn-sm',
                  'data' => [
                      'confirm' => 'Esta seguro de eliminar el registro?',
                      'method' => 'post',
                            ],
                  ]);?>
               </p>  
        <?php }else{?>
            <p>   
                <?php echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'token' => $token], ['class' => 'btn btn-primary btn-sm']);?>
            </p>    
        <?php }
?>
               
    <div class="panel panel-success">
        <div class="panel-heading">
            Orden de Producción tipo
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 90%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?></th>
                    <td><?= Html::encode($model->id_insumos) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Codigo') ?></th>
                    <td><?= Html::encode($model->codigo_insumo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Materia_prima') ?></th>
                    <td><?= Html::encode($model->descripcion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'aplica_iva') ?></th>
                    <td><?= Html::encode($model->aplicaIva) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'precio_unitario') ?></th>
                    <td style="text-align: right;"><?= Html::encode(''.number_format($model->precio_unitario,0)) ?></td>
                </tr>
                <tr style="font-size: 90%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Medida') ?></th>
                    <td><?= Html::encode($model->tipomedida->medida) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_entrada') ?></th>
                    <td><?= Html::encode($model->fecha_entrada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_vencimiento') ?></th>
                    <td><?= Html::encode($model->fecha_vencimiento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_registro') ?></th>
                    <td><?= Html::encode($model->fecha_registro) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'subtotal') ?></th>
                    <td style="text-align: right;"><?= Html::encode(''.number_format($model->subtotal,0)) ?></td>
                </tr>
                <tr style="font-size: 90%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'usuariosistema') ?></th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'stock_inicial') ?></th>
                    <td style="text-align: right;"><?= Html::encode(''.number_format($model->stock_inicial,0)) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idproveedor') ?></th>
                    <td><?= Html::encode($model->proveedorMateria->nombrecorto) ?></td>
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'stock_real') ?></th>
                    <td style="text-align: right; background-color:#F5EEF8;"><?= Html::encode(''.number_format($model->stock_real,0)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Iva') ?></th>
                    <td style="text-align: right;"><?= Html::encode(''.number_format($model->total_iva,0)) ?></td>
                </tr>
                <tr style="font-size: 90%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'inventario_inicial') ?></th>
                    <td><?= Html::encode($model->inventarioInicial) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'aplica_inventario') ?></th>
                    <td><?= Html::encode($model->aplicaInventario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'porcentaje_iva') ?></th>
                    <td colspan="3"><?= Html::encode($model->porcentaje_iva) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_materia_prima') ?></th>
                    <td style="text-align: right;"><?= Html::encode(''.number_format($model->total_materia_prima,0)) ?></td>
                </tr>
                <tr style="font-size: 90%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'descripcion') ?></th>
                    <td colspan="9"><?= Html::encode($model->descripcion)?></td>
                </tr>
            </table>
        </div>
    </div>

</div>
