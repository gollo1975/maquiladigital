<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Ordenproducciondetalle;
use app\models\Ordenproducciondetalleproceso;
use app\models\ValorPrendaUnidad;
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
$ordenproduccion = Ordenproduccion::find()->where(['=','ordenproduccion', $model->ordenproduccion])->all();
$this->title = 'Ficha de operaciones';
$this->params['breadcrumbs'][] = ['label' => 'Ficha Operaciones', 'url' => ['indexconsultaficha']];
$this->params['breadcrumbs'][] = $model->idordenproduccion;
?>
<div class="ordenproduccionproceso-view">
    <p>
        <?php if($condicion == 0){?> 
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['indexconsultaficha'], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php }else{?>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['indexoperacionprenda'], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php }?>
    </p>

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
   
    <!--INICIO DEL TAB-->
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#detalleorden" aria-controls="detalleorden" role="tab" data-toggle="tab">Detalle orden <span class="badge"><?= count($modeldetalles) ?></span></a></li>
            <li role="presentation"><a href="#costoordenproduccion" aria-controls="costoordenproduccion" role="tab" data-toggle="tab">Costo del servicio <span class="badge"><?= count($ordenproduccion) ?></span></a></li>
            <li role="presentation"><a href="#modulo" aria-controls="modulo" role="tab" data-toggle="tab">Modulos <span class="badge"><?= count($modulos) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="detalleorden">
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
                                        <th scope="col" style='background-color:#B9D5CE;'>Progreso</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Confección</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
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
                                            <td style="width:25px;">                                
                                                    <!-- Inicio Vista,Eliminar,Editar -->
                                                    <?php echo Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                                        ['/orden-produccion/detalle_proceso_consulta','idordenproduccion' => $model->idordenproduccion,'iddetalleorden' => $val->iddetalleorden],
                                                        [
                                                            'title' => 'Consulta Detalle Proceso',
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
            <!-- SEGUNDO TAB-->
            <div role="tabpanel" class="tab-pane" id="costoordenproduccion">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            Confección / Terminación
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo servicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Valor lote</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>Costo servicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Utilidad operativa" >U. Operativa</span></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Porcentaje de costo" >%Costo</span></th>
                                          <th scope="col" style='background-color:#B9D5CE;'><span title="Porcentaje de utilidad" >%Utilidad</span></th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha Proceso</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $valor_total = 0; $utilidad = 0; $porcentaje = 0; $buscar1 = 0; $buscar2 = 0;$costo1 =0; $costo2 =0;
                                    $total_ingresos = 0; $total = 0;
                                    foreach ($ordenproduccion as $val):
                                        $valor_prenda = ValorPrendaUnidad::find()->where(['=','idordenproduccion', $val->idordenproduccion])->all();
                                        foreach ($valor_prenda as $valor):
                                            $valor_total += $valor->total_pagar;   
                                        endforeach;  
                                        $utilidad = $val->totalorden - $valor_total;
                                        if($val->tipo->idtipo == 1 or $val->tipo->idtipo == 4){
                                            $buscar1 = $utilidad;
                                            $costo1 = $valor_total;
                                        }
                                        if($val->tipo->idtipo == 2){
                                            $buscar2 = $utilidad;
                                            $costo2 = $valor_total;
                                        }
                                        $porcentaje = round(((100 * $valor_total)/$val->totalorden),2);
                                         ?>
                                           <tr style="font-size: 85%;">
                                               <td><?= $val->idordenproduccion ?></td>
                                               <td><?= $val->tipo->tipo?></td>
                                               <td align="right"><?= ''.number_format($val->cantidad,0) ?></td>
                                               <td align="right"><?= ''.number_format($val->totalorden,0) ?></td>
                                               <td align="right"><?= ''.number_format($valor_total,0) ?></td>
                                               <td align="right"><?= ''.number_format($utilidad,0) ?></td>
                                               <td align="right"><?= ''.number_format($porcentaje,0) ?> %</td>
                                               <td align="right"><?= ''.number_format(100-$porcentaje,0) ?>%</td>
                                               <td><?= $val->fechallegada ?></td>
                                               <td><?= $val->usuariosistema ?></td>
                                           </tr>
                                    <?php
                                     $total += $valor_total;
                                    $valor_total = 0;
                                    $total_ingresos += $val->totalorden; 
                                    endforeach;?>
                            </table>               
                        </div>                   
                    </div>                      
                </div>                                       
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            Compras
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Factura</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Tercero</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Vr. Costo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha Compra</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Fecha Proceso</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>

                                    </tr>
                                </thead>
                                <body>
                                    <?php
                                    $total_gastos = 0;
                                    $TotalGastosOperacion = 0;
                                    $otrosCostos = app\models\OtrosCostosProduccion::find()->where(['=','idordenproduccion', $model->idordenproduccion])->all();
                                    foreach ($otrosCostos as $costos):?>
                                         <tr style="font-size: 85%;">
                                            <td><?= $costos->nrofactura ?></td>
                                            <td><?= $costos->proveedorCostos->nombrecorto ?></td>
                                            <td align="right"><?= ''.number_format($costos->vlr_costo,0) ?></td>
                                            <td><?= $costos->fecha_compra ?></td>
                                            <td><?= $costos->fecha_proceso ?></td>
                                              <td><?= $costos->usuariosistema ?></td>
                                         </tr>    
                                    <?php
                                    $total_gastos += $costos->vlr_costo;
                                    $TotalGastosOperacion = $total_gastos + $total;
                                    endforeach;    
                                    if ($TotalGastosOperacion == 0){
                                        $TotalGastosOperacion = $total;
                                    }
                                    ?> 
                               
                            </table>    
                        </div>
                    </div>    
                </div>
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            Resultados
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                   <?php
                                    $sumaCosto = 0;
                                    $porcentaje = 100;
                                    $sumaCosto = number_format((($TotalGastosOperacion / $total_ingresos)*100),0);
                                   ?>
                                    <td colspan="0"><td style="font-size: 90%;background: #4B6C67; color: #FFFFFF; width: 210px;" align="right"><b>Ingresos:</b> <?= ''.number_format($total_ingresos,0) ?></td>       
                                    <td colspan="0"><td style="font-size: 90%;background: #4B6C67; color: #FFFFFF; width: 210px;" align="right"><b>Gastos:</b> <?= ''.number_format($total_gastos + $total,0)?> ( <?= ''. number_format((($TotalGastosOperacion / $total_ingresos)*100),0) ?>%)</td>    
                                    <td colspan="0"><td style="font-size: 90%;background: #4B6C67; color: #FFFFFF; width: 210px;" align="right"><b>Utilidad:</b> <?= ''.number_format(($total_ingresos- ($total_gastos + $total)) ,0) ?> (<?= $porcentaje - $sumaCosto ?>%) </td>    
                            </table>
                        </div>    
                    </div>
                </div>
                               
            <?php include('indicador.php'); ?>   
            </div>   
            <div role="tabpanel" class="tab-pane" id="modulo">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                      <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Número</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Modulo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Sam Pro.</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Sam Conf.</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Sam Balanceo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'># Operarios</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha terminación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                         <th scope="col" style='background-color:#B9D5CE;'></th>
                                      
                                    </tr>
                                </thead>
                                  <?php foreach ($modulos as $registro_modulo): ?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $registro_modulo->id_balanceo ?></td>
                                            <td><?= $registro_modulo->modulo ?></td>
                                               <td><?= $registro_modulo->procesoconfeccion->descripcion_proceso ?></td>
                                            <td><?= $registro_modulo->ordenproduccion->duracion ?></td>
                                            <td><?= $registro_modulo->total_minutos ?></td>
                                            <td><?= $registro_modulo->tiempo_balanceo ?></td>
                                            <td><?= $registro_modulo->cantidad_empleados ?></td>
                                            <td><?= $registro_modulo->fecha_inicio ?></td>
                                            <td><?= $registro_modulo->fecha_terminacion ?></td>
                                            <td><?= $registro_modulo->observacion ?></td>
                                            <?php if($registro_modulo->id_proceso_confeccion == 1){?>
                                                <td style="width: 25px;">
                                                    <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/orden-produccion/eficienciamodulo', 'id_balanceo' => $registro_modulo->id_balanceo], ['target' => '_blank']) ?>
                                                </td>
                                            <?php }else{?>    
                                                <td></td>
                                            <?php }?>    
                                        </tr>    
                                  <?php endforeach; ?>          
                            </table>
                        </div>
                    </div>
                </div>
            </div>    
            <!-- TERMINA EL TAB-->
   </div>
</div>    
