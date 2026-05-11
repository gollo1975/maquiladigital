<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use kartik\select2\Select2;

$this->title = 'Actualizar Sam';
$this->params['breadcrumbs'][] = $this->title;
?>

<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtroproceso");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!-- FORMULARIO DE BÚSQUEDA (GET) -->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("orden-produccion/aplicar_sam_ordenes"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-4 form-group">{input}</div>',
        'labelOptions' => ['class' => 'col-sm-2 control-label'],
        'options' => [ 'tag' => false,]
    ],
]); ?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()" style="cursor:pointer;">
        Filtros de búsqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
    <div class="panel-body" id="filtroproceso" style="display:block">
        <div class="row">
            <?= $formulario->field($form, 'idproceso')->widget(Select2::classname(), [
                'data' => $operaciones,
                'options' => ['prompt' => 'Seleccione la operación...'],
                'pluginOptions' => ['allowClear' => true],
            ]); ?>
            <?= $formulario->field($form, "nuevo_sam")->textInput(['type' => 'number', 'step' => '0.01']) ?>
            <?= $formulario->field($form, 'totalRegistro')->dropDownList(['10' => '10', '20' => '20', '50' => '50','100' => '100']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm"]) ?>
            <a href="<?= Url::toRoute("orden-produccion/aplicar_sam_ordenes") ?>" class="btn btn-default btn-sm"><span class='glyphicon glyphicon-refresh'></span> Limpiar</a>
        </div>
    </div>
</div>
<?php $formulario->end() ?>

<!-- FORMULARIO DE ACCIÓN MASIVA (POST) -->
<?php $formPost = ActiveForm::begin([
    "method" => "post",
    "action" => Url::toRoute(["orden-produccion/aplicar_sam_ordenes", 'FormFiltroConsultaOperaciones' => Yii::$app->request->get('FormFiltroConsultaOperaciones')]),
]); ?>

<div class="table-responsive">
    <div class="panel panel-success">
        <div class="panel-heading">
            Registros <span class="badge"><?= isset($model) ? count($model) : 0 ?></span>
        </div>
        
        <table class="table table-bordered table-hover">
            <thead>
                <tr style="font-size: 85%;">
                    <th style='background-color:#B9D5CE;'>Op Interna</th>  
                    <th style='background-color:#B9D5CE;'>Op Cliente</th>
                    <th style='background-color:#B9D5CE;'>Referencia</th>
                    <th style='background-color:#B9D5CE;'>Cliente</th>
                    <th style='background-color:#B9D5CE;'>Servicio</th>
                    <th style='background-color:#B9D5CE;'>Producto</th>
                    <th style='background-color:#B9D5CE; text-align:center;'><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($model)): ?>
                    <?php foreach ($model as $val): ?>
                        <tr style="font-size: 85%;">
                            <td><?= $val->idordenproduccion ?></td>
                            <td><?= $val->ordenproduccion ?></td>
                            <td><?= $val->codigoproducto ?></td>
                            <td><?= $val->cliente->nombrecorto ?></td>
                            <td style="background-color: <?= $val->tipo->color ?>; color: black;"><?= $val->tipo->tipo ?></td>
                            <td><?= $val->tipoProducto->concepto ?></td>
                            <td style='text-align:center;'>
                                <!-- Importante: name="listado_ordenes[]" -->
                                <input type="checkbox" name="listado_ordenes[]" value="<?= $val->idordenproduccion ?>">
                            </td>     
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">No se encontraron resultados.</td></tr>
                <?php endif; ?>
            </tbody>                
        </table>
        
       <div class="panel-footer text-right">
            <?php if (!empty($model)): ?>
                <?= Html::submitButton("<span class='glyphicon glyphicon-plus'></span> Actualizar SAM", [
                    "class" => "btn btn-success btn-sm", 
                    'name' => 'actualizar_nuevo_sam',
                    'data' => [
                        // Solo dejamos la confirmación
                        'confirm' => '¿Está seguro de aplicar el nuevo SAM a las órdenes seleccionadas?',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $formPost->end() ?>

<?php if (!empty($pagination)): ?>
    <?= LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>

<script type="text/javascript">
	function marcar(source) {
		checkboxes = document.getElementsByName('listado_ordenes[]');
		for(i=0; i<checkboxes.length; i++) {
			checkboxes[i].checked = source.checked;
		}
	}
</script>