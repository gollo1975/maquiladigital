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

$this->title = 'Detalle Orden de Producción';
$this->params['breadcrumbs'][] = ['label' => 'Ordenes de Producción', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idordenproduccion;
$view = 'orden-produccion';
?>

<?php
    $remision = Remision::find()->where(['=', 'idordenproduccion', $model->idordenproduccion])->one();
?>

<div class="ordenproduccion-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->idordenproduccion], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php if ($model->autorizado == 0) { ?>
                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->idordenproduccion], ['class' => 'btn btn-success btn-sm']) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->idordenproduccion], [
                        'class' => 'btn btn-danger btn-sm',
                        'data' => [
                            'confirm' => 'Esta seguro de eliminar el registro?',
                            'method' => 'post' ,
                        ],
                    ]) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->idordenproduccion], ['class' => 'btn btn-default btn-sm']);
        }
            else {
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->idordenproduccion], ['class' => 'btn btn-default btn-sm']);
                echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimir', 'id' => $model->idordenproduccion], ['class' => 'btn btn-default btn-sm']);            
                echo Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 4, 'codigo' => $model->idordenproduccion,'view' => $view], ['class' => 'btn btn-default btn-sm']);                
                if($model->exportacion == 2){
                    echo Html::a('<span class="glyphicon glyphicon-print"></span> Exportación', ['imprimirexportacion', 'id' => $model->idordenproduccion], ['class' => 'btn btn-info btn-sm']);                
                }
                if ($remision){
                   if ($model->tipo->remision == 1)
                   {    
                        echo Html::a('<span class="glyphicon glyphicon-file"></span> Remision', ['remision/remision', 'id' => $model->idordenproduccion], ['class' => 'btn btn-default btn-sm']);                             
                   }
                }else{
                    if ($model->tipo->remision == 1)
                    {
                    ?>
                    <!-- Editar modal detalle -->
                    <a href="#" data-toggle="modal" data-target="#remision<?= $model->idordenproduccion ?>" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-file"></span> Remision</a>
                    <div class="modal fade" role="dialog" aria-hidden="true" id="remision<?= $model->idordenproduccion ?>">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    <h4 class="modal-title">Remisión</h4>
                                </div>                            
                                <?= Html::beginForm(Url::toRoute(["remision/remision", 'id' => $model->idordenproduccion]), "POST") ?>                            
                                <?php
                                    $colores = ArrayHelper::map(Color::find()->all(), 'id', 'color');
                                ?>
                                <div class="modal-body">
                                    <div class="panel panel-success">
                                        <div class="panel-heading">
                                            <h4>Información Remisión</h4>
                                        </div>
                                        <div class="panel-body">
                                            <div class="col-lg-3">
                                                <label>Colores:</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <?php echo Html::dropdownList('color', '',$colores, ['class' => 'form-control', 'style' => 'width:200px','prompt' => 'Seleccione...','required' => true]) ?>
                                            </div>   
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                    <button type="submit" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span> Crear</button>
                                </div>
                                <?= Html::endForm() ?>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
            <?php }}    ?>
            <?php }    ?>        

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
                    <td><?= Html::encode($model->ordenproduccionext) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigoproducto') ?>:</th>
                    <td><?= Html::encode($model->codigoproducto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Minutos') ?>:</th>
                    <td><?= Html::encode($model->duracion) ?></td>                    
                </tr>
                <tr style="font-size: 85%;">
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan="3"><?= Html::encode($model->observacion) ?></td>    
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Exportacion') ?>:</th>
                    <td><?= Html::encode($model->exportarOrden) ?></td>    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'totalorden') ?>:</th>
                    <td  style="text-align: right"><?= Html::encode('$ '.number_format($model->totalorden,0)) ?></td>
                </tr>
              
            </table>
        </div>
    </div>
     <?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
    ]);?>
    <!-- comienza los tabs -->
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#detalle_orden" aria-controls="detalle_orden" role="tab" data-toggle="tab">Detalle <span class="badge"><?= count($modeldetalles) ?></span></a></li>
            <li role="presentation"><a href="#costo_adicional" aria-controls="costo_adicional" role="tab" data-toggle="tab">Costos <span class="badge"><?= count($otrosCostosProduccion) ?></span></a></li>
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
                                            <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Producto</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Precio</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Subtotal</th>
                                            <th scope="col" style='background-color:#B9D5CE;'></th>
                                            <th scope="col" style='background-color:#B9D5CE;'></th>
                                        </tr>
                                    </thead>    
                                    <body>
                                        <?php foreach ($modeldetalles as $val): ?>
                                            <tr style="font-size: 85%;">
                                                <td><?= $val->iddetalleorden ?></td>
                                                <td><?= $val->productodetalle->prendatipo->prenda.' / '.$val->productodetalle->prendatipo->talla->talla   ?></td>
                                                <td><?= $val->codigoproducto ?></td>
                                                <td><?= $val->cantidad ?></td>
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
                <div role="tabpanel" class="tab-pane" id="costo_adicional">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'><b>Documento</b></th>                        
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'>Tercero</th>                        
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'>F. compra</th>       
                                             <th scope="col" align="center" style='background-color:#B9D5CE;'>F. proceso</th>  
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'>Usuario</th>                        
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'>Vr. compra</th>  
                                            <th scope="col" align="center" style='background-color:#B9D5CE;'></th> 
                                             <th scope="col" style='background-color:#B9D5CE;'></th> 
                                        </tr>
                                    </thead>
                                    <body>
                                         <?php
                                         foreach ($otrosCostosProduccion as $val):?>
                                            <tr style="font-size: 85%;">
                                                <td><?= $val->nrofactura ?></td>
                                                <td><?= $val->proveedorCostos->nombrecorto ?></td>
                                                <td><?= $val->fecha_compra ?></td>
                                                <td><?= $val->fecha_proceso ?></td>
                                                <td><?= $val->usuariosistema ?></td>
                                                <td style="text-align:right"><?= ''.number_format($val->vlr_costo,2) ?></td>
                                                <td style="padding-right: 1;padding-right: 0;"><input type="text" name="vlr_costo[]" value="<?= $val->vlr_costo ?>" size="9" required="true"> </td>  
                                                <input type="hidden" name="detalle_costo[]" value="<?= $val->id_costo ?>">
                                                <td style= 'width: 25px; height: 25px;'>
                                                        <?php 
                                                        if($model->cerrar_orden == 0){?>
                                                           <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['eliminar', 'id' => $model->idordenproduccion, 'detalle' => $val->id_costo], [
                                                                      'class' => '',
                                                                      'data' => [
                                                                          'confirm' => 'Esta seguro de eliminar el registro?',
                                                                          'method' => 'post',
                                                                      ],
                                                                  ])
                                                           ?>
                                                        <?php } ?> 
                                                    </div>    
                                                </td>     
                                            </tr>
                                         <?php endforeach;?>          
                                    </body>
                                </table>
                            </div>
                            <div class="panel-footer text-right">  
                                <?php 
                                if($model->cerrar_orden == 0){?>
                                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-success btn-sm",]) ?>
                                    <?= Html::a('<span class="glyphicon glyphicon-search"></span> Buscar', ['orden-produccion/nuevocostoproduccion', 'id' => $model->idordenproduccion],[ 'class' => 'btn btn-primary btn-sm']) ?>                                            
                                    
                                <?php }?>
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
                                             <th scope="col" style='background-color:#B9D5CE;'></th> 
                                              <th scope="col" style='background-color:#B9D5CE;'></th> 
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
                                                <td style= 'width: 25px; height: 25px;'>
                                                     <a href="<?= Url::toRoute(["orden-produccion/vistanovedadorden",'id_novedad'=>$val->id_novedad,'id' => $model->idordenproduccion]) ?>" ><span class="glyphicon glyphicon-eye-open" title="Ver detalle de la novedad "></span></a>
                                                </td>
                                                <?php 
                                                if($val->autorizado == 0){?>
                                                   <td style= 'width: 25px; height: 25px;'>
                                                       <a href="<?= Url::toRoute(["orden-produccion/editarnovedadproduccion",'id_novedad'=>$val->id_novedad,'id' => $model->idordenproduccion]) ?>" ><span class="glyphicon glyphicon-pencil" title="Editar novedad"></span></a>
                                                                                                                                                                                                        
                                                   </td>
                                                    <td style= 'width: 25px; height: 25px;'>
                                                        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['eliminarnovedadproduccion', 'id' => $model->idordenproduccion, 'id_novedad' => $val->id_novedad], [
                                                                   'class' => '',
                                                                   'data' => [
                                                                       'confirm' => 'Esta seguro de eliminar el registro?',
                                                                       'method' => 'post',
                                                                   ],
                                                               ])
                                                        ?>
                                                    </td>    
                                                <?php }else{ ?> 
                                                    <td style="width: 25px;">				
                                                        <a href="<?= Url::toRoute(["imprimirnovedadorden",'id_novedad'=>$val->id_novedad, 'id' =>$model->idordenproduccion]) ?>" ><span class="glyphicon glyphicon-print" title="Imprimir novedad "></span></a>
                                                    </td>
                                                    <td style= 'width: 25px; height: 25px;'></td>
                                                <?php }?>
                                            </tr>
                                         <?php endforeach;?>          
                                    </body>
                                </table>
                            </div>
                            <div class="panel-footer text-right">  
                               <!-- Inicio Nuevo Detalle proceso -->
                                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear novedad',
                                    ['/orden-produccion/crearnovedadordenproduccion','id' => $model->idordenproduccion],
                                    [
                                        'title' => 'Novedades de producción',
                                        'data-toggle'=>'modal',
                                        'data-target'=>'#modalcrearnovedadordenproduccion'.$model->idordenproduccion,
                                        'class' => 'btn btn-success btn-xs'
                                    ])    
                                ?>
                                <div class="modal remote fade" id="modalcrearnovedadordenproduccion<?= $model->idordenproduccion ?>">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content"></div>
                                    </div>
                                </div>
                            </div>   
                        </div>
                    </div>
                </div> 
                  <!-- TERMINA TABS DE NOVEDADES -->
            </div>  
    </div>
  <?php ActiveForm::end(); ?>  
</div>

   