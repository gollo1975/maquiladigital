<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Cliente;
use app\models\Ordenproducciontipo;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Trazabilidad de OP';
$this->params['breadcrumbs'][] = $this->title;

?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtro");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("orden-produccion/trazabilidad_ordenes"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$clientes = ArrayHelper::map(Cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombreClientes');
$tipos = ArrayHelper::map(Ordenproducciontipo::find()->all(), 'idtipo', 'tipo');
$tipoProducto = ArrayHelper::map(app\models\TipoProducto::find()->orderBy('concepto ASC')->all(), 'id_tipo_producto', 'concepto');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
            <?= $formulario->field($form, 'idcliente')->widget(Select2::classname(), [
                'data' => $clientes,
                'options' => ['prompt' => 'Seleccione un cliente ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, "codigoproducto")->input("search") ?>
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
            <?= $formulario->field($form, 'ordenproduccionint')->widget(Select2::classname(), [
                'data' => $tipoProducto,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, "ordenproduccioncliente")->input("search") ?>
            <?= $formulario->field($form, 'tipo')->dropDownList($tipos, ['prompt' => 'Seleccione un tipo...']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("orden-produccion/trazabilidad_ordenes") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros  <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
            <tr style="font-size: 85%;">                
                <th style='background-color:#B9D5CE;' scope="col">Op interna</th>
                 <th style='background-color:#B9D5CE;' scope="col">Op cliente</th>
                <th style='background-color:#B9D5CE;' scope="col">Nombre del cliente</th>
                <th style='background-color:#B9D5CE;' scope="col">Referencia</th>                
                <th style='background-color:#B9D5CE;' scope="col">Unidades</th>
                <th style='background-color:#B9D5CE;' scope="col">Sam</th>
                <th style='background-color:#B9D5CE;' scope="col">F. llegada</th>
                <th style='background-color:#B9D5CE;' scope="col">F. salida</th>
                <th style='background-color:#B9D5CE;' scope="col"><span title="Grupo de producto">Linea</span></th>
                <th style='background-color:#B9D5CE;' scope="col"><span title="Grupo de producto">Fact.</span></th>
                <th style='background-color:#B9D5CE; width: 150px;' scope="col">Tipo servicio</th>
                <th style='background-color:#B9D5CE;' scope="col" colspan="4"></th>                               
               
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
            <tr style="font-size: 85%;">                
                <td><?= $val->idordenproduccion ?></td>
                 <td><?= $val->ordenproduccion ?></td>
                <td><?= $val->cliente->nombrecorto ?></td>
                <td><?= $val->codigoproducto ?></td>
                <td style="text-align:right"><?= ''. number_format($val->cantidad,0) ?></td>
                <td><?= $val->duracion ?></td>
                <td><?= date("Y-m-d", strtotime("$val->fechallegada")) ?></td>
                 <td><?= date("Y-m-d", strtotime("$val->fechaentrega")) ?></td>
                <?php if($val->id_tipo_producto <> null){?>
                    <td><?= $val->tipoProducto->linea ?></td>
                <?php }else{?>
                    <td><?= 'No found' ?></td>
                <?php }?>  
                <td><?= $val->facturar?></td>
                <td style='background-color:<?= $val->tipo->color?>; color: black;'><?= $val->tipo->tipo ?></td>
               <td style="width: 20px; height: 20px">				
                     <a href="<?= Url::toRoute(["orden-produccion/vista_trazabilidad", "id" => $val->idordenproduccion]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                
                </td>
                
            </tr>
            </tbody>
            <?php endforeach; ?>
        </table>    
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>







