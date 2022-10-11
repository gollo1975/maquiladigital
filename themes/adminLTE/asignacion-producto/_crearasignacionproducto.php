<?php
//modelos

//clases
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use app\models\AsignacionProductoDetalle;
?>
<?php $form = ActiveForm::begin([

    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
]); ?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"></h4>
</div>
<div class="modal-body">        
    <div class="table table-responsive">
        <div class="panel panel-success ">
            <div class="panel-heading">
                Registros <span class="badge"><?= count($productos)?></span>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                           <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                           <th scope="col" style='background-color:#B9D5CE;'>Producto</th>
                           <th scope="col" style='background-color:#B9D5CE;'>Tipo producto </th>
                            <th scope="col" style='background-color:#B9D5CE;'>Unidades </th>
                            <th scope="col" style='background-color:#B9D5CE;'>Fecha creación </th>
                            <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                          <th scope="col" style='background-color:#B9D5CE;'></th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        foreach ($productos as $val):
                            $detalle = AsignacionProductoDetalle::find()->where(['=','id_asignacion', $val->id_producto])->one();
                            if(!$detalle){ ?>
                                <tr style ='font-size:90%;'>
                                    <td><?= $val->codigo_producto ?></td>
                                    <td><?= $val->descripcion ?></td>
                                    <td><?= $val->tipoProducto->concepto ?></td>
                                    <td><?= $val->cantidad ?></td>
                                    <td><?= $val->fecha_creacion ?></td>
                                     <td><?= $val->usuariosistema ?></td>
                                    <td style="width: 30px;"><input type="checkbox" name="id_producto[]" value="<?= $val->id_producto ?>"></td>
                                    <input type="hidden" name="id" value="<?= $id?>">
                                </tr>
                            <?php }    
                        endforeach; ?>
                   </tbody>     
                </table>
            </div>   
            <div class="panel-footer text-right">			
                <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar", ["class" => "btn btn-primary btn-sm", 'name' => 'productoasignado']) ?>                    
            </div>
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