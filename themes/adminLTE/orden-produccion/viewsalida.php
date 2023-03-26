 <?php

//modelos
use app\models\Ordenproducciondetalle;
use app\models\Ordenproduccion;
use app\models\Cliente;
use app\models\Color;
use app\models\Remision;
use app\models\Producto;
use app\models\Productodetalle;
//clase
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\Session;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;


/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$this->title = 'Detalle salida/entrada';
$this->params['breadcrumbs'][] = ['label' => 'salida/entrada', 'url' => ['indexentradasalida']];
$this->params['breadcrumbs'][] = $model->id_salida;
$view = 'orden-produccion/indexentradasalida';
?>

<div class="ordenproduccion-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['indexentradasalida', 'id' => $model->id_salida], ['class' => 'btn btn-primary btn-sm']); ?>
        <?php  if ($model->autorizado == 0) { ?>
                   <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['updatesalida', 'id' => $model->id_salida], ['class' => 'btn btn-success btn-sm']); 
                   if (count($modeldetalles) > 0){?> 
                      <?=  Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizadosalidaentrada', 'id' => $model->id_salida], ['class' => 'btn btn-default btn-sm']);
                   }
           }else {
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizadosalidaentrada', 'id' => $model->id_salida], ['class' => 'btn btn-default btn-sm']);
                echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimirsalida', 'id' => $model->id_salida], ['class' => 'btn btn-default btn-sm']);            
                
        }?>
    <br>
    <br>  
 
    <div class="panel panel-success">
        <div class="panel-heading">
           Registro
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, "Id_salida") ?>:</th>
                    <td><?= Html::encode($model->id_salida) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?>:</th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Op_Cliente') ?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Producto') ?>:</th>
                    <td><?= Html::encode($model->codigo_producto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Proceso') ?>:</th>
                    <td><?= Html::encode($model->tipoProceso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Despacho') ?>:</th>
                    <td><?= Html::encode(''.number_format($model->total_cantidad,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_registro') ?>:</th>
                    <td><?= Html::encode($model->fecha_entrada_salida) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_grabado') ?>:</th>
                    <td><?= Html::encode($model->fecha_proceso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Autorizado') ?>:</th>
                    <td><?= Html::encode($model->autorizadoSalida) ?></td>                    
                </tr>
                <tr style="font-size: 85%;">
                    <th style='-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>  
                    <th style='-color:#F0F3EF;'><?= Html::activeLabel($model, 'Tipo_Entrada') ?>:</th>
                    <td><?= Html::encode($model->tipoentrada->concepto) ?></td> 
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Unidades_Lote') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion->cantidad) ?></td>       
                </tr>
                 <tr style="font-size: 85%;">
                    <th style='-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan="5"><?= Html::encode($model->observacion) ?></td>  
                        
                </tr>
                 <?php 
                 $mensaje = 'Las unidades de entrada no pueden ser mayores que la cantidad del lote.';

                 if($model->total_cantidad > $model->ordenproduccion->cantidad){?>
                     <tr>
                         <td colspan="8" style="background-color: #F5F0D8;"><?=  $mensaje?></td>
                     </tr>  
                   <?php } ?>
            </table>
        </div>
    </div>
  
    <div class="table-responsive">
        <div class="panel panel-success ">
            <div class="panel-heading">
                Detalles de la entrada/salida
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Producto</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                        <th scope="col" style='background-color:#B9D5CE;'></th>
                        <th scope="col" style='background-color:#B9D5CE;'></th>
                           
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($modeldetalles as $val): ?>
                    <tr style="font-size: 85%;">
                          <td><?= $val->consecutivo ?></td>
                          <td><?= $model->codigo_producto ?></td>
                        <td><?= $val->productodetalle->prendatipo->prenda.' / '.$val->productodetalle->prendatipo->talla->talla   ?></td>
                       <td align="right"><?= ''.number_format($val->cantidad,0) ?></td>
                        <?php if ($model->autorizado == 0) { ?>
                        <td style="width: 25px;">
                                <a href="#" data-toggle="modal" data-target="#consecutivo2<?= $val->consecutivo ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                                <!-- Editar modal detalle -->
                                <div class="modal fade" role="dialog" aria-hidden="true" id="consecutivo2<?= $val->consecutivo ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                <h4 class="modal-title">Editar detalle <?= $val->consecutivo ?></h4>
                                            </div>
                                            <?= Html::beginForm(Url::toRoute("orden-produccion/editardetallesalidaunico"), "POST") ?>
                                            <div class="modal-body">
                                                <div class="panel panel-success">
                                                    <div class="panel-heading">
                                                        <h4>Detalle de salida/Entrada</h4>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="col-lg-2">
                                                            <label>Cantidad:</label>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <input type="text" name="cantidad" value="<?= $val->cantidad ?>" class="form-control" required>
                                                        </div>
                                                        <input type="hidden" name="consecutivo" value="<?= $val->consecutivo ?>">
                                                        <input type="hidden" name="id_salida" value="<?= $model->id_salida ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                <button type="submit" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span> Guardar</button>
                                            </div>
                                             <?= Html::endForm() ?>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                        </td>
                        <td style="width: 25px;">
                                <!-- Eliminar modal detalle -->
                                <a href="#" data-toggle="modal" data-target="#iddetalleorden<?= $val->consecutivo ?>"><span class="glyphicon glyphicon-trash"></span></a>
                                <div class="modal fade" role="dialog" aria-hidden="true" id="iddetalleorden<?= $val->consecutivo ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                <h4 class="modal-title">Eliminar Detalle</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Realmente deseas eliminar el registro con código <?= $val->consecutivo ?>?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <?= Html::beginForm(Url::toRoute("orden-produccion/eliminardetallesalida"), "POST") ?>
                                                <input type="hidden" name="consecutivo" value="<?= $val->consecutivo ?>">
                                                <input type="hidden" name="id_salida" value="<?= $model->id_salida ?>">
                                                <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Eliminar</button>
                                                <?= Html::endForm() ?>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            </td>                            
                                                       
                        <?php } else {?>
                            <th style="width: 25px; background-color:#B9D5CE;" scope="col" ></th>
                            <th  style="width: 25px; background-color:#B9D5CE;" scope="col" ></th>
                        <?php }?>
                    </tr>
                    </tbody>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <?php if ($model->autorizado == 0) { ?>
                <div class="panel-footer text-right">
                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['orden-produccion/editardetallesalida', 'id' => $model->id_salida],[ 'class' => 'btn btn-success btn-sm']) ?>                                            
                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nuevo', ['orden-produccion/nuevalinea', 'idordenproduccion' => $model->idordenproduccion,'idcliente' => $model->idcliente, 'id' => $model->id_salida], ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
            
            <?php } ?>
        </div>
    </div>
    
</div>

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