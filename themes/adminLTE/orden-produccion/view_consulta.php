<?php


use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Ordenproducciondetalle;
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
use app\models\Ordenproduccion;
use app\models\Cliente;
use app\models\Color;
use app\models\Remision;
use app\models\Producto;
use app\models\Productodetalle;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;


/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$this->title = 'Orden de producción';
$this->params['breadcrumbs'][] = ['label' => 'Consulta', 'url' => ['indexconsulta']];
$this->params['breadcrumbs'][] = $model->idordenproduccion;
$view = 'orden-produccion';
?>

<?php
    $remision = Remision::find()->where(['=', 'idordenproduccion', $model->idordenproduccion])->one();
?>

<div class="ordenproduccion-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['indexconsulta', 'id' => $model->idordenproduccion], ['class' => 'btn btn-primary btn-sm']) ?>              

    <br>
    <br>    
    <div class="panel panel-success">
        <div class="panel-heading">
            Orden de Producción
        </div>
         <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, "idordenproduccion") ?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?>:</th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipo') ?>:</th>
                    <td><?= Html::encode($model->tipo->tipo) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Unidades') ?>:</th>
                    <td><?= Html::encode($model->cantidad) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechallegada') ?>:</th>
                    <td><?= Html::encode($model->fechallegada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechaprocesada') ?>:</th>
                    <td><?= Html::encode($model->fechaprocesada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechaentrega') ?>:</th>
                    <td><?= Html::encode($model->fechaentrega) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'usuariosistema') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ordenproduccion') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ordenproduccionext') ?>:</th>
                    <td ><?= Html::encode($model->ordenproduccionext) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigoproducto') ?>:</th>
                    <td style = "background-color: #83c5be; color: black"><?= Html::encode($model->codigoproducto) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Exportacion') ?>:</th>
                    <td><?= Html::encode($model->exportarOrden) ?></td>                       
                </tr>
                <tr style="font-size: 85%;">
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan="8"><?= Html::encode($model->observacion) ?></td>    
                </tr>
              
            </table>
        </div>
    </div>
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#detalle_orden" aria-controls="detalle_orden" role="tab" data-toggle="tab">Detalle <span class="badge"><?= count($modeldetalles) ?></span></a></li>
            <li role="presentation"><a href="#avanceprenda" aria-controls="avanceprenda" role="tab" data-toggle="tab">Avance de la OP <span class="badge"><?= count($modeldetalles) ?></span></a></li>
            <li role="presentation"><a href="#novedadesorden" aria-controls="novedadesorden" role="tab" data-toggle="tab">Novedades <span class="badge"><?= count($novedad_orden) ?></span></a></li>
        </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="detalle_orden">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Producto</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Confeccionadas</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Faltan x confeccionar</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Precio</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Subtotal</th>
                                            <th scope="col" style='background-color:#B9D5CE;'></th>
                                            <th scope="col" style='background-color:#B9D5CE;'></th>
                                        </tr>
                                    </thead>    
                                    <body>
                                        <?php foreach ($modeldetalles as $val): ?>
                                            <tr style="font-size: 85%;">
                                                <td style="background-color: <?= $val->plantaProduccion->nombre_color?> "><?= $val->plantaProduccion->nombre_planta ?></td>
                                                <td><?= $val->productodetalle->prendatipo->prenda.' / '.$val->productodetalle->prendatipo->talla->talla   ?></td>
                                                <td style="text-align: right; background-color: #83c5be; color: black"><?= ''. number_format($val->cantidad,0) ?></td>
                                                <td style="text-align: right; background-color: #d8e2dc; color: black"><?= ''. number_format($val->cantidad_operada,0) ?></td>
                                                <td style="text-align: right; background-color: #fcd5ce; color: black"><?= ''. number_format($val->cantidad - $val->cantidad_operada,0) ?></td>
                                                <td><?= '$ '.number_format($val->vlrprecio,2) ?></td>
                                                <td><?= '$ '.number_format($val->subtotal,2) ?></td>
                                                <?php if ($model->autorizado == 0) { ?>
                                                <td style="width: 25px;">
                                                        <a href="#" data-toggle="modal" data-target="#iddetalleorden2<?= $val->iddetalleorden ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                                                        <!-- Editar modal detalle -->
                                                        <div class="modal fade" role="dialog" aria-hidden="true" id="iddetalleorden2<?= $val->iddetalleorden ?>">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                                        <h4 class="modal-title">Editar detalle <?= $val->iddetalleorden ?></h4>
                                                                    </div>
                                                                    <?= Html::beginForm(Url::toRoute("orden-produccion/editardetalle"), "POST") ?>
                                                                    <div class="modal-body">
                                                                        <div class="panel panel-success">
                                                                            <div class="panel-heading">
                                                                                <h4>Información Orden Producción Detalle</h4>
                                                                            </div>
                                                                            <div class="panel-body">
                                                                                <div class="col-lg-2">
                                                                                    <label>Cantidad:</label>
                                                                                </div>
                                                                                <div class="col-lg-3">
                                                                                    <input type="text" name="cantidad" value="<?= $val->cantidad ?>" class="form-control" required>
                                                                                </div>
                                                                                <div class="col-lg-2">
                                                                                    <label>Costo:</label>
                                                                                </div>
                                                                                <div class="col-lg-3">
                                                                                    <input type="text" name="vlrprecio" value="<?=  $val->vlrprecio ?>" class="form-control" required>
                                                                                </div>
                                                                                <input type="hidden" name="iddetalleorden" value="<?= $val->iddetalleorden ?>">
                                                                                <input type="hidden" name="idordenproduccion" value="<?= $val->idordenproduccion ?>">
                                                                                <input type="hidden" name="subtotal" value="<?= $val->subtotal ?>">
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
                                                        <a href="#" data-toggle="modal" data-target="#iddetalleorden<?= $val->iddetalleorden ?>"><span class="glyphicon glyphicon-trash"></span></a>
                                                        <div class="modal fade" role="dialog" aria-hidden="true" id="iddetalleorden<?= $val->iddetalleorden ?>">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                                        <h4 class="modal-title">Eliminar Detalle</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>¿Realmente deseas eliminar el registro con código <?= $val->iddetalleorden ?>?</p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <?= Html::beginForm(Url::toRoute("orden-produccion/eliminardetalle"), "POST") ?>
                                                                        <input type="hidden" name="iddetalleorden" value="<?= $val->iddetalleorden ?>">
                                                                        <input type="hidden" name="idordenproduccion" value="<?= $model->idordenproduccion ?>">
                                                                        <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                                        <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Eliminar</button>
                                                                        <?= Html::endForm() ?>
                                                                    </div>
                                                                </div><!-- /.modal-content -->
                                                            </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal -->
                                                    </td>                            

                                                <?php } else {?>
                                                    <th scope="col" style='background-color:#B9D5CE;'></th>
                                                    <th scope="col" style='background-color:#B9D5CE;'></th>
                                                <?php }?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </body>          
                                </table>    
                            </div>  
                            <?php if ($model->autorizado == 0) { ?>
                                <div class="panel-footer text-right">
                                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nuevo', ['orden-produccion/nuevodetalles', 'idordenproduccion' => $model->idordenproduccion,'idcliente' => $model->idcliente], ['class' => 'btn btn-success btn-sm']) ?>
                                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['orden-produccion/editardetalles', 'idordenproduccion' => $model->idordenproduccion],[ 'class' => 'btn btn-success btn-sm']) ?>                                            
                                    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['orden-produccion/eliminardetalles', 'idordenproduccion' => $model->idordenproduccion], ['class' => 'btn btn-danger btn-sm']) ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>    
                </div>
              
                <!-- TERMINA TABS DE DETALLE -->
                <div role="tabpanel" class="tab-pane" id="avanceprenda">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Producto / Referencia</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Progreso de confección</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Confeccion</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Faltan</th>

                                        </tr>
                                    </thead>
                                    <body>
                                        <?php
                                        $falta = 0;
                                        foreach ($modeldetalles as $val):
                                            $falta =$val->cantidad - $val->cantidad_operada 
                                            ?>
                                            <tr style="font-size: 85%;">
                                                <td style="background-color: <?= $val->plantaProduccion->nombre_color?> "><?= $val->plantaProduccion->nombre_planta ?></td>
                                                <td><?= $val->productodetalle->prendatipo->prenda.' / '.$val->productodetalle->prendatipo->talla->talla ?></td>
                                                <td><?= $val->codigoproducto ?></td>
                                                <td><?= $val->cantidad ?></td>
                                                <td><div class="progress"><b>Operación:&nbsp;</b>
                                                        <progress id="html5" max="100" value="<?= $val->porcentaje_proceso ?>"></progress>
                                                        <span><b><?= Html::encode(round($val->porcentaje_proceso,1)).' %' ?></b></span>&nbsp;&nbsp;-&nbsp;&nbsp;<b>Cantidad:</b>
                                                        <progress id="html5" max="100" value="<?= $val->porcentaje_cantidad ?>"></progress>
                                                        <span><b><?= Html::encode(round($val->porcentaje_cantidad,1)).' %' ?></b></span>
                                                    </div>
                                                </td>
                                                <td style ="text-align: right "><?= $val->cantidad_operada ?></td>
                                                <td style ="text-align: right "><?= $falta?></td>
                                            </tr>
                                        <?php endforeach; ?>      
                                    </body>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>    
                <div role="tabpanel" class="tab-pane" id="novedadesorden">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'><b>Id</b></th>                        
                                            <th scope="col" align="center" style='background-color:#B9D5CE; width: 65%;' >Novedad</th>                        
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'>Fecha proceso</th>       
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'>Usuario</th>   
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'>Autorizado</th>  
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'></th> 
                                        </tr>
                                    </thead>
                                    <body>
                                         <?php
                                         foreach ($novedad_orden as $val):?>
                                            <tr style="font-size: 85%;">
                                                <td><?= $val->id_novedad ?></td>
                                                <td><?= $val->novedad ?></td>
                                                <td><?= $val->fecha_proceso ?></td>
                                                <td><?= $val->usuariosistema ?></td>
                                                <td><?= $val->autorizarNovedad ?></td>
                                                <td style="width: 25px;">				
                                                    <a href="<?= Url::toRoute(["imprimirnovedadorden",'id_novedad'=>$val->id_novedad, 'id' =>$model->idordenproduccion]) ?>" ><span class="glyphicon glyphicon-print" title="Imprimir novedad "></span></a>
                                                </td>
                                            </tr>
                                         <?php endforeach;?>          
                                    </body>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> 
                  <!-- TERMINA TABS DE NOVEDADES -->
            </div>  
    </div>     
</div>
