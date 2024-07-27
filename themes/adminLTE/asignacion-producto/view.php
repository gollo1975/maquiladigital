<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\ComprobanteEgreso */

$this->title = 'Asignacion de ordenes';
$this->params['breadcrumbs'][] = ['label' => 'Asignacion ordenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_asignacion;
$view = 'asignacion-producto';
$producto = 0;
foreach ($detalle_orden as $proceso):
    $producto = $proceso->detalle->id_orden_fabricacion;
endforeach;
?>
<div class="costo-producto-view-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <div class="btn-group btn-sm" role="group">
           <button type="button" class="btn btn-default btn">  <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
           </button>    
            <?php if ($model->autorizado == 0) { ?>
                <button type="button" class="btn btn-default btn"><?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizar', 'id' => $model->id_asignacion, 'token' => $token], ['class' => 'btn btn-default btn-sm'])?>
                </button>
            <?php }else{
                if($model->orden_produccion == 0 && $model->autorizado != 0){?>
                    <button type="button" class="btn btn-default btn"><?php echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizar', 'id' => $model->id_asignacion, 'token' => $token], ['class' => 'btn btn-default btn-sm']);?>
                       <?= Html::a('<span class="glyphicon glyphicon-duplicate"></span> Crear documento', ['generardocumento','id' => $model->id_asignacion, 'token' => $token, 'id_orden' => $producto],['class' => 'btn btn-info btn-sm',
                          'data' => ['confirm' => 'Esta segura de crear la Orden de Producción a este proveedor.', 'method' => 'post']]) ?>
                    </button>    
                <?php }else{ ?>
                <button type="button" class="btn btn-default btn"> <?php echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['/asignacion-producto/imprimiordenproduccion', 'id' => $model->id_asignacion],['class' => 'btn btn-default btn-sm']) ;?>
                </button>        
                <button type="button" class="btn btn-default btn"> <?=  Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 22, 'codigo' => $model->id_asignacion,'view' => $view, 'token' => $token], ['class' => 'btn btn-default btn-sm']);?>                                                         
                </button>   
               <?php }
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
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?>:</th>
                    <td><?= Html::encode($model->id_asignacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_orden') ?>:</th>
                    <td><?= Html::encode($model->orden_produccion) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Documento') ?>:</th>
                    <td><?= Html::encode($model->documento) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Proveedor') ?>:</th>
                    <td ><?= Html::encode($model->razon_social)?></td>
                   
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_asignacion') ?>:</th>
                    <td><?= Html::encode($model->fecha_asignacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_registro') ?>:</th>
                    <td><?= Html::encode($model->fecha_registro) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Unidades') ?>:</th>
                    <td align="right"><?= Html::encode($model->unidades) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Proceso') ?>:</th>
                    <td ><?= Html::encode($model->tipo->tipo) ?></td>
                  
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Autorizado') ?>:</th>
                    <td ><?= Html::encode($model->estadoautorizado) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?>:</th>
                    <td><?= Html::encode($model->usuario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Ultimo_usuario') ?>:</th>
                    <td align="right"><?= Html::encode($model->usuario_editado) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Total_orden') ?>:</th>
                    <td align="right"><?= Html::encode('$ '.number_format($model->total_orden,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_editado') ?>:</th>
                    <td><?= Html::encode($model->fecha_editado) ?></td>
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan="5"><?= Html::encode($model->observacion) ?></td>
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
            <li role="presentation" class="active"><a href="#detalletallas" aria-controls="detalletallas" role="tab" data-toggle="tab">Detalle orden <span class="badge"><?= count($detalle_orden) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="insumos">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style="">
                                        <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Tallas</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Sam</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Vr. unidad</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>subtotal</th>
                                         <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($detalle_orden as $val): ?>
                                       <tr style="font-size: 85%;">
                                            <td><?= $val->codigo_producto ?></td>
                                            <td><?= $val->referencia ?></td>
                                            <td><?=$val->talla->talla ?></td>
                                            <td style="text-align: right"><?= ''.number_format($val->tiempo_confeccion,0) ?></td>
                                            <td style="text-align: right"><?= ''.number_format($val->cantidad,0) ?></td>
                                            <td style="text-align: right"><?= '$'.number_format($val->valor_minuto,0) ?></td>
                                            <td style="text-align: right"><?= '$'.number_format($val->subtotal_producto,0) ?></td>
                                            <td style="width: 25px;"><input type="checkbox" name="detalle[]" value="<?= $val->id_detalle_asignacion?>"></td>    
                                       </tr>                    
                                        <?php endforeach; ?>
                                </<body>
                            </table>
                        </div>
                        <?php if($model->autorizado == 0){?>
                            <div class="panel-footer text-right">
                                   <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['asignacion-producto/editardetalleasignacion', 'id' => $model->id_asignacion, 'token' => $token],[ 'class' => 'btn btn-primary btn-sm']) ?>
                                 <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar", ["class" => "btn btn-danger btn-sm", 'name' => 'eliminardetalle']) ?>
                            </div>    
                         <?php }?>
                             
                    </div>    
                </div>
            </div> 
            <!-- TERMINA TABS-->
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
