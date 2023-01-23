<?php
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\bootstrap\ActiveForm;
use yii\db\Expression;
use app\models\CantidadPrendaTerminadas;
use app\models\Balanceo;
use app\models\Horario;
use app\models\Ordenproduccion;
use yii\base\ErrorException;

$this->title = 'Eficiencia modular';
$this->params['breadcrumbs'][] = ['label' => 'Eficiencia', 'url' => ['view_consulta_ficha']];
$this->params['breadcrumbs'][] = $id_balanceo;

$cantidad_prendas= CantidadPrendaTerminadas::find()->where(['=','id_balanceo', $id_balanceo])->all(); 
$balanceo = Balanceo::find()->where(['=','id_balanceo', $id_balanceo])->one();
$orden_produccion = Ordenproduccion::findOne($balanceo->ordenproduccion->idordenproduccion); 
?>

<div class="orden-produccion-view">

 <!--<?= Html::encode($this->title) ?>-->
   
    <div class="panel panel-success">
        <div class="panel-heading">
            Modulo
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover" width="100%">
                <tr style ='font-size:95%;'>
                    <th><?= Html::activeLabel($balanceo, 'Nro_Balanceo') ?>:</th>
                    <td><?= Html::encode($balanceo->id_balanceo) ?></td>
                    <th><?= Html::activeLabel($balanceo, 'fecha_inicio') ?>:</th>
                    <td><?= Html::encode($balanceo->fecha_inicio) ?></td>
                     <th><?= Html::activeLabel($balanceo, 'fecha_terminacion') ?></th>
                    <td><?= Html::encode($balanceo->fecha_terminacion) ?></td>
                    <th><?= Html::activeLabel($balanceo, 'Minutos_Proveedor') ?>:</th>
                    <td><?= Html::encode($orden_produccion->duracion) ?></td>
                    </tr>   
                <tr style ='font-size:95%;'>
                       <th><?= Html::activeLabel($balanceo, 'Minutos_Confección') ?>:</th>
                    <td><?= Html::encode($balanceo->total_minutos) ?></td>
                     <th><?= Html::activeLabel($balanceo, 'Minutos_Balanceo') ?>:</th>
                    <td><?= Html::encode($balanceo->tiempo_balanceo) ?></td>
                    <th><?= Html::activeLabel($balanceo, 'Tiempo_Operario') ?>:</th>
                     <td><?= Html::encode($balanceo->tiempo_operario) ?></td>
                    <th><?= Html::activeLabel($balanceo, 'Usuario') ?>:</th>
                    <td colspan="5"><?= Html::encode($balanceo->usuariosistema) ?></td>
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
   <!-- COMIENZA EL TAB-->
   <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#listado" aria-controls="listado" role="tab" data-toggle="tab">Listado <span class="badge"><?= count($cantidad_prendas)?></span> </a></li>
            <li role="presentation"><a href="#eficiencia" aria-controls="eficiencia" role="tab" data-toggle="tab">Eficiencia <span class="badge"><?= count($unidades)?></span></a></li>
        </ul>
        <div class="tab-content" >
            <div role="tabpanel" class="tab-pane active" id="listado">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr align="center" >
                                        <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>   
                                        <th scope="col" style='background-color:#B9D5CE;'>Nro Unidades</th>  
                                        <th scope="col" style='background-color:#B9D5CE;'>Hora corte</th>  
                                        <th scope="col" style='background-color:#B9D5CE;'>Facturación</th>  
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha confección</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha/hora Confección</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Observación</th>                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($cantidad_prendas as $val):?>
                                        <tr style ='font-size:85%;'>
                                            <td><?= $val->detalleorden->productodetalle->prendatipo->prenda. ' / '. $val->detalleorden->productodetalle->prendatipo->talla->talla?></td>
                                            <td><?= $val->cantidad_terminada ?></td>  
                                             <td><?= $val->hora_corte_entrada ?></td>  
                                            <td align="right"><?= ''. number_format($val->detalleorden->vlrprecio * $val->cantidad_terminada,0) ?></td>
                                            <td ><?= $val->fecha_entrada ?></td>
                                            <td ><?= $val->fecha_procesada ?></td>
                                            <td ><?= $val->usuariosistema ?></td>
                                            <td ><?= $val->observacion ?></td>
                                        </tr>
                                    <?php endforeach;?>
                               </tbody>
                            </table>
                        </div>    
                   </div> 
                </div>
            </div>
        <!-- TERMINA TAB-->
        <div role="tabpanel" class="tab-pane" id="eficiencia">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE; width: 15%'>Fecha confección</th>   
                                        <th scope="col" style='background-color:#B9D5CE; width: 15%'>Cantidad operarios</th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 15%'>Unidades x operarios(100%) </th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 15%'>Cantidad x dia(100%))</th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 15%'>Unidades confeccionadas</th> 
                                        <th scope="col" style='background-color:#B9D5CE; width: 15%'>Cumplimiento</th>  
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($eficiencia as $eficiencia):?>
                                        <tr style="font-size: 85%;">
                                           <td ><?= $eficiencia->fecha_confeccion ?></td>
                                           <td align="right"><?= $eficiencia->nro_operarios ?></td>
                                           <td align="right"><?= $eficiencia->unidades_por_operarios ?></td>
                                           <td align="right"><?= $eficiencia->cantidad_por_dia ?></td>
                                           <td align="right"><?= $eficiencia->unidades_confeccionadas ?></td>
                                           <td align="right"><?= $eficiencia->porcentaje_cumplimiento ?>%</td>
                                        </tr>
                                    <?php endforeach;
                                    ?>
                                    <td colspan="5"><td style="font-size: 90%;background: #4B6C67; color: #FFFFFF; width: 142px;" align="right"><b>Eficiencia modulo:</b> <?= $balanceo->total_eficiencia ?> %</td>
                               </tbody>
                            </table>
                        </div>    
                   </div> 
                </div>
            </div>
            <!---TERMINA TAB-->
    </div> 
</div>
<?php $form->end() ?> 

    


