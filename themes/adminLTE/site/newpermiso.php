<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\UploadedFile;
use app\models\Matriculados;
use app\models\Inscritos;
use app\models\PagosPeriodo;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Session;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use kartik\date\DatePicker;

$this->title = 'Nuevo Permiso';
?>

<?php $form = ActiveForm::begin([

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
            Permisos
        </div>
        <div class="panel-body">
               <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Módulo</th>
                    <th scope="col" style='background-color:#B9D5CE;' >Menú Operación</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Permiso</th>                    
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($permisos as $val): 
                        $permiso = app\models\UsuarioDetalle::find()->where(['=','id_permiso', $val->id_permiso])
                                                                               ->andWhere(['=','codusuario', $id])->one();
                         if($permiso){
                            ?>
                            <tr style="font-size: 85%;">                    
                                <td style='background-color:#E1F1E9;'><?= $val->id_permiso ?></td>
                                <td style='background-color:#E1F1E9;'><?= $val->modulo ?></td>
                                <td style='background-color:#E1F1E9;'><?= $val->menu_operacion ?></td>
                                <td style='background-color:#E1F1E9;'><?= $val->permiso ?></td>                    
                                <td style='background-color:#E1F1E9;'><input type="checkbox" name="idpermiso[]" disabled="false" value="<?= $val->id_permiso ?>"></td>
                            </tr>
                         <?php }else{ ?>    
                            <tr style="font-size: 85%;">                    
                                <td><?= $val->id_permiso ?></td>
                                <td><?= $val->modulo ?></td>
                                <td><?= $val->menu_operacion ?></td>
                                <td><?= $val->permiso ?></td>                    
                                <td><input type="checkbox" name="idpermiso[]" value="<?= $val->id_permiso ?>"></td>
                            </tr>
                    <?php }        
                    endforeach; ?>
                </tbody>        
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['site/view','id' => $id], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
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