<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Compra;
use yii\helpers\Url;
use yii\web\Session;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $model app\models\Compra */

$this->title = 'Detalle Compra';
$this->params['breadcrumbs'][] = ['label' => 'Compras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_compra;
$view = 'compra';
?>

<div class="compra-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->id_compra], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php if ($model->autorizado == 0) { ?>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_compra], ['class' => 'btn btn-success btn-sm']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->id_compra, 'token' => $token], ['class' => 'btn btn-default btn-sm']); 
        } else {
            echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->id_compra, 'token' => $token], ['class' => 'btn btn-default btn-sm']);
            echo Html::a('<span class="glyphicon glyphicon-check"></span> Generar', ['generarnro', 'id' => $model->id_compra, 'token' => $token], ['class' => 'btn btn-default btn-sm']);
            if (($model->numero > 0)){                
                echo Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 7, 'codigo' => $model->id_compra,'view' => $view, 'token' => $token], ['class' => 'btn btn-default btn-sm']);?>                                                         
                <?php if($model->saldo == $model->total ){ ?>
                    <td style="width: 25px; height: 25px;">
                        <!-- Inicio Nuevo Detalle proceso -->
                          <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Cambiar impuesto ',
                              ['/compra/cambiar_iva', 'id' => $model->id_compra],
                              [
                                  'title' => 'Cambiar el iva',
                                  'data-toggle'=>'modal',
                                  'data-target'=>'#modalcambiariva'.$model->id_compra,
                                  'class' => 'btn btn-info btn-sm',
                                  'data-backdrop' => 'static',

                              ])    
                         ?>
                    </td>  
                <?php }
                if($model->proveedor->tiporegimen == 2){?>
                    <td style="width: 25px; height: 25px;">
                             <!-- Inicio Nuevo Detalle proceso -->
                               <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear documento soporte ',
                                   ['/compra/crear_documento_soporte', 'id' => $model->id_compra],
                                   [
                                       'title' => 'Permite crear el documento soporte desde compras',
                                       'data-toggle'=>'modal',
                                       'data-target'=>'#modalcreardocumentosoporte'.$model->id_compra,
                                       'class' => 'btn btn-success btn-sm',
                                       'data-backdrop' => 'static',

                                   ])    
                              ?>
                     </td> 
                <?php }?>     
                <div class="modal remote fade" id="modalcambiariva<?= $model->id_compra ?>">
                          <div class="modal-dialog modal-lg" style ="width: 550px;">
                              <div class="modal-content"></div>
                          </div>
                      </div>
                </td>
                <div class="modal remote fade" id="modalcreardocumentosoporte<?= $model->id_compra ?>">
                          <div class="modal-dialog modal-lg" style ="width: 650px;">
                              <div class="modal-content"></div>
                          </div>
                      </div>
                </td>
            
        <?php } 
        }?>
    </p>
    
    <div class="panel panel-success">
        <div class="panel-heading">
            <h5><?= Html::encode($this->title) ?></h5>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%">
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'id_compra') ?>:</th>
                    <td><?= Html::encode($model->id_compra) ?></td>
                    <th style='background-color:#F0F3EF; '><?= Html::activeLabel($model, 'Proveedor') ?>:</th>
                    <td><?= Html::encode($model->proveedor->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF; '><?= Html::activeLabel($model, 'subtotal') ?>:</th>
                    <td style="text-align: right"><?= Html::encode('$ '.number_format($model->subtotal,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'concepto') ?>:</th>
                    <td><?= Html::encode($model->compraConcepto->concepto) ?></td>
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'porcentajeAiu') ?>:</th>
                    <td><?= Html::encode($model->porcentajeaiu) ?></td>
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'baseAiu') ?>:</th>
                    <td style="text-align: right"><?= Html::encode('$ '.number_format($model->base_aiu,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'factura') ?>:</th>
                    <td><?= Html::encode($model->factura) ?></td>
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'porcentajeiva') ?>:</th>
                    <td><?= Html::encode($model->porcentajeiva) ?></td>
                    <th style='background-color:#F0F3EF; '><?= Html::activeLabel($model, 'impuestoiva') ?>: +</th>
                    <td style="text-align: right"><?= Html::encode('$ '.number_format($model->impuestoiva,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'numero') ?>:</th>
                    <td><?= Html::encode($model->numero) ?></td>
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'porcentajereteiva') ?>: </th>
                    <td><?= Html::encode($model->porcentajereteiva) ?></td>                    
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'retencioniva') ?>: - </th>
                    <td style="text-align: right"><?= Html::encode('$ '.number_format($model->retencioniva,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'fechainicio') ?>:</th>
                    <td><?= Html::encode($model->fechainicio) ?></td>                    
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'porcentajefuente') ?>: </th>
                    <td><?= Html::encode($model->porcentajefuente) ?></td>
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'retencionfuente') ?>: -</th>
                    <td style="text-align: right"><?= Html::encode('$ '.number_format($model->retencionfuente,0)) ?></td>
                </tr> 
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechavencimiento') ?>:</th>
                    <td><?= Html::encode($model->fechavencimiento) ?></td>                    
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'usuariosistema') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'total') ?>:</th>
                    <td style="text-align: right"><?= Html::encode('$ '.number_format($model->total,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'id_tipo_compra') ?>:</th>
                    <td><?= Html::encode($model->tipoCompra->descripcion) ?></td>
                    <?php if($model->id_planta != null){?>
                        <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'id_planta') ?>:</th>
                        <td><?= Html::encode($model->plantaEmpresa->nombre_planta) ?></td>
                    <?php }else{?>
                        <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'id_planta') ?>:</th>
                        <td><?= Html::encode('NOT FOUND') ?></td>
                        
                    <?php }?> 
                    <th style='background-color:#F0F3EF;'></th>
                    <td></td>
                </tr>
                 <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;' ><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan ="6"><?= Html::encode($model->observacion) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
