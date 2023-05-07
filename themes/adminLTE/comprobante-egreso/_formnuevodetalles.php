<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\web\Session;
use yii\data\Pagination;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $model app\models\Facturaventadetalle */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([

    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
]); ?>

<?php
if ($mensaje != ""){
    ?> <div class="alert alert-danger"><?= $mensaje ?></div> <?php
}
?>

<div class="table table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Nuevo detalle
        </div>
        <div class="panel-body">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th scope="col">Nro Compra</th>
                    <th scope="col">Factura</th>
                    <th scope="col">Concepto</th>
                    <th scope="col">Fecha Inicio</th>
                    <th scope="col">Fecha Vcto</th>
                    <th scope="col">Subtotal</th>
                    <th scope="col">Base Aiu</th>
                    <th scope="col">Rete Fuente</th>
                    <th scope="col">Rete iva</th>
                    <th scope="col">Iva</th>
                    <th scope="col">Saldo</th>
                    <th scope="col">Total</th>
                    <th scope="col"><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($compraegreso as $val): ?>
                    <tr style="font-size: 85%;">
                    <td><?= $val->numero ?></td>
                    <td><?= $val->factura ?></td>
                    <td><?= $val->compraConcepto->concepto ?></td>
                    <td><?= $val->fechainicio ?></td>
                    <td><?= $val->fechavencimiento ?></td>
                    <td><?= '$ ' .number_format($val->subtotal,0) ?></td>
                    <td><?= '$ ' .number_format($val->base_aiu,0) ?></td>
                    <td><?= '$ ' .number_format($val->retencionfuente,0) ?></td>
                    <td><?= '$ ' .number_format($val->retencioniva,0) ?></td>
                    <td><?= '$ ' .number_format($val->impuestoiva,0) ?></td>
                    <td><?= '$ ' .number_format($val->saldo,0) ?></td>
                    <td><?= '$ ' .number_format($val->total,0) ?></td>
                    <td><input type="checkbox" name="id_compra[]" value="<?= $val->id_compra ?>"></td>
                </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['comprobante-egreso/view', 'id' => $id_comprobante_egreso, 'token' => $token], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
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