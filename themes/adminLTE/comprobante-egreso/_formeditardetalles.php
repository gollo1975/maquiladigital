<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
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
/* @var $model app\models\ComprobanteEgreso */
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

<div class="modal-body">

    <div class="panel panel-success ">
        <div class="panel-heading">
            Editar detalle.
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th scope="col">Id Compra</th>
                    <th scope="col">Factura</th>
                    <th scope="col">Base Aiu</th>
                    <th scope="col">Rete Fuente</th>
                    <th scope="col">Rete Iva</th>
                    <th scope="col">Valor Abono</th>
                    <th scope="col">Valor Saldo</th>
                   
                </tr>
                </thead>
                <tbody>
                <?php foreach ($mds as $val): ?>
                    <tr style="font-size: 85%;">
                    <td><?= $val->id_compra ?></td>
                    <td><?= $val->compra->factura ?></td>
                    <td><?= $val->base_aiu ?></td>
                    <td><?= $val->retefuente ?></td>
                    <td><?= $val->reteiva ?></td>
                    <td><input type="text" name="vlr_abono[]" value="<?= $val->vlr_abono ?>" required></td>
                    <td><?= $val->vlr_saldo ?></td>
                    <input type="hidden" name="id_comprobante_egreso_detalle[]" value="<?= $val->id_comprobante_egreso_detalle ?>">
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

