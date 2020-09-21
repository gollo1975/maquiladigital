<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Compra;
use app\models\Proveedor;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Compras';
$this->params['breadcrumbs'][] = $this->title;


?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtro");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista Compras</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("compra/indexconsulta"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$proveedores = ArrayHelper::map(Proveedor::find()->orderBy('nombrecorto ASC')->all(), 'idproveedor', 'nombreProveedores');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, 'idproveedor')->widget(Select2::classname(), [
                'data' => $proveedores,
                'options' => ['prompt' => 'Seleccione un proveedor ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, "numero")->input("search") ?>
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
            <?= $formulario->field($form, 'pendiente')->dropDownList(['1' => 'SI'],['prompt' => 'Seleccione una opcion ...']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("compra/indexconsulta") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros: <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
            <tr>                
                <th scope="col">Factura</th>
                <th scope="col">Cedula/Nit</th>
                <th scope="col">Proveedor</th>
                <th scope="col">Concepto</th>
                <th scope="col">Fecha Inicio</th>
                <th scope="col">F. Vcto</th>
                <th scope="col">Subtotal</th>
                <th scope="col">Saldo</th>
                <th scope="col">Total</th>
                <th scope="col">Aut.</th>
                <th scope="col">Estado</th>
                <th scope="col"></th>                               
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
                <tr style="font-size: 85%;">                
                <td><?= $val->factura ?></td>
                <td><?= $val->proveedor->cedulanit ?></td>
                <td><?= $val->proveedor->nombrecorto ?></td>
                <td><?= $val->compraConcepto->concepto ?></td>
                <td><?= $val->fechainicio ?></td>
                <td><?= $val->fechavencimiento ?></td>
                <td><?= number_format($val->subtotal,0) ?></td>
                <td><?= number_format($val->saldo,0) ?></td>
                <td><?= number_format($val->total,0) ?></td>
                <td><?= $val->autorizar ?></td>
                <td><?= $val->estados ?></td>
                <td style="width: 25px;">				
                <a href="<?= Url::toRoute(["compra/viewconsulta", "id" => $val->id_compra]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                
                </td>
            </tr>
            </tbody>
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >            
            <?php
                $form = ActiveForm::begin([
                            "method" => "post",                            
                        ]);
                ?>    
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>







