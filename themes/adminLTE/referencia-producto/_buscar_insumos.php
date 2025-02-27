<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\MaquinaOperario;
use app\models\Tipo;
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

$this->title = 'Listado de insumos';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute(["referencia-producto/search_insumos", 'id' => $id]),
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
	
    <div class="panel-body" id="buscarreferencias">
        <div class="row" >
            <?= $formulario->field($form, "codigo")->input("search") ?>  
            <?= $formulario->field($form, "nombre_producto")->input("search") ?>  
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute(["referencia-producto/search_insumos", 'id' => $id]) ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<?php
$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-2 control-label'],
                'options' => []
            ],
        ]);
?>

<div class="table table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Registros : <span class="badge"><?= count($insumos) ?> </span>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Nombre del insumo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Grupo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Stock</th>
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($insumos as $val): ?>
                        <tr style="font-size: 85%;">
                            <td><?= $val->codigo_insumo ?></td>
                            <td><?= $val->descripcion ?></td>
                            <td><?= $val->grupo->nombre_grupo ?></td>
                            <td style="text-align: right"><?= $val->stock_real ?></td>
                            <td style="width: 30px;"><input type="checkbox" name="codigo_insumo[]" value="<?= $val->id_insumos ?>"></td>

                        </tr>

                    <?php endforeach; ?>
                </tbody>    
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['referencia-producto/view', 'id' => $id], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-send'></span> Enviar", ["class" => "btn btn-success btn-sm", 'name' => 'enviar_insumos']) ?>
        </div>

    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>
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