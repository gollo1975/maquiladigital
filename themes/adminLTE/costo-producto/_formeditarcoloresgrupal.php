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
/* @var $model app\models\Ordenproduccion */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Editar cantidad';
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
            Registros <span class="badge"><?= count($colores)?></span>
        </div>
        <div class="panel-body">
           <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Color</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha registro</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($colores as $val): ?>
                    <tr style='font-size: 85%;'>
                    <td><?= $val->id_producto_color?></td>    
                    <td><?= $val->color->color   ?></td>
                    <td><?= $val->productoTalla->talla->talla   ?></td>
                    <td><?= $val->usuariosistema   ?></td>
                    <td><?= $val->fecha_registro   ?></td>
                    <td ><input type="text" name="cantidad_color[]" style="text-align: right" value="<?= $val->cantidad_color ?>" required></td>
                    <input type="hidden" name="id_color[]" value="<?= $val->id_producto_color ?>">
                </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['costo-producto/view', 'id' => $id, 'token' => $token], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

