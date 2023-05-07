<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\Recibocaja;
use app\models\Recibocajadetalle;
use app\models\ReciboCajaSearch;
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

<div class="modal-body">

    <div class="panel panel-success ">
        <div class="panel-heading">
            Editar registro
        </div>
        <div class="panel-body">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Nro Factura</th>
                    <th scope="col">Factura electronica</th>
                    <th scope="col">Rete Fuente</th>
                    <th scope="col">Rete Iva</th>
                    <th scope="col">Valor Abono</th>
                    <th scope="col">Valor Saldo</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($mds as $val): ?>
                    <tr style="font-size: 85%;">
                    <td><?= $val->iddetallerecibo ?></td>
                    <td><?= $val->factura->nrofactura ?></td>
                    <td><?= $val->nrofacturaelectronica ?></td>
                    <td><?= '$'.number_format($val->retefuente,0) ?></td>
                    <td><?= '$'.number_format($val->reteiva,0) ?></td>
                    <td><input type="text" name="vlrabono[]" value="<?= $val->vlrsaldo ?>" required></td>
                    <td><?= '$'.number_format($val->vlrsaldo,0) ?></td>
                    <td><input type="hidden" name="iddetallerecibo[]" value="<?= $val->iddetallerecibo ?>"></td>
                </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['recibocaja/view', 'id' => $idrecibo, 'token' => $token], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

