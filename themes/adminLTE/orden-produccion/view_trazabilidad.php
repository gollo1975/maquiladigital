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

$this->title = 'Trazabilidad OP';
$this->params['breadcrumbs'][] = ['label' => 'Ordenes de Producción', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idordenproduccion;
$view = 'orden-produccion';
?>
<div class="ordenproduccion-view_trazabilidad">
    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['trazabilidad_ordenes'], ['class' => 'btn btn-primary btn-sm']) ?>
    </p>        
          
    <div class="panel panel-success">
        <div class="panel-heading">
            Orden de Producción
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, "idordenproduccion") ?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idcliente') ?>:</th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigoproducto') ?></th>
                    <td style="background-color: <?= $model->tipo->color?>"><?= Html::encode($model->codigoproducto) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Unidades') ?>:</th>
                     <td style="text-align: right"><?= Html::encode(''.number_format($model->cantidad,0)) ?></td>
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
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'usuariosistema') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
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
            <li role="presentation" class="active"><a href="#detalle_orden" aria-controls="detalle_orden" role="tab" data-toggle="tab">Tallas de la op <span class="badge"><?= count($detalle_orden) ?></span></a></li>
            <li role="presentation"><a href="#listado_operaciones" aria-controls="listado_operaciones" role="tab" data-toggle="tab">Operaciones <span class="badge"><?= count($operaciones) ?></span></a></li>
            <li role="presentation"><a href="#costos_ordenes" aria-controls="costos_ordenes" role="tab" data-toggle="tab">Ingresos vs Costos <span class="badge"><?= 1 ?></span></a></li>
        </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="detalle_orden">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                          <tr style="font-size: 85%;">
                                            <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Descripcion y talla</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Confeccionadas</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Faltan x confeccionar</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Progreso</th>
                                            <th scope="col" style='background-color:#B9D5CE;'></th>
                                        </tr>
                                    </thead>    
                                    <body>
                                        <?php foreach ($detalle_orden as $val): ?>
                                            <tr style="font-size: 85%;">
                                                <td style="background-color: <?= $val->plantaProduccion->nombre_color?> "><?= $val->plantaProduccion->nombre_planta ?></td>
                                                <td><?= $val->productodetalle->prendatipo->prenda.' / '.$val->productodetalle->prendatipo->talla->talla   ?></td>
                                                <td style="text-align: right"><?= $val->codigoproducto ?></td>
                                                <td style="text-align: right; background-color: #83c5be; color: black"><?= ''. number_format($val->cantidad,0) ?></td>
                                                <td style="text-align: right; background-color: #d8e2dc; color: black"><?= ''. number_format($val->cantidad_operada,0) ?></td>
                                                <td style="text-align: right; background-color: #fcd5ce; color: black"><?= ''. number_format($val->cantidad - $val->cantidad_operada,0) ?></td>
                                                <td>
                                                    <div class="progress"><b>Operación:&nbsp;</b>
                                                        <progress id="html5" max="100" value="<?= $val->porcentaje_proceso ?>"></progress>
                                                        <span><b><?= Html::encode(round($val->porcentaje_proceso,1)).' %' ?></b></span>&nbsp;&nbsp;-&nbsp;&nbsp;<b>Cantidad:</b>
                                                        <progress id="html5" max="100" value="<?= $val->porcentaje_cantidad ?>"></progress>
                                                        <span><b><?= Html::encode(round($val->porcentaje_cantidad,1)).' %' ?></b></span>
                                                     </div>
                                               </td>
                                                <td style="width: 20px; height: 20px;">
                                                        <!-- Inicio Nuevo Detalle proceso -->
                                                          <?= Html::a('<span class="glyphicon glyphicon-user"></span> ',
                                                              ['/orden-produccion/mostrar_operarios_talla', 'id' => $model->idordenproduccion, 'id_detalle_talla' => $val->iddetalleorden],
                                                              [
                                                                  'title' => 'Permite mostrar todos los operarios que estan en esta talla',
                                                                  'data-toggle'=>'modal',
                                                                  'data-target'=>'#modalmostraroperariotalla'.$model->idordenproduccion,
                                                                  'class' => '',
                                                                  'data-backdrop' => 'static',

                                                              ])    
                                                         ?>
                                                </td> 
                                                 <div class="modal remote fade" id="modalmostraroperariotalla<?= $model->idordenproduccion ?>">
                                                          <div class="modal-dialog modal-lg" style ="width: 1000px;">
                                                              <div class="modal-content"></div>
                                                          </div>
                                                      </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </body>          
                                </table>    
                            </div>  
                        </div>
                    </div>    
                </div>
              
                <!-- TERMINA TABS DE DETALLE -->
                 <div role="tabpanel" class="tab-pane" id="listado_operaciones">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                       <tr style="font-size: 85%;">
                                            <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Nombre de la peracion</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Segundos</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Minutos</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Maquina</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Fecha creacion</th>
                                            <th scope="col" style='background-color:#B9D5CE;'></th>
                                        </tr>
                                    </thead>    
                                    <body>
                                        <?php
                                        if(!empty($operaciones)){
                                            foreach ($operaciones as $val): ?>
                                                <tr style="font-size: 85%;">
                                                    <td><?= $val->idproceso ?></td>
                                                    <td><?= $val->proceso->proceso ?></td>
                                                    <td style="text-align: right"><?= $val->segundos   ?></td>
                                                    <td style="text-align: right"><?= $val->minutos ?></td>
                                                    <td><?= $val->tipomaquina->descripcion ?></td>
                                                    <td><?= $val->fecha_creacion ?></td>
                                                    <td style="width: 20px; height: 20px;">
                                                            <!-- Inicio Nuevo Detalle proceso -->
                                                              <?= Html::a('<span class="glyphicon glyphicon-th-list"></span> ',
                                                                  ['/orden-produccion/listado_operarios', 'id' => $id, 'id_proceso' => $val->idproceso],
                                                                  [
                                                                      'title' => 'Permite mostrar todos los operarios que estan operacion',
                                                                      'data-toggle'=>'modal',
                                                                      'data-target'=>'#modalmostraroperarios'.$model->idordenproduccion,
                                                                      'class' => '',
                                                                      'data-backdrop' => 'static',

                                                                  ])    
                                                             ?>
                                                    </td> 
                                                     <div class="modal remote fade" id="modalmostraroperarios<?= $model->idordenproduccion ?>">
                                                              <div class="modal-dialog modal-lg" style ="width: 1000px;">
                                                                  <div class="modal-content"></div>
                                                              </div>
                                                          </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; 
                                        }else{ ?>
                                                 <div class="alert alert-info" style="align-content: center">No hay registros para mostrar o no se ha creado el listado operacional.</div>
                                        <?php } ?>        
                                    </body>          
                                </table>    
                            </div>  
                        </div>
                    </div>    
                </div>
                <!-- TERMINA TABS DE OPERACIONES-->
                <div role="tabpanel" class="tab-pane" id="costos_ordenes">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            Costos del servicio
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                   <tr style="font-size: 85%;">
                                        <th scope="col" style='background-color:#B9D5CE;'>Op</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo servicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Valor lote</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>Costo servicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Utilidad operativa" >U. Operativa</span></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Porcentaje de costo" >%Costo</span></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Porcentaje de utilidad" >%Utilidad</span></th>
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $valor_total = 0; $utilidad = 0; $porcentaje = 0; $buscar1 = 0; $buscar2 = 0;$costo1 =0; $costo2 =0;
                                    $total_ingresos = 0; $total = 0;
                                        $valor_prenda = app\models\ValorPrendaUnidad::find()->where(['=','idordenproduccion', $id])->all();
                                        foreach ($valor_prenda as $valor):
                                            $valor_total += $valor->total_pagar;   
                                        endforeach;  
                                        $utilidad = $model->totalorden - $valor_total;
                                        if($model->tipo->idtipo == 1 or $model->tipo->idtipo == 4){
                                            $buscar1 = $utilidad;
                                            $costo1 = $valor_total;
                                        }
                                        if($model->tipo->idtipo == 2){
                                            $buscar2 = $utilidad;
                                            $costo2 = $valor_total;
                                        }
                                        $porcentaje = round(((100 * $valor_total)/$model->totalorden),2);
                                         ?>
                                           <tr style="font-size: 85%;">
                                               <td><?= $id ?></td>
                                               <td><?= $model->tipo->tipo?></td>
                                               <td align="right"><?= ''.number_format($model->cantidad,0) ?></td>
                                               <td align="right"><?= ''.number_format($model->totalorden,0) ?></td>
                                               <td align="right"><?= ''.number_format($valor_total,0) ?></td>
                                               <td align="right"><?= ''.number_format($utilidad,0) ?></td>
                                               <td align="right"><?= ''.number_format($porcentaje,0) ?> %</td>
                                               <td align="right"><?= ''.number_format(100-$porcentaje,0) ?>%</td>
                                           </tr>
                                    <?php
                                     $total += $valor_total;
                                    $valor_total = 0;
                                    $total_ingresos += $model->totalorden; ?>
                                  
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
                                        <th scope="col" style='background-color:#B9D5CE;'>Valor compra</th>
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
                <!-- TERMINA TABS-->
            </div>  
    </div>
  <?php ActiveForm::end(); ?>  
</div>


   