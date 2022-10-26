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
//$unidades= CantidadPrendaTerminadas::find()->where(['=','id_balanceo', $id_balanceo])->groupBy('fecha_entrada')->all(); 
$balanceo = Balanceo::find()->where(['=','id_balanceo', $id_balanceo])->one();
$horario = Horario::findOne(1);
$calculo = 0;
 try {
        $calculo = round((60/$balanceo->tiempo_balanceo) *($horario->total_horas));
    } catch (ErrorException $e) {
        Yii::$app->getSession()->setFlash('warning', 'Error en la division por ceros en el tabs de eficiencia.');
    }

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
                                        <th scope="col" style='background-color:#B9D5CE; width: 15%'>Dias confección</th>   
                                        <th scope="col" style='background-color:#B9D5CE; width: 15%'>Unidades Confeccionadas</th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 15%'>Nro Operarios </th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 15%'>Unidad x Operario(100%)</th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 15%'>Cantidad x Dia(100%)</th> 
                                        <th scope="col" style='background-color:#B9D5CE; width: 15%'>Cumplimiento</th>  
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $fecha_entrada = 0;
                                    $suma = 0;
                                    $total = 0;
                                    $contador = 0;
                                    $cumplimiento = 0;
                                    $aux1 = 0; $aux2 = 0; $calculo_dia = 0;
                                    $con = 0;
                                     foreach ($unidades as $eficiencia):
                                           $con += 1;
                                           $fecha_entrada = $eficiencia->fecha_entrada;
                                           $total = 0;
                                           $var_1 = CantidadPrendaTerminadas::find()->where(['=','fecha_entrada', $fecha_entrada])->andWhere(['=','id_balanceo', $balanceo->id_balanceo])->all();
                                           foreach ($var_1 as $dato_1):
                                                    $total +=  1;
                                           endforeach;
                                           if($total == 1){
                                                $suma = 0;  
                                                $var_2 = CantidadPrendaTerminadas::find()->where(['=','fecha_entrada', $fecha_entrada])->andWhere(['=','id_balanceo', $balanceo->id_balanceo])->one();
                                                 $horario = Horario::findOne($balanceo->id_horario);
                                                if($balanceo->fecha_inicio === $fecha_entrada){
                                                    if($balanceo->hora_final_modulo == 0){
                                                        $calculo = round((60 / $balanceo->tiempo_balanceo) * $balanceo->total_horas); 
                                                    }else{
                                                        if(count($unidades) > 1){
                                                            $calculo = round((60 / $balanceo->tiempo_balanceo) * $balanceo->total_horas);
                                                        }else{
                                                             $calculo = round((60 / $balanceo->tiempo_balanceo) * $balanceo->hora_final_modulo);
                                                        }    
                                                    }
                                                }else{
                                                     if($balanceo->hora_final_modulo == 0){
                                                         $calculo = round((60 / $balanceo->tiempo_balanceo) * $horario->total_horas); 
                                                     }else{
                                                         $calculo = round((60 / $balanceo->tiempo_balanceo) * $balanceo->hora_final_modulo); 
                                                     }
                                                }     
                                                $calculo_dia = round($calculo * $eficiencia->nro_operarios);
                                                $suma =   $eficiencia->cantidad_terminada;
                                               try {
                                                     $cumplimiento = round(($suma * 100)/$calculo_dia,2);
                                                 } catch (ErrorException $e) {
                                                     Yii::$app->getSession()->setFlash('warning', 'Error en la division por ceros en el tabs de eficiencia.');
                                                 }

                                                $aux1 += $cumplimiento;?>
                                                <tr style="font-size: 85%;">
                                                   <td ><?= $eficiencia->fecha_entrada ?></td>
                                                   <td ><?= $suma ?></td>
                                                   <td ><?= $eficiencia->nro_operarios ?></td>
                                                   <td align="right"><?= $calculo ?></td>
                                                   <td align="right"><?= $calculo_dia ?></td>
                                                   <td align="right"><?= $cumplimiento ?>%</td> 
                                                </tr>
                                              <?php 
                                           }else{
                                                $calculo = 0;
                                                $suma = 0;
                                                $var_3 = CantidadPrendaTerminadas::find()->where(['=','fecha_entrada', $fecha_entrada])->andWhere(['=','id_balanceo', $balanceo->id_balanceo])->all();
                                                foreach ($var_3 as $dato):    
                                                $suma += $dato->cantidad_terminada;
                                                endforeach;
                                                if($balanceo->fecha_inicio === $fecha_entrada){
                                                    if($balanceo->hora_final_modulo == 0){
                                                        $calculo = round((60 / $balanceo->tiempo_balanceo) * $balanceo->total_horas); 
                                                    }else{
                                                       if(count($unidades) > 1){
                                                            $calculo = round((60 / $balanceo->tiempo_balanceo) * $balanceo->total_horas);
                                                        }else{
                                                             $calculo = round((60 / $balanceo->tiempo_balanceo) * $balanceo->hora_final_modulo);
                                                        }    
                                                    }
                                                }else{
                                                    $horario = Horario::findOne($balanceo->id_horario);
                                                     if($balanceo->hora_final_modulo == 0){
                                                         $calculo = round((60 / $balanceo->tiempo_balanceo) * $horario->total_horas); 
                                                     }else{
                                                         $calculo = round((60 / $balanceo->tiempo_balanceo) * $balanceo->hora_final_modulo); 
                                                     }
                                                }     
                                                $calculo_dia = round($calculo * $eficiencia->nro_operarios);
                                                 try {
                                                    $cumplimiento = round(($suma * 100)/$calculo_dia,2);
                                                } catch (ErrorException $e) {
                                                    Yii::$app->getSession()->setFlash('warning', 'Error en la division por ceros en el tabs de eficiencia.');
                                                }

                                                $aux2 += $cumplimiento;?>
                                              <tr style="font-size: 85%;">
                                                 <td ><?= $eficiencia->fecha_entrada ?></td>
                                                 <td ><?= $suma ?></td>
                                                  <td ><?= $eficiencia->nro_operarios ?></td>
                                                 <td align="right"><?= $calculo ?></td>
                                                 <td align="right"><?= $calculo_dia ?></td>
                                                 <td align="right"><?= $cumplimiento ?>%</td>
                                              </tr>
                                           <?php
                                           }
                                    endforeach;
                                    $efectividad = round(($aux1 + $aux2) / $con,2) ;      
                                           
                                    ?>
                                    <td colspan="5"><td style="font-size: 90%;background: #4B6C67; color: #FFFFFF; width: 142px;" align="right"><b>Eficiencia modulo:</b> <?= $efectividad ?>%</td>
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

    


