<?php
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\bootstrap\ActiveForm;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use app\models\Balanceo;
use app\models\Ordenproduccion;
$this->title = 'Detalle del reproceso';
$this->params['breadcrumbs'][] = ['label' => 'Reproceso'];
$this->params['breadcrumbs'][] = $id_balanceo;

//vectores
$detalle_orden = app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $id])->all(); 

$balanceo = Balanceo::find()->where(['=','id_balanceo', $id_balanceo])->one();
$orden_produccion = Ordenproduccion::findOne($id); 
?>

<div class="orden-produccion-view">
    </p>
       <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view_reproceso_produccion', 'id' => $id], ['class' => 'btn btn-primary btn-sm']); ?>

        <?php if($balanceo->activo_reproceso == 0){?>
            <?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar modulo', ['cerrarmodulo', 'id_balanceo' => $id_balanceo, 'id' => $id],['class' => 'btn btn-warning btn-xs',
                'data' => ['confirm' => 'Esta seguro de cerrar el balanceo Nro: '. $id_balanceo. '', 'method' => 'post']]);
        }?>   
        
   </p><!-- comment -->  
    <div class="panel panel-success">
        <div class="panel-heading">
            Modulo
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover" width="100%">
                <tr style ='font-size:95%;'>
                    <th><?= Html::activeLabel($balanceo, 'Nro_Balanceo') ?>:</th>
                    <td><?= Html::encode($balanceo->id_balanceo) ?> - ( <?= Html::encode($id) ?>)</td>
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
            <li role="presentation" class="active"><a href="#balanceo" aria-controls="balanceo" role="tab" data-toggle="tab">Balanceo <span class="badge"><?= count($balanceo_detalle)?></span> </a></li>
            <li role="presentation" ><a href="#reproceso_confeccion" aria-controls="reproceso_confeccion" role="tab" data-toggle="tab">Rep_Confección <span class="badge"><?= count($reproceso_confeccion) ?></span></a></li>
            <li role="presentation" ><a href="#reproceso_terminacion" aria-controls="reproceso_terminacion" role="tab" data-toggle="tab">Rep_Terminación <span class="badge"><?= count($reproceso_terminacion) ?></span></a></li>
        </ul>
        <div class="tab-content" >
              <div role="tabpanel" class="tab-pane active" id="balanceo">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    
                                    <tr style="font-size: 85%;">
                                        <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operaciones</th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 70px;'>Proceso</th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 70px;'>Cant.</th>    
                                        <?php 
                                        if (count($detalle_orden)== 2 ){?>
                                          <th scope="col" style='background-color:#B9D5CE; text-align: center;'  colspan="2">Tallas</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                        <?php } ?>
                                           <?php if (count($detalle_orden)== 3 ){?>
                                          <th scope="col" style='background-color:#B9D5CE; text-align: center;'  colspan="3">Tallas</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                        <?php } ?>
                                           <?php if (count($detalle_orden)== 4 ){?>
                                          <th scope="col" style='background-color:#B9D5CE; text-align: center;'  colspan="4">Tallas</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                        <?php } ?>
                                           <?php if (count($detalle_orden)== 5 ){?>
                                          <th scope="col" style='background-color:#B9D5CE; text-align: center;'  colspan="5">Tallas</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                        <?php } ?>
                                           <?php if (count($detalle_orden)== 6 ){?>
                                          <th scope="col" style='background-color:#B9D5CE; text-align: center;'  colspan="6">Tallas</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                        <?php } ?>
                                          <?php if (count($detalle_orden)== 7 ){?>
                                          <th scope="col" style='background-color:#B9D5CE; text-align: center;'  colspan="7">Tallas</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                        <?php } ?> 
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    
                                    foreach ($balanceo_detalle as $index => $val): // Added $index to uniquely identify each row ?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $val->operario->nombrecompleto ?></td>
                                            <td><?= $val->proceso->proceso ?></td>
                                            <td align="center">
                                                <select name="tipo_reproceso[<?= $val->id_detalle ?>]" style="width: 85px;">
                                                    <option value="1">Confeccion</option>
                                                    <option value="2">Terminacion</option>
                                                </select>
                                            </td>
                                            <td><input type="text" style="width: 40px;" name="cantidad[<?= $val->id_detalle ?>]" value="0" required></td>

                                            <?php foreach ($detalle_orden as $talla): ?>
                                                <td>
                                                    <input type="checkbox" name="id_talla[<?= $val->id_detalle ?>][]" value="<?= $talla->idproductodetalle ?>">
                                                    Talla (<?= $talla->productodetalle->prendatipo->talla->talla ?>)
                                                </td>
                                            <?php endforeach; ?>

                                            <td style="width: 50px;"><input type="text" name="observacion[<?= $val->id_detalle ?>]" value="" maxlength="27"></td>
                                            <input type="hidden" name="iddetalle[]" value="<?= $val->id_detalle ?>">
                                        </tr>
                                    <?php endforeach; ?>
                                                                    </tbody>  
                            </table>
                        </div>
                           <?php if($balanceo->activo_reproceso == 0){?>
                            <div class="panel-footer text-right">

                                <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar datos", ["class" => "btn btn-success btn-sm"]) ?>
                            </div>
                        <?php }?>                 
                         
                    </div>
                </div>    
            </div>
            <!-- TERMINA TAB -->
             <div role="tabpanel" class="tab-pane" id="reproceso_confeccion">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operacion</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Modulo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Op</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>Reproceso</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>F_registro.</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                            
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0; $total = 0; $dato = 0;
                                    foreach ($detalle_orden as $tallas):
                                        $suma = 0;
                                       $total += $tallas->cantidad;
                                        foreach ($reproceso_confeccion as $val):
                                            if($tallas->idproductodetalle == $val->idproductodetalle){
                                               $suma += $val->cantidad;  
                                                ?>
                                                 <tr style="font-size: 85%; ">
                                                     <td><?= $val->operario->nombrecompleto ?></td>
                                                      <td><?= $val->productodetalle->prendatipo->prenda .' / '.$val->productodetalle->prendatipo->talla->talla .' - ('. $tallas->cantidad.')'?></td>
                                                     <td><?= $val->proceso->proceso ?></td>
                                                     <td><?= $val->id_balanceo ?></td>
                                                     <td><?= $val->idordenproduccion ?></td>
                                                     <td><?= $val->cantidad ?></td>
                                                     <td><?= round(($suma / $tallas->cantidad)*100,2)?></td>
                                                      <td><?= $val->fecha_registro ?></td>
                                                     <td><?= $val->observacion ?></td>
                                                    
                                                 </tr>
                                           <?php }
                                        endforeach;
                                        $contador = $suma; 
                                        $dato += $contador;
                                        $contador = round(($dato / $total)*100,2); 
                                        if($suma == 0){
                                        }else{?>
                                         <td colspan="6"><td style="font-size: 85%; width: 100px; text-align: right; background: #4B6C67; color: #FFFFFF;"><b>Unidades: </b> <?= ''.number_format($suma,0) ?></td> <td style="font-size: 85%; width: 130px; text-align: right; background: #4B6C67; color: #FFFFFF;"><b>Reproceso :</b>  <?= round(($suma / $tallas->cantidad)*100,2) ?>%</td>  <td colspan = "2"></td>    
                                         <?php }
                                        endforeach;    ?>
                                          
                                </tbody>   
                                <tr>
                                  <td style="font-size: 100%; width: 240px; text-align: right; background: #4B6C67; color: #FFFFFF;"><b>Total unidades  : </b> <?= $dato ?></td> <td colspan="8"></td>
                                </tr>
                                 <td style="font-size: 100%; width: 240px; text-align: right; background: #4B6C67; color: #FFFFFF;"><b>Total reproceso: </b> <?= $contador ?> %</td> <td colspan="8"></td>
                            </table>
                           
                              <div class="panel-footer text-right">
                                <?= Html::a('<span class="glyphicon glyphicon-export"></span> Excel', ['reprocesosexcelconfeccion', 'id' => $id], ['class' => 'btn btn-primary btn-sm']);?>
                            </div>
                        </div>    
                    </div>
                </div>    
            </div>
           <!-- TERMINA EL TABS-->
             <div role="tabpanel" class="tab-pane" id="reproceso_terminacion">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operacion</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Modulo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Op</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>Reproceso</th>
                                          <th scope="col" style='background-color:#B9D5CE;'>F_registro.</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                            
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0; $total = 0; $dato = 0;
                                    foreach ($detalle_orden as $tallas):
                                        $suma = 0;
                                       $total += $tallas->cantidad;
                                        foreach ($reproceso_terminacion as $val):
                                            if($tallas->idproductodetalle == $val->idproductodetalle){
                                               $suma += $val->cantidad;  
                                                ?>
                                                 <tr style="font-size: 85%; ">
                                                     <td><?= $val->operario->nombrecompleto ?></td>
                                                      <td><?= $val->productodetalle->prendatipo->prenda .' / '.$val->productodetalle->prendatipo->talla->talla .' - ('. $tallas->cantidad.')'?></td>
                                                     <td><?= $val->proceso->proceso ?></td>
                                                     <td><?= $val->id_balanceo ?></td>
                                                     <td><?= $val->idordenproduccion ?></td>
                                                     <td><?= $val->cantidad ?></td>
                                                     <td><?= round(($suma / $tallas->cantidad)*100,2)?></td>
                                                      <td><?= $val->fecha_registro ?></td>
                                                     <td><?= $val->observacion ?></td>
                                                    
                                                 </tr>
                                           <?php }
                                        endforeach;
                                        $contador = $suma; 
                                        $dato += $contador;
                                        $contador = round(($dato / $total)*100,2); 
                                        if($suma == 0){
                                        }else{?>
                                               <td colspan="6"><td style="font-size: 85%; width: 100px; text-align: right; background: #4B6C67; color: #FFFFFF;"><b>Unidades: </b> <?= ''.number_format($suma,0) ?></td> <td style="font-size: 85%; width: 130px; text-align: right; background: #4B6C67; color: #FFFFFF;"><b>Reproceso :</b>  <?= round(($suma / $tallas->cantidad)*100,2) ?>%</td>  <td colspan = "2"></td>    
                                        <?php }
                                    endforeach;    ?>
                                          
                                </tbody>   
                                <tr>
                                  <td style="font-size: 100%; width: 240px; text-align: right; background: #4B6C67; color: #FFFFFF;"><b>Total unidades  : </b> <?= $dato ?></td> <td colspan="8"></td>
                                </tr>
                                 <td style="font-size: 100%; width: 240px; text-align: right; background: #4B6C67; color: #FFFFFF;"><b>Total reproceso: </b> <?= $contador ?> %</td> <td colspan="8"></td>
                            </table>
                           
                              <div class="panel-footer text-right">
                                <?= Html::a('<span class="glyphicon glyphicon-export"></span> Excel', ['reprocesosexcelterminacion', 'id' => $id], ['class' => 'btn btn-primary btn-sm']);?>
                            </div>
                        </div>    
                    </div>
                </div>    
            </div>
           <!-- TERMINA TABS-->
            
        
    </div> 
</div>
<?php $form->end() ?> 

    


