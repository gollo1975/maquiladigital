<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\Ordenproduccion;
use app\models\Producto;
use app\models\Ordenproducciondetalle;
use app\models\OrdenproduccionSearch;
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
<?php
$this->title = 'Ordenar operaciones';
$this->params['breadcrumbs'][] = ['label' => 'Detalle', 'url' => ['view', 'id' => $idordenproduccion]];
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
            Orden operaciones
        </div>
        <div class="panel-body">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Operacion</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Op</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Segundos</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Minutos</th>
                     <th scope="col" style='background-color:#B9D5CE;'>Maquina</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Orden aleatorio</th>
                    <th scope="col" style='background-color:#B9D5CE;'></th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($mds as $val): ?>
                        <tr style="font-size: 85%;">
                            <td><?= $val->id ?></td>
                            <td><?= $val->proceso->proceso ?></td>
                            <td><?= $val->idordenproduccion ?></td>
                            <td><?= $val->segundos ?></td>
                            <td><?= $val->minutos ?></td>
                             <td><?= $val->tipomaquina->descripcion ?></td>
                            <td><input type="text" name="orden_aleatorio[]" value="<?= $val->orden_aleatorio ?>" required></td>
                            <td><input type="hidden" name="id[]" value="<?= $val->id ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>  
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['orden-produccion/view_balanceo', 'id' => $idordenproduccion], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
