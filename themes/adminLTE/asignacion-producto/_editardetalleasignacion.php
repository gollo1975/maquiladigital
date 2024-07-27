<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Session;
use yii\data\Pagination;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
$this->title = 'Editar detalle';
$this->params['breadcrumbs'][] = ['label' => 'Asignación', 'url' => ['view', 'id' => $id]];
$this->params['breadcrumbs'][] = $this->title;
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
            Registros <span class="badge"><?= count($detalle)?></span>
        </div>
        <div class="panel-body">
           <table class="table table-bordered table-hover">
                <thead>
                <tr style ='font-size: 90%;'>
                    <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Producto</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                     <th scope="col" style='background-color:#B9D5CE;'>Sam</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Vl. minuto</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Subtotal</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($detalle as $val): ?>
               <tr style ='font-size: 90%;'>
                    <td><?= $val->codigo_producto ?></td>
                    <td><?= $val->referencia ?></td>
                    <td><?=$val->talla->talla ?></td>
                     <td><input type="text" style="text-align: right;" name="sam[]" value="<?= $val->tiempo_confeccion ?>" required></td>
                    <td><input type="text" style="text-align: right;" name="cantidad[]" value="<?= $val->cantidad ?>" required></td>
                    <td style="text-align: right;"><?= ''.number_format($val->valor_minuto,0) ?></td>
                    <td style="text-align: right;"><?= ''.number_format($val->subtotal_producto,0) ?></td>
                    <input type="hidden" name="id_detalle[]" value="<?= $val->id_detalle_asignacion ?>">
                </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['asignacion-producto/view', 'id' => $id, 'token' => $token], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

