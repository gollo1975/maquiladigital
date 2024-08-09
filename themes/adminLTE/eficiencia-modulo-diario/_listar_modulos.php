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

$this->title = 'Listado de modulos';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute(["eficiencia-modulo-diario/listar_modulos", 'id' => $id, 'id_planta' => $id_planta]),
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
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute(["eficiencia-modulo-diario/listar_modulos", 'id' => $id, 'id_planta' => $id_planta]) ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
            Registros  <span class="badge"><?= count($listado) ?> </span>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Nro balanceo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Op interna</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                       <th scope="col" style='background-color:#B9D5CE;'>Servicio</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Modulo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Hora inicio</th>
                    <th scope="col" style='background-color:#B9D5CE;'><span title="Cantidad de operarios">No Ope.</span></th>
                     <th scope="col" style='background-color:#B9D5CE;'>T. minutos</th>
                    <th scope="col" style='background-color:#B9D5CE;'>T. balanceo</th>                    
                    <th scope="col" style='background-color:#B9D5CE;'>Planta</th> 
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($listado as $val):?>
                        <tr style="font-size: 85%;">
                            <td><?= $val->id_balanceo?></td>
                            <td><?= $val->idordenproduccion?></td>
                            <td><?= $val->ordenproduccion->codigoproducto?></td>
                            <td><?= $val->cliente->nombrecorto ?></td>
                            <?php if($val->id_proceso_confeccion == 1){?>
                                 <td style='background-color:#A1D2D8;'><?= $val->procesoconfeccion->descripcion_proceso ?></td>
                            <?php }else {?>
                                <td style='background-color:#F1E4F4 ;'><?= $val->procesoconfeccion->descripcion_proceso ?></td> 
                            <?php }?>      
                            <td><?= $val->modulo ?></td> 
                            <td><?= $val->fecha_inicio ?></td>
                            <td><?= $val->hora_inicio ?></td>
                            <td><?= $val->cantidad_empleados?></td>
                            <td style="text-align: right"><?= $val->total_minutos ?></td>
                            <td style="text-align: right"><?= $val->tiempo_balanceo ?></td>
                            <td><?= $val->plantaempresa->nombre_planta?></td>
                            <td style="width: 30px;"><input type="checkbox" name="modulo_activo[]" value="<?= $val->id_balanceo ?>"></td>
                            <input type="hidden" name="id_planta" value="<?= $id_planta ?>">
                        </tr>   
  
                    <?php endforeach; ?>
               </tbody>         
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['eficiencia-modulo-diario/view', 'id' => $id, 'id_planta' => $id_planta], ['class' => 'btn btn-primary btn-sm']) ?>
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