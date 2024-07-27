<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\ComprobanteEgreso */

$this->title = 'Pedido del cliente';
$this->params['breadcrumbs'][] = ['label' => 'Pedido del cliente', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_pedido;
$view = 'pedido-cliente';
?>
<div class="pedido-cliente-view">

    <p>
        <div class="btn-group btn-sm" role="group">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']);
            if ($model->autorizado == 0 && $model->numero_pedido == 0) { 
                echo Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->id_pedido, 'token' => $token], ['class' => 'btn btn-default btn-sm']); }
            else {
                if ($model->autorizado == 1 && $model->numero_pedido == 0) {
                    echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->id_pedido, 'token' => $token], ['class' => 'btn btn-default btn-sm']);
                    echo Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar pedido', ['cerrar_pedido', 'id' => $model->id_pedido, 'token'=> $token],['class' => 'btn btn-info btn-sm',
                         'data' => ['confirm' => 'Esta seguro que desea cerrar el pedido al cliente ('.$model->cliente->nombrecorto.')', 'method' => 'post']]);
                }else{    
                    echo Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 21, 'codigo' => $model->id_pedido,'view' => $view, 'token' => $token], ['class' => 'btn btn-default btn-sm']);                                                         
                    echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir pedido', ['/pedido-cliente/imprimir_pedido', 'id' => $model->id_pedido],['class' => 'btn btn-default btn-sm']);
                     echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir tallas', ['/pedido-cliente/imprimir_tallas', 'id' => $model->id_pedido],['class' => 'btn btn-default btn-sm']);
                }    
            }?>
        </div>    
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h5><?= Html::encode($this->title) ?></h5>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?>:</th>
                    <td><?= Html::encode($model->id_pedido) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_pedido') ?></th>
                    <td><?= Html::encode($model->numero_pedido) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idcliente') ?></th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'valor_total') ?>:</th>
                    <td align="right"><?= Html::encode(''.number_format($model->valor_total,0)) ?></td>
                   
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_pedido') ?>:</th>
                    <td><?= Html::encode($model->fecha_pedido) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_entrega') ?>:</th>
                    <td><?= Html::encode($model->fecha_entrega) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_proceso') ?>:</th>
                    <td ><?= Html::encode($model->fecha_proceso) ?>%</td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'impuesto') ?>:</th>
                    <td align="right"><?= Html::encode(''.number_format($model->impuesto,0)) ?></td>
                  
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_unidades') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->total_unidades,0)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'user_name') ?>:</th>
                    <td><?= Html::encode($model->user_name) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'pedido_cerrado') ?>:</th>
                    <td align="right"><?= Html::encode($model->pedidoCerrado) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_pedido') ?>:</th>
                    <td align="right"><?= Html::encode(''.number_format($model->total_pedido,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan="7"><?= Html::encode($model->observacion) ?></td>
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
            <li role="presentation" class="active"><a href="#listadoreferencias" aria-controls="listadoreferencias" role="tab" data-toggle="tab">Referencias <span class="badge"><?= count($referencias) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="listadoreferencias">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style="font-size: 90%;">
                                        <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Lista de precio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Valor unitario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Subtota</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Impuesto</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Total</th>
                                         <th scope="col" style='background-color:#B9D5CE;'></th>
                                         <th scope="col" style='background-color:#B9D5CE;'></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($referencias as $val): 
                                        $conLista = app\models\ReferenciaListaPrecio::find()->where(['=','codigo', $val->codigo])->all();
                                        $conLista = ArrayHelper::map($conLista, 'id_detalle', 'id_lista');
                                        ?>
                                       <tr style="font-size: 90%;">
                                            <td><?= $val->codigo ?></td>
                                            <td><?= $val->referencia ?></td>
                                            <td style="padding-left: 1;padding-right: 0;"><?= Html::dropDownList('tipo_lista[]', $val->id_detalle, $conLista, ['class' => 'col-sm-12', 'prompt' => 'Seleccione', 'required' => true]) ?></td>
                                            <td style="text-align: right"><?= '$'. number_format($val->valor_unitario,0)?></td>
                                            <td style="text-align: right"><?= ''. number_format($val->cantidad,0)?></td>
                                            <td style="text-align: right"><?= '$'. number_format($val->subtotal,0)?></td>
                                            <td style="text-align: right"><?= '$'. number_format($val->iva,0)?></td>
                                            <td style="text-align: right"><?= '$'. number_format($val->total_linea,0)?></td>
                                            <?php if ($model->autorizado == 0){
                                                if($val->valor_unitario > 0){
                                                    $conTalla = \app\models\PedidoClienteTalla::find()->where(['=','id_referencia', $val->id_referencia])->one(); 
                                                    if(!$conTalla){ ?>
                                                        <td style= 'width: 25px; height: 25px;'>
                                                                 <a href="<?= Url::toRoute(["pedido-cliente/crear_tallas_referencia", "id" => $model->id_pedido, 'token' => $token, 'id_referencia' => $val->id_referencia]) ?>" ><span class="glyphicon glyphicon-import"></span></a>
                                                        </td>
                                                        <td style= 'width: 25px; height: 25px;'></td>
                                                    <?php }else{?>
                                                        <td style= 'width: 25px; height: 25px;'>
                                                                 <a href="<?= Url::toRoute(["pedido-cliente/crear_tallas_referencia", "id" => $model->id_pedido, 'token' => $token, 'id_referencia' => $val->id_referencia]) ?>" ><span class="glyphicon glyphicon-import"></span></a>
                                                        </td>
                                                        <td style= 'width: 25px; height: 25px;'>
                                                                 <a href="<?= Url::toRoute(["pedido-cliente/ver_tallas", "id" => $model->id_pedido, 'token' => $token, 'id_referencia' => $val->id_referencia]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                                        </td>
                                                    <?php }    
                                                }else{?>
                                                    <td style= 'width: 25px; height: 25px;'></td>
                                                    <td style= 'width: 25px; height: 25px;'></td>
                                                <?php }    
                                            }else{?>
                                                     <td style= 'width: 25px; height: 25px;'>
                                                                 <a href="<?= Url::toRoute(["pedido-cliente/ver_tallas", "id" => $model->id_pedido, 'token' => $token, 'id_referencia' => $val->id_referencia]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                                        </td>
                                                    <td style= 'width: 25px; height: 25px;'></td>
                                            <?php }?>        
                                            <input type="hidden" name="listado_referencia[]" value="<?= $val->id_referencia ?>">
                                            <td style="width: 30px;"><input type="checkbox" name="listado_eliminar[]" value="<?= $val->id_referencia ?>"></td>
                                           
                                       </tr>  
                                    <?php endforeach;?>   
                                </<body>
                            </table>
                        </div>
                        <div class="panel-footer text-right"> 
                            <?php 
                            if($model->autorizado == 0){
                                if(count($referencias) == 0){?>

                                        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nueva Referencias', ['pedido-cliente/nueva_referencia_pedido', 'id' => $model->id_pedido, 'token' => $token], ['class' => 'btn btn-success btn-sm']) ?>
                                <?php }else{?>
                                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nueva Referencias', ['pedido-cliente/nueva_referencia_pedido', 'id' => $model->id_pedido, 'token' => $token], ['class' => 'btn btn-success btn-sm']) ?>
                                    <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar", ["class" => "btn btn-danger btn-sm", 'name' => 'eliminar_referencia']) ?>
                                    <?= Html::submitButton("<span class='glyphicon glyphicon-refresh'></span> Actualizar", ["class" => "btn btn-warning btn-sm", 'name' => 'actualizar_linea']) ?>
                                <?php }
                            }?>
                        </div>     
                    </div>    
                </div>
            </div> 
            <!--TERMINA TABS DE OPERACIONES-->
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
