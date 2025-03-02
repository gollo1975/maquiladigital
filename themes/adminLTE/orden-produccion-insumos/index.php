<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

//models
use app\models\TipoProducto;
$this->title = 'Ordenes de insumos';
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
    "action" => Url::toRoute("orden-produccion-insumos/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$tipoPrenda = ArrayHelper::map(TipoProducto::find()->orderBy('concepto ASC')->all(), 'id_tipo_producto', 'concepto');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
            <?= $formulario->field($form, "op_cliente")->input("search") ?>
            <?= $formulario->field($form, "op_interna")->input("search") ?>
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
            <?= $formulario->field($form, 'tipo_orden')->widget(Select2::classname(), [
                'data' => $tiposOrden,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, "numero_orden")->input("search") ?>
            <?= $formulario->field($form, "referencia")->input("search") ?>
            
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("orden-produccion-insumos/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
            <tr style='font-size:85%;'align="center" >     
                <th scope="col" style='background-color:#B9D5CE;'>Numero orden</th>
                <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                <th scope="col" style='background-color:#B9D5CE;'>Op interna</th>
                <th scope="col" style='background-color:#B9D5CE;'>Op cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. orden</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>Tipo orden</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total insumo</th>
                <th scope="col" style='background-color:#B9D5CE;'></th> 
              
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
                <tr style='font-size:84%;'>   
                    <td><?= $val->numero_orden ?></td>
                    <td><?= $val->codigo_producto ?></td>
                    <td><?= $val->idordenproduccion ?></td>
                    <td><?= $val->orden_produccion_cliente ?></td>
                    <td> <?= $val->fecha_creada ?></td>
                    <td><?= $val->ordenproduccion->cliente->nombrecorto ?></td>
                    <td><?= $val->tipo->tipo ?></td>
                    <td align="right"><?= number_format($val->total_insumos,0) ?></td>
                    <td style= 'width: 25px;'>				
                       <a href="<?= Url::toRoute(["orden-produccion-insumos/view", "id" => $val->id_entrega]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                
                    </td>
                   
                   
                </tr>
            </tbody>
            <?php endforeach; ?>
        </table>    
        
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>







