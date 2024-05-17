<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Ordenproducciondetalle;
use app\models\Ordenproducciondetalleproceso;
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
use app\models\Producto;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\bootstrap\Progress;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$this->title = 'Ficha de operaciones detalle';
$this->params['breadcrumbs'][] = ['label' => 'Ficha Operaciones', 'url' => ['proceso']];
$this->params['breadcrumbs'][] = $model->idordenproduccion;
?>
<div class="ordenproduccionproceso-view">
   
        <?php echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['proceso'], ['class' => 'btn btn-primary btn-sm']);?>
        <div class="btn-group btn-sm" role="group">
            <button type="button" class="btn btn-info  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               Imprimir
               <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                    <li><?= Html::a('<span class="glyphicon glyphicon-print"></span> Medidas AL', ['imprimirpilotos', 'id' => $model->idordenproduccion]) ?></li>
                    <?php if($model->proceso_sin_lavanderia > 0){?>
                        <li><?= Html::a('<span class="glyphicon glyphicon-print"></span> Medidas DL', ['imprimir_pilotos_dl', 'id' => $model->idordenproduccion]) ?></li>
                    <?php }?>    
            </ul>
        </div>   
        <?= Html::a('<span class="glyphicon glyphicon-eye-close"></span> Cerrar medidas',
                                              ['/orden-produccion/cerrar_medidas_pilotos', 'id' => $model->idordenproduccion],
                                                ['title' => 'Proceso que permite cerrar las medidas de la pilot AL',
                                                 'data-toggle'=>'modal',
                                                 'data-target'=>'#modalcerrarmedidaspiloto',
                                                 'class' => 'btn btn-success btn-xs'
                                    ])    
        ?>
        <div class="modal remote fade" id="modalcerrarmedidaspiloto">
               <div class="modal-dialog modal-lg" style ="width: 500px;">    
                   <div class="modal-content"></div>
               </div>
        </div>
                  
   

    <div class="panel panel-success">
        <div class="panel-heading">
            Ficha Operaciones Detalle
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idordenproduccion') ?></th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?></th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ordenproduccion') ?></th>
                    <td><?= Html::encode($model->ordenproduccion) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechallegada') ?></th>
                    <td><?= Html::encode($model->fechallegada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechaprocesada') ?></th>
                    <td><?= Html::encode($model->fechaprocesada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechaentrega') ?></th>
                    <td><?= Html::encode($model->fechaentrega) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cantidad') ?></th>
                    <td><?= Html::encode($model->cantidad) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Progreso') ?></th>
                    <td><div class="progress"><b>Operación:&nbsp;</b>
                            <progress id="html5" max="100" value="<?= $model->porcentaje_proceso ?>"></progress>
                            <span><b><?= Html::encode(round($model->porcentaje_proceso,1)).' %' ?></b></span>
                            <b>&nbsp;Faltante:&nbsp;</b><progress id="html5" max="100" value="<?= 100 - $model->porcentaje_proceso ?>"></progress>
                            <span><b><?= Html::encode(round(100 - $model->porcentaje_proceso,1)).' %' ?></b></span>
                        </div>
                        <div class="progress"><b>Cantidad:&nbsp;&nbsp;&nbsp;</b>
                            <progress id="html5" max="100" value="<?= $model->porcentaje_cantidad ?>"></progress>
                            <span><b><?= Html::encode(round($model->porcentaje_cantidad,1)).' %' ?></b></span>
                            <b>&nbsp;Faltante:&nbsp;</b><progress id="html5" max="100" value="<?= 100 - $model->porcentaje_cantidad ?>"></progress>
                            <span><b><?= Html::encode(round(100 - $model->porcentaje_cantidad,1)).' %' ?></b></span>
                        </div>
                    </td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipo') ?></th>
                    <td><?= Html::encode($model->tipo->tipo) ?></td>
                </tr>
            </table>
        </div>
    </div>
    <!-- INICIO DE LOS TABS -->
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#detalle_orden" aria-controls="detalle_orden" role="tab" data-toggle="tab">Detalle <span class="badge"><?= count($modeldetalles) ?></span></a></li>
            <li role="presentation"><a href="#medidapilotos" aria-controls="medidapilotos" role="tab" data-toggle="tab">Medidas <span class="badge"><?= count($detalle_piloto) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="detalle_orden">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr style="font-size: 85%;">
                                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Producto</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Progreso</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Cantidad confeccionada</th>
                                    <th style='background-color:#B9D5CE;'></th>
                                    <th style='background-color:#B9D5CE;'></th>
                                    <th style='background-color:#B9D5CE;'></th>
                                    <th style='background-color:#B9D5CE;'></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modeldetalles as $val): ?>
                                    <tr style="font-size: 85%;">
                                        <td><?= $val->iddetalleorden ?></td>
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
                                        <td><?= $val->cantidad_operada ?></td>
                                        <?php if($model->cerrar_orden == 0){?>
                                        <td style="width: 25px; height: 15px;">
                                                    <?= Html::a('<span class="glyphicon glyphicon-folder-open " title ="Crear las pilotos"></span>', ['/orden-produccion/newpilotoproduccion','id' => $model->idordenproduccion,'iddetalle' => $val->iddetalleorden]) ?>
                                                    <!-- Inicio Vista,Eliminar,Editar -->
                                        </td>
                                        <td style="width: 25px; height: 15px;">
                                                <?= Html::a('<span class="glyphicon glyphicon-import " title ="Importar operaciones de otra OP."></span>', ['/orden-produccion/importaroperacionesprenda','id' => $model->idordenproduccion,'iddetalleorden' => $val->iddetalleorden]) ?>
                                                <!-- Inicio Vista,Eliminar,Editar -->
                                        </td>
                                        <td style="width: 25px; height: 15px;">
                                                <?= Html::a('<span class="glyphicon glyphicon-log-in " title ="Crear operaciones a las tallas"></span>', ['/orden-produccion/nuevo_detalle_proceso','id' => $model->idordenproduccion,'iddetalleorden' => $val->iddetalleorden]) ?>
                                                <!-- Inicio Vista,Eliminar,Editar -->
                                        </td>
                                        <?php }else{?>
                                            <td style="width: 25px; height: 15px;">
                                            </td>
                                            <td style="width: 25px; height: 15px;">
                                            </td>
                                            <td style="width: 25px; height: 15px;">
                                            </td>
                                        <?php }?>    
                                        <td style="width: 25px; height: 15px;">
                                                <?php echo Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                                    ['/orden-produccion/detalle_proceso','idordenproduccion' => $model->idordenproduccion,'iddetalleorden' => $val->iddetalleorden],
                                                    [
                                                        'title' => 'Listado de operaciones',
                                                        'data-toggle'=>'modal',
                                                        'data-target'=>'#modaldetalleproceso'.$val->iddetalleorden,
                                                    ]
                                                );
                                                ?>
                                                <div class="modal remote fade" id="modaldetalleproceso<?= $val->iddetalleorden ?>">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content"></div>
                                                    </div>
                                                </div>
                                                <!-- Fin Vista,Eliminar,Editar -->

                                            </td>

                                    </tr>
                                <?php endforeach; ?>
                                </tbody>    
                            </table>
                        </div>
                    </div>
                </div>
            </div>        
            <!--TERMINA EL TABS-->
            <div role="tabpanel" class="tab-pane " id="medidapilotos">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr style="font-size: 85%;">
                                    <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Concepto</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Ficha AL</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Ficha DL</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Confección AL</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Confección DL</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Tole. AL</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Nota AL</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Tole. DL</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Nota DL</th>
                                     <th scope="col" style='background-color:#B9D5CE;'>Aplica</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th>
                                   
                                     <th style='background-color:#B9D5CE;'></th>
                                </tr>
                                </thead>
                                <body>
                                    <?php
                                    foreach ($detalle_piloto as $val):?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $val->detalleorden->productodetalle->prendatipo->talla->talla ?></td>
                                            <td><?= $val->concepto?></td>
                                            <td><?= $val->medida_ficha_al?></td>
                                            <td><?= $val->medida_ficha_dl?></td>
                                            <td><?= $val->medida_confeccion_al?></td>
                                            <td><?= $val->medida_confeccion_dl?></td>
                                            <?php if($val->tolerancia_al < 0){?>
                                                <td style="background-color:#B2F3EE; color: #F51F15;"><?= $val->tolerancia_al ?></td>
                                                 <td style="color: #F51F15;"><?= $val->observacion_al ?></td>
                                              <?php }else{ ?>
                                                    <td style="background-color:#DAF7A6; color: #111213;"><?= $val->tolerancia_al ?></td>
                                                     <td style="color: #117A65;"><?= $val->observacion_al ?></td>
                                            <?php } 
                                            if($val->tolerancia_dl < 0){?>
                                                <td style="background-color:#B2F3EE; color: #F51F15;"><?= $val->tolerancia_dl ?></td>
                                                <td style="color: #F51F15;"><?= $val->observacion_dl ?></td>
                                            <?php }else{ 
                                                ?>
                                                <td style="background-color:#DAF7A6; color: #111213;"><?= $val->tolerancia_dl ?></td>
                                                <td style="color: #117A65;"><?= $val->observacion_dl ?></td>
                                            <?php } ?>
                                            <td><?= $val->aplicadoproceso?></td>
                                           <td><?= $val->fecha_registro?></td>
                                       </tr> 
                                    <?php endforeach; ?>
                                    
                                </body>
                                
                            </table>
                            <div class="panel-footer text-right">
                                <?= Html::a('<span class="glyphicon glyphicon-export"></span> Excel', ['generarexcelmedidas', 'id' => $model->idordenproduccion], ['class' => 'btn btn-primary btn-sm ']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- TERMINA EL TABS -->
       </div>    
    </div>    
</div>
