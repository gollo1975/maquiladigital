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
/* @var $model app\models\Ordenproduccion */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Listado de pago';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin([

    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
]); ?>

<div class="table table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Registros <span class="badge"><?= count($detalles)?></span>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Tipo documento</th> 
                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>                        
                    <th scope="col" style='background-color:#B9D5CE;'>Nombres</th>                        
                    <th scope="col" style='background-color:#B9D5CE;'>Tipo transación</th> 
                    <th scope="col" style='background-color:#B9D5CE;'>Codigo banco</th> 
                    <th scope="col" style='background-color:#B9D5CE;'>Banco</th>
                    <th scope="col" style='background-color:#B9D5CE;'>No cuenta</th> 
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha aplicacion</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Vr. pagar</th> 
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $listados):
                        if($tipo_proceso == 7){
                             $operario = \app\models\Operarios::find()->where(['=','documento', $listados->documento])->one();
                            ?>
                            <tr style='font-size: 85%;'>
                                <td><?= $operario->tipoDocumento->descripcion ?></td>
                                <td><?= $listados->documento ?></td>
                                <td><?= $listados->nombres ?></td>
                                <td><?= $operario->tipoTransacion?></td>
                                <td><?= $listados->codigo_banco ?></td>
                                <td><?= $operario->bancoEmpleado->banco ?></td>
                                <td><?= $listados->numero_cuenta ?></td>
                                <td><?= $listados->fecha_aplicacion ?></td>
                                <td><?=''.number_format($listados->valor_transacion,0) ?></td>
                                <td><input type="checkbox" name="seleccion[]" value="<?= $listados->id_detalle ?>"></td>
                            </tr>
                        <?php } else {    
                               $empleado = app\models\Empleado::find()->where(['=','identificacion', $listados->documento])->one();
                            ?>
                            <tr style='font-size: 85%;'>
                                <td><?= $empleado->tipoDocumento->descripcion ?></td>
                                <td><?= $listados->documento ?></td>
                                <td><?= $listados->nombres ?></td>
                                <td><?= $empleado->tipoTransacion?></td>
                                <td><?= $listados->codigo_banco ?></td>
                                <td><?= $empleado->bancoEmpleado->banco ?></td>
                                <td><?= $listados->numero_cuenta ?></td>
                                <td><?= $listados->fecha_aplicacion ?></td>
                                <td><?=''.number_format($listados->valor_transacion,0) ?></td>
                                <td><input type="checkbox" name="seleccion[]" value="<?= $listados->id_detalle ?>"></td>
                            </tr>
                        <?php }    
                    endforeach; ?>
                </tbody>        
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['pago-banco/view', 'id' => $id], ['class' => 'btn btn-primary']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar todo", ["class" => "btn btn-danger",]) ?>
        </div>

    </div>
</div>
<?php ActiveForm::end(); ?>

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