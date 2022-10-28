<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\Prendatipo;
use app\models\Producto;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\web\Session;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */
/* @var $model app\models\Facturaventadetalle */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Listado prendas';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute(["producto/nuevodetalles", 'idproducto' => $idproducto]),
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
    <div class="panel-heading">
        Parametros de entrada
    </div>
	
    <div class="panel-body" id="costodiario">
        <div class="row" >
            <?= $formulario->field($form, "q")->input("search") ?>            
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute(["producto/nuevodetalles", 'idproducto' => $idproducto]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<?php
 $form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
        ]); ?>

<?php
if ($mensaje != ""){
    ?> <div class="alert alert-danger"><?= $mensaje ?></div> <?php
}
?>

<div class="table table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
           Prendas <span class="badge"><?= $pagination->totalCount ?></span>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th scope="col" scope="col" style='background-color:#B9D5CE;'>Id</th>
                    <th scope="col" scope="col" style='background-color:#B9D5CE;'>Prenda</th>
                    <th scope="col" scope="col" style='background-color:#B9D5CE;'>Talla</th>                    
                    <th scope="col" scope="col" style='background-color:#B9D5CE; width: 40px;'><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($prendas as $val): ?>
                <tr>
                    <td><?= $val->idprendatipo ?></td>
                    <td><?= $val->prenda ?></td>
                    <td><?= $val->talla->talla ?></td>                    
                    <td><input type="checkbox" name="idprendatipo[]" value="<?= $val->idprendatipo ?>"></td>
                </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['producto/view', 'id' => $idproducto], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
    <?= LinkPager::widget(['pagination' => $pagination]) ?>
</div>

<?php $form->end() ?>    

<script type="text/javascript">
	function marcar(source) 
	{
		checkboxes=document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
		for(i=0;i<checkboxes.length;i++) //recoremos todos los controles
		{
			if(checkboxes[i].type == "checkbox") //solo si es un checkbox entramos
			{
				checkboxes[i].checked=source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
			}
		}
	}
</script>