<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\GrupoPago;
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

$this->title = 'Intereses';
$this->params['breadcrumbs'][] = $this->title;
$grupo = ArrayHelper::map(GrupoPago::find()->orderBy ('grupo_pago ASC')->all(), 'id_grupo_pago', 'grupo_pago');
?>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute(["pago-adicional-fecha/importinteres", 'id' => $id, 'fecha_corte' => $fecha_corte]),
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
            <?= $formulario->field($form, "documento")->input("search") ?>          
            <?= $formulario->field($form, "id_grupo_pago")->widget(Select2::classname(), [
                'data' => $grupo,
                'options' => ['prompt' => 'Seleccione el grupo...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute(["pago-adicional-fecha/importinteres", 'id' => $id, 'fecha_corte' => $fecha_corte]) ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
            Registros : <span class="badge"><?= count($intereses) ?> </span>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Id_Prog.</th>
                    <th scope="col" style='background-color:#B9D5CE;'><SPAN title="Periodo de pago">Per.</SPAN></th></th>
                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Grupo pago</th>                    
                    <th scope="col" style='background-color:#B9D5CE;'>F. Inicio</th>
                    <th scope="col" style='background-color:#B9D5CE;'>F. Corte</th>
                    <th scope="col" style='background-color:#B9D5CE;'>F. Creación</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Vr. Cesantia</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Vr. Interes</th>
                     <th scope="col" style='background-color:#B9D5CE;'><SPAN title="Muestra si el dato fue enviado a pago por fechas">Env.</SPAN></th></th>
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($intereses as $val): ?>
                    <tr style="font-size: 85%;">
                    <td><?= $val->id_programacion ?></td>
                    <td><?= $val->id_periodo_pago_nomina ?></td>
                    <td><?= $val->documento ?></td>
                    <td><?= $val->empleado->nombrecorto ?></td>
                    <td><?= $val->grupoPago->grupo_pago ?></td>
                    <td><?= $val->fecha_inicio ?></td>
                    <td><?= $val->fecha_corte ?></td>
                    <td><?= $val->fecha_creacion ?></td>
                    <td style="text-align: right"><?= '$'.number_format($val->vlr_cesantia,0) ?></td>
                    <td style="text-align: right"><?= '$'.number_format($val->vlr_intereses,0) ?></td>
                    <td><?= $val->enviadodato ?></td>
                    <input type="hidden" name="fecha_corte" value="<?= $fecha_corte ?>">
                    <td style="width: 30px;"><input type="checkbox" name="id_interes[]" value="<?= $val->id_interes ?>"></td>
                </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['pago-adicional-fecha/view', 'id' => $id, 'fecha_corte' => $fecha_corte], ['class' => 'btn btn-primary btn-sm']) ?>
             <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Cerrar intereses", ["class" => "btn btn-warning btn-sm", 'name' => 'enviarexportado']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar a pago", ["class" => "btn btn-success btn-sm",'name' => 'enviardatos']) ?>
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