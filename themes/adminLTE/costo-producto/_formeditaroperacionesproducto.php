<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\TiposMaquinas;
use app\models\Ordenproducciontipo;
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
$maquinas = ArrayHelper::map(TiposMaquinas::find()->where(['=','estado', 1])->all(), 'id_tipo', 'descripcion');
$proceso = ArrayHelper::map(Ordenproducciontipo::find()->all(), 'idtipo', 'tipo');
?>
<?php
$this->title = 'Operaciones';
$this->params['breadcrumbs'][] = ['label' => 'Listado', 'url' => ['view', 'id' => $id]];
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
            Registros  <span class="badge"><?= count($mds)?></span>
        </div>
        <div class="panel-body">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Operacion</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Segundos</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Minutos</th>
                       <th scope="col" style='background-color:#B9D5CE;'>Segundos</th>
                    <th scope="col" style='background-color:#B9D5CE; text-align: center;'>Proceso</th>
                     <th scope="col" style='background-color:#B9D5CE; text-align: center;'>Maquina</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($mds as $val): ?>
                        <tr style="font-size: 85%;">
                            <td><?= $val->id_operacion ?></td>
                            <td><?= $val->proceso->proceso ?></td>
                            <td><?= $val->segundos ?></td>
                            <td><?= $val->minutos ?></td>
                            <td><input type="text" name="segundos[]" style="text-align: right;"  value="<?= $val->segundos ?>" required></td>
                            <td><?= Html::dropDownList('proceso[]', $val->idtipo, $proceso, ['class' => 'col-sm-12', 'prompt' => 'Seleccion el proceso']) ?></td>
                            <td><?= Html::dropDownList('id_tipo[]', $val->id_tipo, $maquinas, ['class' => 'col-sm-12', 'prompt' => 'Seleccion la maquina']) ?></td>
                            <input type="hidden" name="id_operacion[]" value="<?= $val->id_operacion ?>">
                        </tr>
                    <?php endforeach; ?>
                </tbody>  
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['costo-producto/view', 'id' => $id], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

