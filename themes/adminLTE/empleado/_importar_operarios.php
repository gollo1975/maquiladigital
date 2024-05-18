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

$this->title = 'Listado operarios';
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
            Detalle del operario
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                     <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Departamento</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Municipio</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Celular</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Planta/Bodega</th>
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($operarios as $val):
                        $empleado = app\models\Empleado::find()->where(['=','identificacion',  $val->documento])->one();
                        if($empleado){
                            
                        }else{ ?>
                            <tr style="font-size: 85%;">
                                <td><?= $val->documento ?></td>
                                <td><?= $val->nombrecompleto ?></td>
                                <td><?= $val->departamento->departamento ?></td>
                                <td><?= $val->municipio->municipio ?></td>
                                <td><?= $val->celular?></td>
                                <td><?= $val->planta->nombre_planta?></td>
                                <td style="width: 30px;"><input type="checkbox" name="importar[]" value="<?= $val->documento ?>"></td>
                            </tr>
                        <?php }
                    endforeach; ?>
                </tbody>    
            </table>
        </div>
        <div class="panel-footer text-right"> 
            <?php if ($sw == 0){
                echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['empleado/index'], ['class' => 'btn btn-primary btn-sm']);
            }else{
                echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['proveedor/index'], ['class' => 'btn btn-primary btn-sm']); 
            }    
                echo  Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar", ["class" => "btn btn-success btn-sm",]);?>
                
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