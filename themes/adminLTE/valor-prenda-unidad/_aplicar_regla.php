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

/* @var $this yii\web\View */
/* @var $model app\models\Facturaventadetalle */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Listado confección';
$this->params['breadcrumbs'][] = $this->title;

?>
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
            Registros : <span class="badge"><?= count($valores) ?> </span>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                     <th scope="col" style='background-color:#B9D5CE;'>id</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Dia pago</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Vr. Prenda</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Vlr_Pagar.</th>
                     <th scope="col" style='background-color:#B9D5CE;'>Eficiencia</th> 
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($valores as $val): ?>
                    <tr style="font-size: 85%;">
                        <td><?= $val->consecutivo ?></td>
                    <td><?= $val->operarioProduccion->nombrecompleto ?></td>
                    <td><?= $val->dia_pago ?></td>
                    <td><?= $val->cantidad ?></td>
                   <td style="text-align: right"><?= '$'.number_format($val->vlr_prenda,0) ?></td>
                    <td style="text-align: right"><?= '$'.number_format($val->vlr_pago,0) ?></td>
                    <td style="text-align: right"><?= $val->porcentaje_cumplimiento ?>%</td>
                    <td style="width: 30px;"><input type="checkbox" name="consecutivo[]" value="<?= $val->consecutivo ?>"></td>
                </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['valor-prenda-unidad/view', 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta], ['class' => 'btn btn-primary btn-sm']) ?>
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