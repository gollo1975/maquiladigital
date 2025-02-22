<?php


use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Notacredito;
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
use app\models\Notacreditodetalle;
use app\models\Cliente;
use app\models\Producto;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;


/* @var $this yii\web\View */
/* @var $model app\models\Notacredito */

$this->title = 'Detalle Nota de Crédito';
$this->params['breadcrumbs'][] = ['label' => 'Notas Créditos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idnotacredito;
$view = 'notacredito';
$nota = Notacredito::findOne($model->idnotacredito);
?>
<div class="notacredito-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->idnotacredito], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php if ($model->autorizado == 0) { ?>
            <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->idnotacredito], ['class' => 'btn btn-default btn-sm']) ?>
        <?php }else {
            if($model->autorizado == 1 && $model->numero == 0){
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->idnotacredito], ['class' => 'btn btn-default btn-sm']);
                echo Html::a('<span class="glyphicon glyphicon-send"></span>  Generar nota credito', ['generar_documento', 'id' => $model->idnotacredito],['class' => 'btn btn-success btn-xs' ,
                    'data' => ['confirm' => 'Esta seguro de Generar la Nota Credito para ser enviada a la DIAN. Tener presente que se actualiza el estado de la factura.', 'method' => 'post']]);
                echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir PDF', ['imprimir', 'id' => $model->idnotacredito], ['class' => 'btn btn-default btn-sm']);            
            }else{  
                if($nota->cude == ''){?>
                    <?= Html::a('<span class="glyphicon glyphicon-send"></span>  Enviar nota credio a la Dian', ['enviar_nota_credito_dian', 'id' => $model->idnotacredito],['class' => 'btn btn-success btn-xs',  'id' => 'my_button', 'onclick' => '$("#my_button").attr("disabled", "disabled")' ,
                     'data' => ['confirm' => 'Esta seguro de enviar la Nota Credito No  '. $model->numero. ' a la DIAN', 'method' => 'post']]);?>
                     <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir PDF', ['imprimir', 'id' => $model->idnotacredito], ['class' => 'btn btn-default btn-sm']);  ?>         
           <?php }else{ ?>
               <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir PDF', ['imprimir', 'id' => $model->idnotacredito], ['class' => 'btn btn-default btn-sm']);   ?>          
            <?php }
        }
        }  
        ?>
    </p>

    <div class="panel panel-success">
        <div class="panel-heading">
            Nota crédito
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idnotacredito') ?>:</th>
                    <td><?= Html::encode($model->idnotacredito) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?>:</th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Subtotal') ?>:</th>
                    <td align="right"><?= Html::encode('$ '.number_format($model->valor,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero') ?>:</th>
                    <td><?= Html::encode($model->numero) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha') ?>:</th>
                    <td><?= Html::encode($model->fecha) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Iva') ?>: +</th>
                    <td align="right"><?= Html::encode('$ '.number_format($model->iva,0)) ?></td>
                </tr>
              <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'autorizado') ?>:</th>
                    <td><?= Html::encode($model->autorizar) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'usuariosistema') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ReteIva') ?>: -</th>
                    <td align="right"><?= Html::encode('$ '.number_format($model->reteiva,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_documento') ?>:</th>
                    <td><?= Html::encode($model->documentoeletronico->nombre_documento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_concepto') ?>:</th>
                  <td><?= Html::encode($model->motivoNota->concepto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ReteFuente') ?>: -</th>
                    <td align="right"><?= Html::encode('$ '.number_format($model->retefuente,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechapago') ?>:</th>
                    <td><?= Html::encode($model->fechapago) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td><?= Html::encode($model->observacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total') ?>:</th>
                    <td align="right"><?= Html::encode('$ '.number_format($model->total,0)) ?></td>
                </tr>
               <tr style="font-size: 85%;">
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cude') ?>:</th>
                    <td colspan="6"><?= Html::encode($model->cude) ?></td> 
               </tr>
            </table>
        </div>
    </div>

    <div class="table-responsive">
        <div class="panel panel-success ">
            <div class="panel-heading">
                Detalles de la nota credito
            </div>
            <div class="panel-body">
                 <table class="table table-bordered table-hover">
                    <thead>
                    <tr style="font-size: 85%;">
                        <th scope="col" style='background-color:#B9D5CE;'>Nro Factura</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Concepto</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Saldo</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Cant.</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Vl. unitario</th> 
                        <th scope="col" style='background-color:#B9D5CE;'>% Iva</th>
                        <th scope="col" style='background-color:#B9D5CE;'>% Retencion</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Subtotal</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Total nota</th>
                        <th scope="col" style='background-color:#B9D5CE;'></th>
                        <th scope="col" style='background-color:#B9D5CE;'></th>
                       
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($modeldetalles as $val): ?>
                    <tr style="font-size: 85%;">
                        <td><?= $val->nrofactura ?></td>
                        <?php if ($val->id <> ''){?>
                            <td><?= $val->conceptoFacturacion->concepto?></td>
                        <?php }else{?>
                            <td><?= 'NO FOUND'?></td>
                        <?php }?>    
                        <td style="text-align: right"><?= '$'.number_format($val->saldo_factura,0) ?></td>
                        <td style="text-align: right"><?= $val->cantidad ?></td>
                        <td style="text-align: right"><?= $val->precio_unitario ?></td>
                        <td style="text-align: right"><?= $val->porcentaje_iva ?></td>
                        <td style="text-align: right"><?= $val->porcentaje_retefuente ?></td>
                        <td style="text-align: right"><?= '$'.number_format($val->valor_nota_credito,0) ?></td>
                         <td style="text-align: right"><?= '$'.number_format($val->total_nota,0) ?></td>
                        <?php if ($model->autorizado == 0 ) {
                                if ($model->motivoNota->codigo_interno <> 3 ){?>
                                    <td style="width: 20px; height: 20px"> 
                                         <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ',
                                             ['notacredito/editardetalle', 'id' => $model->idnotacredito, 'id_detalle' => $val->iddetallenota],
                                               ['title' => 'Permite ingresar las cantidades a devolver',
                                                'data-toggle'=>'modal',
                                                'data-target'=>'#modaleditardetalle',
                                                'class' => '',
                                                'data-backdrop' => 'static',
                                                'data-keyboard' => 'false'
                                               ]);?> 
                                        <div class="modal remote fade" id="modaleditardetalle">
                                            <div class="modal-dialog modal-lg" style ="width: 500px;">    
                                                <div class="modal-content"></div>
                                            </div>
                                        </div>
                                    </td>
                                <?php }else{?>
                                    <td style="width: 20px; height: 20px; height: 20px"></td>
                                <?php }?>    
                           <td style="width: 20px; height: 20px"> 
                                <!-- Eliminar modal detalle -->
                                <a href="#" data-toggle="modal" data-target="#iddetallenota<?= $val->iddetallenota ?>"><span class="glyphicon glyphicon-trash"></span></a>
                                <div class="modal fade" role="dialog" aria-hidden="true" id="iddetallenota<?= $val->iddetallenota ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                <h4 class="modal-title">Eliminar Detalle</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Realmente deseas eliminar el registro con código <?= $val->iddetallenota ?>?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <?= Html::beginForm(Url::toRoute("notacredito/eliminardetalle"), "POST") ?>
                                                <input type="hidden" name="iddetallenota" value="<?= $val->iddetallenota ?>">
                                                <input type="hidden" name="idnotacredito" value="<?= $model->idnotacredito ?>">
                                                <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Eliminar</button>
                                                <?= Html::endForm() ?>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            </td>
                        <?php }else{ ?>
                            <td style="width: 20px; height: 20px"></td>
                            <td style="width: 20px; height: 20px"></td>
                        <?php } ?>    
                    </tr>
                    </tbody>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php
            $contador = count($modeldetalles);
            if ($model->autorizado == 0 && $contador == 0){ ?>
                <div class="panel-footer text-right">
                    <?= Html::a('<span class="glyphicon glyphicon-search"></span> Search factura', ['notacredito/nuevodetalles', 'idnotacredito' => $model->idnotacredito,'idcliente' => $model->idcliente], ['class' => 'btn btn-info btn-sm']) ?>
                    
                </div>
            <?php } ?>
        </div>
    </div>


</div>