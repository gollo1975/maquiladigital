<?php

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
/* @var $model app\models\Empleado */

$this->title = 'Confección (Balanceo/Preparación)';
$this->params['breadcrumbs'][] = ['label' => 'Detalle', 'url' => ['view_balanceo']];
$this->params['breadcrumbs'][] = $detalletallas->iddetalleorden;
$view = 'orden-produccion/vistatallas';
$orden = app\models\Ordenproduccion::findOne($detalletallas->idordenproduccion);
$operacionModulo = app\models\BalanceoDetalle::find()->where(['=','id_balanceo', $modulo])->orderBy('id_proceso DESC')->all();
?>
<div class="operarios-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view_balanceo', 'id' => $detalletallas->idordenproduccion], ['class' => 'btn btn-primary btn-sm']) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($detalletallas, 'Id') ?>:</th>
                    <td><?= Html::encode($detalletallas->iddetalleorden) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($detalletallas, 'Producto_Talla') ?>:</th>
                    <td><?= Html::encode($detalletallas->productodetalle->prendatipo->prenda.' / '.$detalletallas->productodetalle->prendatipo->talla->talla) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($detalletallas, 'Unidades') ?>:</th>
                    <td><?= Html::encode($detalletallas->cantidad) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($detalletallas, 'Op_Interna') ?>:</th>
                    <td><?= Html::encode($detalletallas->idordenproduccion) ?>  - Op Cliente: <?= Html::encode($orden->ordenproduccion) ?></td>
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
            ]); ?>
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#balanceo" aria-controls="balanceo" role="tab" data-toggle="tab">Balanceo: <span class="badge"><?= count($cantidades) ?></span></a></li>
            <li role="presentation"><a href="#preparacion" aria-controls="preparacion" role="tab" data-toggle="tab">Preparación: <span class="badge"><?= count($cantidad_preparacion) ?></span></a></li>
           
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="balanceo">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size: 85%;'>
                                        <td scope="col" style='background-color:#B9D5CE;'><b>Id</b></td>                        
                                        <td scope="col" style='background-color:#B9D5CE;'><b>Nro Balanceo</b></td>   
                                        <td scope="col" style='background-color:#B9D5CE;'><b>Cant.</b></td>     
                                        <th scope="col" style='background-color:#B9D5CE;'>Op</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>F. Entrada</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>F. Registro</th>
                                        <td scope="col"  style='background-color:#B9D5CE;'><b>Nro prendas</b></td>   
                                        <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_c =0;
                                    foreach ($cantidades as $val):
                                        if($val->id_proceso_confeccion == 1){
                                            $total_c += $val->cantidad_terminada;
                                            ?>
                                           <tr style='font-size: 85%;'>
                                            <td><?= $val->id_entrada ?></td>
                                            <td><?= $val->id_balanceo ?></td>
                                             <td><?= $val->nro_operarios ?></td>
                                            <td><?= $val->idordenproduccion ?></td>
                                            <td><?= $val->fecha_entrada ?></td>
                                            <td><?= $val->fecha_procesada ?></td>
                                            <td align = "right"><?= $val->cantidad_terminada ?></td>
                                            <td><?= $val->observacion ?></td>
                                            <?php if($detalletallas->faltante < $detalletallas->cantidad){?>
                                                <td style=' width: 25px;'>
                                                    <a href="#" data-toggle="modal" data-target="#entrada<?= $val->id_entrada ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                                                    <!-- Editar modal detalle -->
                                                    <div class="modal fade" role="dialog" aria-hidden="true" id="entrada<?= $val->id_entrada ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
                                                                    <h4 class="modal-title">Cantidades por talla: <?= $val->cantidad_terminada ?></h4>
                                                                </div>
                                                                <?= Html::beginForm(Url::toRoute(["orden-produccion/editarentrada", 'id_proceso_confeccion' => 1]), "POST") ?>
                                                                <div class="modal-body">
                                                                    <div class="panel panel-success">
                                                                        <div class="panel-heading">
                                                                            <h4>Detalle de la cantidad</h4>
                                                                        </div>
                                                                        <div class="panel-body">
                                                                            <div class="col-lg-2">
                                                                                <label>Cantidad:</label>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <input type="text" name="cantidad_terminada" value="<?= $val->cantidad_terminada ?>" class="form-control" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="panel-body">
                                                                            <div class="col-lg-2">
                                                                                <label>Observacion:</label>
                                                                            </div>
                                                                            <div class="col-lg-10">
                                                                                <input type="text" name="observacion" value="<?=  $val->observacion ?>" class="form-control" required>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="identrada" value="<?= $val->id_entrada ?>">
                                                                        <input type="hidden" name="iddetalleorden" value="<?= $detalletallas->iddetalleorden ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                                    <button type="submit" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span> Guardar</button>
                                                                     <?= Html::endForm() ?>
                                                                </div>

                                                            </div><!-- /.modal-content -->
                                                        </div><!-- /.modal-dialog -->
                                                    </div><!-- /.modal -->
                                                </td>   
                                            <?php }else{ ?>
                                                <td style=' width: 25px;'></td> 
                                            <?php }?>    
                                          </tr>

                                <?php }
                                endforeach; ?>
                                </tbody>
                                <td colspan="6"></td><td style="font-size: 85%; width: 140px; text-align: right; background: #4B6C67; color: #FFFFFF;"><b>Nro prendas:</b> <?= $total_c ?> <td colspan="2"></td>
                            </table>
                            <div class="panel-footer text-right">
                                <?= Html::a('<span class="glyphicon glyphicon-exportar"></span> Excel', ['cantidadconfeccionada', 'iddetalleorden' => $detalletallas->iddetalleorden, 'id_proceso_confeccion' => 1], ['class' => 'btn btn-primary btn-sm']);?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
            <!-- TERMINA EL TAB -->
             <div role="tabpanel" class="tab-pane" id="preparacion">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size: 85%;'>
                                        <td scope="col" style='background-color:#B9D5CE;'><b>Id</b></td>                        
                                        <td scope="col" style='background-color:#B9D5CE;'><b>Balanceo</b></td>   
                                        <td scope="col" style='background-color:#B9D5CE;'><b>Op_Int.</b></td>
                                        <td scope="col" style='background-color:#B9D5CE;'><b>Talla</b></td>
                                        <td scope="col" style='background-color:#B9D5CE;'><b>Operario</b></td>
                                        <td scope="col" style='background-color:#B9D5CE;'><b>Operación</b></td>
                                        <td scope="col"  style='background-color:#B9D5CE;'><b>Unidades</b></td>   
                                        <th scope="col" style='background-color:#B9D5CE;'>F. Entrada</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>F. Registro</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_c =0; $varActualizada = 0;
                                    $operacion = 0;
                                    $auxiliar = 0;
                                    foreach ($operacionModulo as $operaciones):
                                        $contarUnidades = 0;
                                        $varActualizada = 0;
                                        foreach ($cantidad_preparacion as $val):
                                            $total_c += $val->cantidad_terminada;
                                            $operacion = $val->total_operaciones;
                                            if($val->id_proceso == $operaciones->id_proceso && $val->id_operario == $operaciones->id_operario){
                                                $contarUnidades += $val->cantidad_terminada;
                                                      
                                               ?>
                                                <tr style='font-size: 85%;'>
                                                 <td><?= $val->id_entrada ?></td>
                                                 <td><?= $val->id_balanceo ?></td>
                                                  <td><?= $val->idordenproduccion ?></td>
                                                 <td><?= $val->detalleorden->productodetalle->prendatipo->prenda.'/'. $val->detalleorden->productodetalle->prendatipo->talla->talla?></td>
                                                 <td><?= $val->operario->nombrecompleto ?></td>
                                                 <td><?= $val->proceso->proceso ?></td>
                                                 <td align = "right"><?= $val->cantidad_terminada ?></td>
                                                 <td><?= $val->fecha_entrada ?></td>
                                                 <td><?= $val->fecha_procesada ?></td>
                                                 <td><?= $val->observacion ?></td>
                                                 <?php if($detalletallas->faltante < $detalletallas->cantidad){?>
                                                     <td style=' width: 25px;'>
                                                         <a href="#" data-toggle="modal" data-target="#entrada<?= $val->id_entrada ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                                                         <!-- Editar modal detalle -->
                                                         <div class="modal fade" role="dialog" aria-hidden="true" id="entrada<?= $val->id_entrada ?>">
                                                             <div class="modal-dialog">
                                                                 <div class="modal-content">
                                                                     <div class="modal-header">
                                                                         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
                                                                         <h4 class="modal-title">Cantidades por talla: <?= $val->cantidad_terminada ?></h4>
                                                                     </div>
                                                                     <?= Html::beginForm(Url::toRoute(["orden-produccion/editarentrada", 'id_proceso_confeccion' => 2]), "POST") ?>
                                                                     <div class="modal-body">
                                                                         <div class="panel panel-success">
                                                                             <div class="panel-heading">
                                                                                 <h4>Detalle de la cantidad</h4>
                                                                             </div>
                                                                             <div class="panel-body">
                                                                                 <div class="col-lg-2">
                                                                                     <label>Cantidad:</label>
                                                                                 </div>
                                                                                 <div class="col-lg-3">
                                                                                     <input type="text" name="cantidad_terminada" value="<?= $val->cantidad_terminada ?>" class="form-control" required>
                                                                                 </div>
                                                                             </div>
                                                                             <div class="panel-body">
                                                                                 <div class="col-lg-2">
                                                                                     <label>Observacion:</label>
                                                                                 </div>
                                                                                 <div class="col-lg-10">
                                                                                     <input type="text" name="observacion" value="<?=  $val->observacion ?>" class="form-control" required>
                                                                                 </div>
                                                                             </div>
                                                                             <input type="hidden" name="identrada" value="<?= $val->id_entrada ?>">
                                                                             <input type="hidden" name="iddetalleorden" value="<?= $detalletallas->iddetalleorden ?>">
                                                                         </div>
                                                                     </div>
                                                                     <div class="modal-footer">
                                                                         <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                                         <button type="submit" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span> Guardar</button>
                                                                          <?= Html::endForm() ?>
                                                                     </div>

                                                                 </div><!-- /.modal-content -->
                                                             </div><!-- /.modal-dialog -->
                                                         </div><!-- /.modal -->
                                                     </td>   
                                                 <?php }else{ ?>
                                                     <td style=' width: 25px;'></td> 
                                                 <?php }?>    
                                               </tr>
                                            <?php }
                                        endforeach;
                                        $varActualizada += $contarUnidades; 
                                        if($varActualizada > 0){
                                            ?>
                                              <td colspan="6"><td style="font-size: 85%; width: 100px; text-align: right; background: #4B6C67; color: #FFFFFF;"><b>Unidades: </b> <?= ''.number_format($varActualizada,0) ?></td><td colspan="1">
                                           <?php }else{?>
                                              <td></td>
                                           <?php }
                                        
                                    endforeach; ?>
                                          
                                </tbody>
                            </table>
                            <div class="panel-footer text-right">
                                <?= Html::a('<span class="glyphicon glyphicon-export"></span> Excel', ['cantidadconfeccionada', 'iddetalleorden' => $detalletallas->iddetalleorden, 'id_proceso_confeccion' => 2], ['class' => 'btn btn-primary btn-sm']);?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
            <!-- TERMINA EL TAB -->
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
