<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;


$this->title = 'Proveedor';
$this->params['breadcrumbs'][] = $this->title;


?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtroproveedor");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista proveedor</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("proveedor/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtroproveedor" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "cedulanit")->input("search") ?>
            <?= $formulario->field($form, "nombrecorto")->input("search") ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("proveedor/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros: <?= $pagination->totalCount ?>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
            <tr>                
                <th scope="col">Cedula/Nit</th>
                <th scope="col">Proveedor</th>
                <th scope="col">Teléfono</th>
                <th scope="col">Dirección</th>
                <th scope="col">Municipio</th>
                <th scope="col"></th>                               
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
            <tr>                
                <td><?= $val->cedulanit ?></td>
                <td><?= $val->nombrecorto ?></td>
                <td><?= $val->telefonoproveedor ?></td>
                <td><?= $val->direccionproveedor ?></td>
                <td><?= $val->municipio->municipio ?></td>
                <td>				
                <a href="<?= Url::toRoute(["proveedor/view", "id" => $val->idproveedor]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                <a href="<?= Url::toRoute(["proveedor/editar", "id" => $val->idproveedor])?>" ><span class="glyphicon glyphicon-pencil"></span></a>
		<?= Html::a('', ['eliminar', 'id' => $val->idproveedor], [
        'class' => 'glyphicon glyphicon-trash',
        'data' => [
            'confirm' => 'Esta seguro de eliminar el registro?',
            'method' => 'post',
        ],
    ]) ?>
                </td>
            </tr>
            </tbody>
            <?php endforeach; ?>
        </table>
        <div class="panel-footer text-right" >
            <a align="right" href="<?= Url::toRoute("proveedor/nuevo") ?>" class="btn btn-success"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>