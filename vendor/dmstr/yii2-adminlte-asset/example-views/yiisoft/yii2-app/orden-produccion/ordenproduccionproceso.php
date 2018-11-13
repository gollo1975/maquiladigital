<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use kartik\date\DatePicker;
use kartik\select2\Select2;

$this->title = 'Ordenes de Producción Procesos';
$this->params['breadcrumbs'][] = $this->title;

?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtroproceso");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("orden-produccion/proceso"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-4 form-group">{input}</div>',
        'labelOptions' => ['class' => 'col-sm-2 control-label'],
        'options' => [ 'tag' => false,]
    ],

]);
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>

<div class="panel-body" id="filtroproceso" style="display:none">
        <div class="row">
            <?= $formulario->field($form, 'idcliente')->widget(Select2::classname(), [
                'data' => $clientes,
                'options' => ['prompt' => 'Seleccione un cliente...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'idtipo')->widget(Select2::classname(), [
                'data' => $ordenproducciontipos,
                'options' => ['prompt' => 'Seleccione un cliente...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="row" >
            <?= $formulario->field($form, "ordenproduccion")->input("search") ?>
        </div>

        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("orden-produccion/proceso") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Registros: <?= $pagination->totalCount ?>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">Id Orden</th>
                <th scope="col">Orden Produccion</th>
                <th scope="col">Cliente</th>
                <th scope="col">Fecha Llegada</th>
                <th scope="col">Fecha Procesada</th>
                <th scope="col">Fecha Entrega</th>
                <th scope="col">Tipo</th>
                <th scope="col">Progreso</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
            <tr>
                <td><?= $val->idordenproduccion ?></td>
                <td><?= $val->ordenproduccion ?></td>
                <td><?= $val->cliente->nombrecorto ?></td>
                <td><?= $val->fechallegada ?></td>
                <td><?= $val->fechaprocesada ?></td>
                <td><?= $val->fechaentrega ?></td>
                <td><?= $val->tipo->tipo ?></td>
                <td><div class="progress">
                        <progress id="html5" max="100" value="<?= $val->porcentaje_proceso ?>"></progress>
                        <span><b><?= Html::encode($val->porcentaje_proceso).' %' ?></b></span>
                    </div></td>
                <td>
                    <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span> ', ['view_detalle', 'id' => $val->idordenproduccion] ) ?>
                </td>
            </tr>
            </tbody>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>
