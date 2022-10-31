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
$this->title = 'Nuevo Detalles';
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

<div class="table table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Listado de tallas
        </div>
        <div class="panel-body">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Producto</th>
                    <th scope="col">Referencia</th>
                    <th scope="col">Unidades</th>                    
                    <th scope="col">Valor unidad</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($productosCliente as $val): ?>
                 <tr style="font-size: 95%;">
                    <td><?= $val->idproductodetalle ?></td>
                    <td><?= $val->prendatipo->prenda.' - '.$val->prendatipo->talla->talla ?></td>                                                           
                    <td> <input type="text" name="codigoproducto[]" value="<?= $ordenProduccion->codigoproducto ?>" readonly="true"></td>
                    <td><input type="text" style="text-align: right" name="cantidad[]" value="0" required> </td>
                    <td><input type="text" style="text-align: right" name="vlrventa[]" value="0" required></td>                    
                    <td><input type="hidden" name="idproductodetalle[]" value="<?= $val->idproductodetalle ?>"></td>
                </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['orden-produccion/view', 'id' => $idordenproduccion], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>

    </div>
</div>
<?php ActiveForm::end(); ?>

