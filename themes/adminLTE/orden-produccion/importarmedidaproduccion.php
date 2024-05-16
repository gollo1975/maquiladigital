<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
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
/* @var $model app\models\Facturaventadetalle */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Medidas (Ficha tecnica)';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute(["orden-produccion/importarmedidapiloto", 'id' => $id, 'iddetalle' => $iddetalle]),
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
	
    <div class="panel-body" id="buscarmaquina">
        <div class="row" >
            <?= $formulario->field($form, "q")->input("search") ?>            
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute(["orden-produccion/importarmedidapiloto", 'id' => $id, 'iddetalle' => $iddetalle]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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

<?php
if ($mensaje != ""){
    ?> <div class="alert alert-danger"><?= $mensaje ?></div> <?php
}
?>

<div class="table table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Registros : <span class="badge"><?= count($pilotoDetalle) ?> </span>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <?php if($orden->proceso_lavanderia == 0 && $orden->lavanderia == 1){?>
                        <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Descripción</th>
                         <th scope="col" style='background-color:#B9D5CE;'>Medida ficha tecnica</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Medida confección</th>                    
                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                    <?php }else{?>
                        <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Descripción</th>
                         <th scope="col" style='background-color:#B9D5CE;'>Medida ficha tecnica</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Medida confección</th>                    
                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                    <?php } ?>    
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($pilotoDetalle as $val):
                        if($orden->proceso_lavanderia == 0 && $orden->lavanderia == 1){ ?>
                            <tr style="font-size: 85%;">
                                <td><?= $val->id_proceso ?></td>
                                <td><?= $val->concepto ?></td>
                                <td><?= $val->medida_ficha_al ?></td>
                                <td><?= $val->medida_confeccion_al ?></td>                   
                                <td style="width: 30px;"><input type="checkbox" name="id_proceso[]" value="<?= $val->id_proceso ?>"></td>
                            </tr>
                        <?php }else{?>
                            <tr style="font-size: 85%;">
                                <td><?= $val->id_proceso ?></td>
                                <td><?= $val->concepto ?></td>
                                <td><?= $val->medida_ficha_dl ?></td>
                                <td><?= $val->medida_confeccion_dl ?></td>                   
                                <td style="width: 30px;"><input type="checkbox" name="id_proceso[]" value="<?= $val->id_proceso ?>"></td>
                            </tr>
                        <?php }?>    
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="panel-footer text-right">
                <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['orden-produccion/newpilotoproduccion', 'id' => $id, 'iddetalle' => $iddetalle], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>

    </div>
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