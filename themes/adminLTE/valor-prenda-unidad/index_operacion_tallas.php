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

$this->title = 'Ordenes de produccion';
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
    "action" => Url::toRoute("valor-prenda-unidad/search_operacion_talla"),
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
             <?= $formulario->field($form, "codigoproducto")->input("search") ?>
            <?= $formulario->field($form, "ordenproduccion")->input("search") ?>
            <?= $formulario->field($form, 'desde')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?= $formulario->field($form, 'hasta')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
             <?= $formulario->field($form, 'idcliente')->widget(Select2::classname(), [
                'data' => $clientes,
                'options' => ['prompt' => 'Seleccione un cliente...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        
        
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/search_operacion_talla") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Registros <span class="badge"><?= $pagination->totalCount ?></span>
        </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style="font-size: 90%">
                <th scope="col" style='background-color:#B9D5CE;'>Op Int.</th>
                <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                <th scope="col" style='background-color:#B9D5CE;'>Op Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. llegada</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. procesada</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. entrega</th>
                
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
                <tr style="font-size: 85%;">
                <td><?= $val->idordenproduccion ?></td>
                <td><?= $val->codigoproducto ?></td>
                  <td><?= $val->cantidad ?></td>    
                <td><?= $val->ordenproduccion ?></td>
                <td><?= $val->cliente->nombrecorto ?></td>
                <td><?= date("Y-m-d", strtotime("$val->fechallegada")) ?></td>
                <td><?= date("Y-m-d", strtotime("$val->fechaprocesada")) ?></td>
                <td><?= date("Y-m-d", strtotime("$val->fechaentrega")) ?></td>
                               <td>
                    <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span> ', ['view_operacion_talla', 'id' => $val->idordenproduccion, 'token' => $token] ) ?>
                </td>
            </tr>
            </tbody>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>
