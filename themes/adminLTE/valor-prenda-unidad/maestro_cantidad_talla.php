<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use kartik\depdrop\DepDrop;
use yii\widgets\DetailView;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Session;
//modelos
$this->title = '('. $model->planta->nombre_planta. '- TALLA ' .$talla->productodetalle->prendatipo->talla->talla .')';
$this->params['breadcrumbs'][] = ['label' => 'Cantida unidades por talla', 'url' => ['search_tallas_ordenes' ,'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion, 'id_planta' =>$model->id_planta]];
$this->params['breadcrumbs'][] = $model->id_valor;
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['search_tallas_ordenes', 'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion, 'id_planta' =>$model->id_planta, 'tokenPlanta' => $tokenPlanta, 'tipo_pago' => $tipo_pago], ['class' => 'btn btn-primary btn-sm']) ?>
</p>
<div class="panel panel-success">
    <div class="panel-heading">
        Detalle del registro  
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
            <tr style="font-size: 85%;">
                <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'No_pago') ?>:</th>
                <td><?= Html::encode($model->id_valor) ?></td>
                <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_Orden') ?>:</th>
                <td><?= Html::encode($model->idordenproduccion) ?><b> Ref:</b> <?= Html::encode($model->ordenproduccion->codigoproducto) ?></td>
                <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?>:</th>
                <td><?= Html::encode($model->ordenproduccion->cliente->nombrecorto) ?></td>
                <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Vr_contrato') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($model->vlr_contrato,0)) ?></td>
                <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Vr_Vinculado') ?>:</th>
                <td align="right"><?= Html::encode('$'.number_format($model->vlr_vinculado,0)) ?></td>
           </tr>  
            <tr style="font-size: 85%;">
                 
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'F._Editado') ?>:</th>
                    <td><?= Html::encode($model->fecha_editado) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cantidad_operaciones') ?>:</th>
                    <td><?= Html::encode($detalle_op->cantidad_operaciones) ?></td>
                       <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cantidad_confeccionada') ?>:</th>
                   <td align="right"><?= Html::encode(''.number_format($detalle_op->cantidad_confeccionada,0)) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Total_Ajuste') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($model->total_ajuste,0)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_confeccion') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($detalle_op->costo_confeccion,0)) ?></td>
                 </tr>  
        </table>
    </div>
</div>
<?php
 $formulario = ActiveForm::begin([
    
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-3 control-label'],
                    'options' => []
                ],

]);
?>
<div>
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#listado_registro" aria-controls="listado_registro" role="tab" data-toggle="tab">Detalle de pago <span class="badge"><?= $pagination->totalCount ?></span></a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="listado_registro">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr style='font-size:85%;'>
                                    <th scope="col" style='background-color:#B9D5CE; '>Operario</th>                        
                                    <th scope="col" style='background-color:#B9D5CE;'>Sabado</th> 
                                     <th scope="col" style='background-color:#B9D5CE;'>Operación</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>Dia</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Cant.</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>Valor</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>Vr.Pagar</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>Costo</th>
                                      <th scope="col" style='background-color:#B9D5CE;'>Meta</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>% Cum.</th> 
                                    <th scope="col" style='background-color:#B9D5CE;'>Observación</th> 

                                </tr>
                            </thead>
                            <?php
                            foreach ($listado_confeccion as $val):?>
                                <tr style='font-size: 85%;'> 
                                    <td><?= $val->operarioProduccion->nombrecompleto?></td>
                                    <td><?= $val->aplicaSabado?></td>
                                    <td><?= $val->operacionPrenda?></td>
                                    <td><?= $val->dia_pago?></td>
                                    <td><?= $val->cantidad?></td>
                                    <td style="text-align: right"><?= ''.number_format($val->vlr_prenda,0)?></td>
                                    <td style="text-align: right"><?= ''.number_format($val->vlr_pago,0)?></td>
                                     <td style="text-align: right"><?= ''.number_format($val->costo_dia_operaria,0)?></td>
                                    <td><?= $val->meta_diaria?></td>
                                    <td><?= $val->porcentaje_cumplimiento?>%</td>
                                   <td><?= $val->observacion?></td>
                                </tr>    
                            <?php endforeach;?>
                        </table>
                         <div class="panel-footer text-right">
                            <?= Html::a('<span class="glyphicon glyphicon-export"></span> Exportar excel', ['generar_excel_talla', 'id' => $model->id_valor, 'id_detalle' => $id_detalle], ['class' => 'btn btn-success btn-sm ']); ?>
                         </div>     
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  
<?php ActiveForm::end(); ?>
<?= LinkPager::widget(['pagination' => $pagination]) ?>