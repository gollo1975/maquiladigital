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
use yii\widgets\LinkPager;

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
            Listado de facturas
        </div>
        <div class="panel-body">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Nro Factura</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Nro Factura Electrónica</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha Inicio</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha Vcto</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Subtotal</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Rete Fuente</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Rete iva</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Iva</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Saldo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Total</th>
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($reciboFactura as $val): ?>
                    <tr style="font-size: 85%;">
                    <td><?= $val->idfactura ?></td>
                    <td><?= $val->nrofactura ?></td>
                    <td><?= $val->nrofacturaelectronica ?></td>
                    <td><?= $val->fecha_inicio ?></td>
                    <td><?= $val->fecha_vencimiento ?></td>
                    <td><?= '$ ' .number_format($val->subtotal,0) ?></td>
                    <td><?= '$ ' .number_format($val->retencionfuente,0) ?></td>
                    <td><?= '$ ' .number_format($val->retencioniva,0) ?></td>
                    <td><?= '$ ' .number_format($val->impuestoiva,0) ?></td>
                    <td><?= '$ ' .number_format($val->saldo,0) ?></td>
                    <td><?= '$ ' .number_format($val->totalpagar,0) ?></td>
                    <td><input type="checkbox" name="idfactura[]" value="<?= $val->idfactura ?>"></td>
                </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['recibocaja/view', 'id' => $idrecibo,  'token' => $token], ['class' => 'btn btn-primary btn-sm']) ?>
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