<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Facturaventatipo */

$this->title = 'Detalle Factura Venta Tipo';
$this->params['breadcrumbs'][] = ['label' => 'Facturas Venta Tipos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_factura_venta_tipo;
?>
<div class="factura_venta_tipo-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->id_factura_venta_tipo], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_factura_venta_tipo], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->id_factura_venta_tipo], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Esta seguro de eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Factura Venta Tipo
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th><?= Html::activeLabel($model, 'id_factura_venta_tipo') ?>:</th>
                    <td><?= Html::encode($model->id_factura_venta_tipo) ?></td>
                    <th><?= Html::activeLabel($model, 'concepto') ?>:</th>
                    <td><?= Html::encode($model->concepto) ?></td>
                    <th><?= Html::activeLabel($model, 'activo') ?>:</th>
                    <td><?= Html::encode($model->estados) ?></td>
                    <th><?= Html::activeLabel($model, 'porcentaje_retefuente') ?>:</th>
                    <td><?= Html::encode($model->porcentaje_retefuente) ?></td>                    
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
    <div class="table-responsive">
        <div class="panel panel-success ">
            <div class="panel-heading">
                Cuentas:  <?= $registros ?>
            </div>
            <div class="panel-body">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Cuenta</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Base</th>
                        <th scope="col">Subtotal</th>
                        <th scope="col">Iva</th>
                        <th scope="col">Rete Fuente</th>
                        <th scope="col">Rete Iva</th>
                        <th scope="col">Total Fact</th>
                        <th scope="col">Base % Rete Fte</th>
                        <th scope="col">% Base</th>
                        <th></th>
                        <th scope="col"><input type="checkbox" onclick="marcar(this);"/></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($modeldetalles as $val): ?>
                    <tr>
                        <?php if ($val->tipocuenta == 1){$tipo = "DEBITO";}else{$tipo = "CREDITO";} ?>                        
                        <?php if ($val->base == 0){$base = "NO";}else{$base = "SI";} ?>
                        <?php if ($val->subtotal == 0){$subtotal = "NO";}else{$subtotal = "SI";} ?>
                        <?php if ($val->iva == 0){$iva = "NO";}else{$iva = "SI";} ?>
                        <?php if ($val->rete_fuente == 0){$retefuente = "NO";}else{$retefuente = "SI";} ?>
                        <?php if ($val->rete_iva == 0){$reteiva = "NO";}else{$reteiva = "SI";} ?>
                        <?php if ($val->total == 0){$total = "NO";}else{$total = "SI";} ?>
                        <?php if ($val->base_rete_fuente == 0){$base_rete_fuente = "NO";}else{$base_rete_fuente = "SI";} ?>
                        <td><?= $val->id_factura_venta_tipo_cuenta ?></td>                        
                        <td><?= $val->cuenta ?></td>
                        <td><?= $tipo ?></td>
                        <td><?= $base ?></td>
                        <td><?= $subtotal ?></td>
                        <td><?= $iva ?></td>
                        <td><?= $retefuente ?></td>
                        <td><?= $reteiva ?></td>
                        <td><?= $total ?></td>
                        <td><?= $base_rete_fuente ?></td>
                        <td><?= $val->porcentaje_base ?></td>
                        <td>
                            <a href="<?= Url::toRoute(["facturaventatipo/editardetalle", "id_factura_venta_tipo_cuenta" => $val->id_factura_venta_tipo_cuenta]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                        </td>
                        <td><input type="checkbox" name="id_factura_venta_tipo_cuenta[]" value="<?= $val->id_factura_venta_tipo_cuenta ?>"></td>
                    </tr>
                    </tbody>
                    <?php endforeach; ?>
                </table>
            </div>            
                <div class="panel-footer text-right">
                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nuevo', ['facturaventatipo/nuevodetalles', 'id_factura_venta_tipo' => $model->id_factura_venta_tipo], ['class' => 'btn btn-success']) ?>                    
                    <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar", ["class" => "btn btn-danger", 'name' => 'eliminar']) ?>
                </div>            
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