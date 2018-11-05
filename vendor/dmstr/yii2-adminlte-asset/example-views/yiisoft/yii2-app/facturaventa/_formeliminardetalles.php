<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\Facturaventa;
use app\models\Producto;
use app\models\Facturaventadetalle;
use app\models\FacturaventaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\web\Session;
use yii\data\Pagination;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */
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
            Eliminar detalle Factura Venta
        </div>
        <div class="panel-body">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Producto</th>
                    <th scope="col">Código</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Costo</th>

                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($mds as $val): ?>
                <tr>
                    <td><?= $val->iddetallefactura ?></td>
                    <td><?= $val->producto->producto ?></td>
                    <td><?= $val->codigoproducto ?></td>
                    <td><?= $val->cantidad ?></td>
                    <td><?= $val->preciounitario ?></td>
                    <td><?= $val->total ?></td>
                    <td><input type="checkbox" name="seleccion[]" value="<?= $val->iddetallefactura ?>"></td>
                </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['facturaventa/view', 'id' => $idfactura], ['class' => 'btn btn-primary']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar", ["class" => "btn btn-danger",]) ?>
        </div>

    </div>
</div>
<?php ActiveForm::end(); ?>

