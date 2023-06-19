<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
use app\models\ValorPrendaUnidadDetalles;
use app\models\Matriculaempresa;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Resume de pago';
$this->params['breadcrumbs'][] = $this->title;
?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtro");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("valor-prenda-unidad/indexsoporte"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
$servicio= ArrayHelper::map(\app\models\Ordenproducciontipo::find()->all(), 'idtipo', 'tipo');
$bodegaPlanta= ArrayHelper::map(\app\models\PlantaEmpresa::find()->all(), 'id_planta', 'nombre_planta');
$operario= ArrayHelper::map(\app\models\Operarios::find()->orderBy('nombrecompleto asc')->all(), 'id_operario', 'nombrecompleto');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "idordenproduccion")->input("search") ?>
             <?= $formulario->field($form, 'id_operario')->widget(Select2::classname(), [
                'data' => $operario,
                'options' => ['prompt' => 'Seleccione el operario'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
               <?= $formulario->field($form, 'dia_pago')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
               <?= $formulario->field($form, 'fecha_corte')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
             <?= $formulario->field($form, 'operacion')->dropDownList(['' => 'TODOS', '1' => 'CONFECCION', '2' => 'OPERACION', '3' => 'AJUSTE'],['prompt' => 'Seleccione el estado ...']) ?>
            <?= $formulario->field($form, 'id_planta')->widget(Select2::classname(), [
                'data' => $bodegaPlanta,
                'options' => ['prompt' => 'Seleccione la planta...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
              <?= $formulario->field($form, 'tipo_servicio')->widget(Select2::classname(), [
                'data' => $servicio,
                'options' => ['prompt' => 'Seleccione el servicio...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
          
        </div>
          <div class="row checkbox checkbox-success" align ="center">
                <?= $formulario->field($form, 'validar_eficiencia')->checkbox(['label' => 'Buscar eficiencia', '1' =>'small', 'class'=>'bs_switch','style'=>'margin-bottom:10px;', 'id'=>'validar_eficiencia']) ?>
            </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/indexsoporte") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>
<?php
    $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<div class="panel-footer text-right">
    <div class="panel-footer text-right">
      <!-- Inicio Nuevo Detalle proceso -->
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear pago',
            ['/valor-prenda-unidad/pagarserviciosoperarios'],
            [
                'title' => 'Crear servicios',
                'data-toggle'=>'modal',
                'data-target'=>'#modalpagarserviciosoperarios',
                'class' => 'btn btn-info btn-xs'
            ])    
       ?>
    </div> 
    <div class="modal remote fade" id="modalpagarserviciosoperarios">
        <div class="modal-dialog modal-lg" style ="width: 700px;">
            <div class="modal-content"></div>
        </div>
    </div>
</div>    
    
<div class="table-responsive">
    <?php if($validar_eficiencia == 1){?>
        <ul class="nav nav-tabs" role="tablist">
           <li role="presentation" class="active"><a href="#listado" aria-controls="listado" role="tab" data-toggle="tab">Listado <span class="badge"><?= $pagination->totalCount ?></span></a></li>
           <li role="presentation" ><a href="#eficiencia" aria-controls="eficiencia" role="tab" data-toggle="tab">Eficiencia <span class="badge"></span></a></li>
            <?php if($bodega > 0 && $dia_pago <> '' && $fecha_corte <> '' && $tipo_servicio > 0){?>
               <li role="presentation" ><a href="#eficiencia_planta" aria-controls="eficiencia_planta" role="tab" data-toggle="tab">Eficiencia planta</a></li>
            <?php }?>   
        </ul>
    <?php }else{ ?>
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#listado" aria-controls="listado" role="tab" data-toggle="tab">Listado <span class="badge"><?= $pagination->totalCount ?></span></a></li>
        </ul>
    <?php }?>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="listado">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr style ='font-size:85%;'>                
                                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                <th scope="col" style='background-color:#B9D5CE;'>OP</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Operación</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Cant.</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Vr. Prenda</th>
                                <th scope="col" style='background-color:#B9D5CE;'>T. pagado</th>
                                <th scope="col" style='background-color:#B9D5CE;'><span title="Porcentaje de cumplimiento">% Cump.</span></th>
                                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                <th scope="col" style='background-color:#B9D5CE;'><span title="Bodega o planta" >Planta</span></th>
                                <th scope="col" style='background-color:#B9D5CE;'>Observacion</th>
                            </thead>
                            <body>
                                <?php 
                                foreach ($modelo as $val):?>
                                    <tr style='font-size:85%;'>  
                                        <td><?= $val->consecutivo ?></td>
                                        <td><?= $val->idordenproduccion ?></td>
                                        <?php if($val->id_operario == (NULL)){?>
                                            <td style='background-color:#AFE7CB;'><?= 'REGISTRO EN CONSTRUCCION'?> </td> 
                                        <?php }else{?>
                                            <td><?= $val->operarioProduccion->nombrecompleto?> </td>
                                        <?php }?>    
                                        <td><?= $val->operacionPrenda?></td>
                                         <td><?= $val->dia_pago ?></td>
                                        <td align="right"><?= ''.number_format($val->cantidad,0) ?></td>
                                        <td align="right"><?= ''.number_format($val->vlr_prenda,0) ?></td>
                                        <td align="right"><?= ''.number_format($val->vlr_pago,0) ?></td>
                                          <td><?= $val->porcentaje_cumplimiento ?></td>
                                        <td><?= $val->usuariosistema ?></td>
                                        <td><?= $val->planta->nombre_planta?></td>
                                        <td><?= $val->observacion?></td>
                                       
                                <?php endforeach; ?>
                            </body>    
                        </table>
                        <div class="panel-footer text-right" >            
                                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                        </div>
                    </div>
                </div>    
            </div>    
        </div>
        <!-- FIN TABS-->
         <div role="tabpanel" class="tab-pane" id="eficiencia">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr style ='font-size:85%;'>    
                                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Fecha operación</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Sabado</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Cumplimiento</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Nota</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                            </thead>
                            <body>
                                 <?php
                                    $cumplimiento = 0;
                                    $auxiliar = '';
                                    $contador = 0;
                                    $sumaSabado = 0;
                                    $conEficiencia = 0;
                                    $acumuladorEficiencia = 0 ; $totalEficiencia = 0;
                                    $empresa = Matriculaempresa::findOne(1);
                                    if($id_operario > 0){
                                         $modelo2 = ValorPrendaUnidadDetalles::find()->where(['>=','dia_pago', $dia_pago])
                                                  ->andWhere(['<=','dia_pago', $fecha_corte])
                                                  ->andWhere(['=','id_operario', $id_operario])->orderBy('dia_pago DESC')->all();
                                    }else{
                                        if($bodega > 0){
                                            $modelo2 = ValorPrendaUnidadDetalles::find()->where(['>=','dia_pago', $dia_pago])
                                                  ->andWhere(['<=','dia_pago', $fecha_corte])
                                                  ->andwhere(['=','id_planta', $bodega])->orderBy('id_operario DESC')->all();
                                        }else{
                                           $modelo2 = ValorPrendaUnidadDetalles::find()->where(['>=','dia_pago', $dia_pago])
                                                  ->andWhere(['<=','dia_pago', $fecha_corte])->orderBy('id_operario DESC')->all();
                                        }   
                                    } 
                                    if (count($modelo) > 0){
                                        foreach ($modelo2 as $eficiencia): 

                                                $cumplimiento = 0;
                                                $detalle = ValorPrendaUnidadDetalles::find()->where(['=','dia_pago', $eficiencia->dia_pago])
                                                                                         ->andWhere(['=','id_operario', $eficiencia->id_operario])->orderBy('dia_pago')->all();
                                                $con = count($detalle);
                                                if($con <= 1){
                                                    foreach ($detalle as $detalles):
                                                         $auxiliar = '';
                                                        ?>
                                                        <tr style="font-size: 85%;">
                                                            <td ><?= $detalles->operarioProduccion->documento ?></td>
                                                           <td ><?= $detalles->operarioProduccion->nombrecompleto ?></td>
                                                           <td ><?= $detalles->dia_pago?></td>
                                                           <td ><?= $detalles->aplicaSabado?></td>
                                                           <?php if($detalles->porcentaje_cumplimiento > $empresa->porcentaje_empresa){?>
                                                                <td style='background-color:#F9F4CB;' ><?= $detalles->porcentaje_cumplimiento ?>%</td>
                                                                <td><?= 'GANA BONIFICACION' ?></td>
                                                           <?php }else{?> 
                                                                <td style='background-color:#B6EFF5;' ><?= $detalles->porcentaje_cumplimiento ?>%</td>
                                                                <td><?= 'NO GANA BONIFICACION' ?></td>
                                                           <?php }?>     
                                                           <td ><?= $detalles->usuariosistema ?></td>
                                                        </tr>
                                                  <?php 
                                                   if($detalles->aplica_sabado == 1){
                                                      $sumaSabado += 1; 
                                                      $conEficiencia += $detalles->porcentaje_cumplimiento;
                                                   }
                                                    $contador += 1;
                                                    $acumuladorEficiencia += $detalles->porcentaje_cumplimiento;
                                                  endforeach; 
                                                }else{
                                                    foreach ($detalle as $contar):
                                                       $cumplimiento += $contar->porcentaje_cumplimiento;
                                                    endforeach;
                                                    if($id_operario > 0){
                                                        if($eficiencia->dia_pago != $auxiliar){
                                                           //codigo que descuenta porcentaje del dia sabado
                                                            if($contar->aplica_sabado == 1){
                                                               $sumaSabado += 1; 
                                                               $conEficiencia += $cumplimiento;
                                                            } 
                                                           $auxiliar = $eficiencia->dia_pago;
                                                            ?>
                                                            <tr style="font-size: 85%;">
                                                              <td ><?= $contar->operarioProduccion->documento ?></td>
                                                              <td ><?= $contar->operarioProduccion->nombrecompleto ?></td>
                                                              <td ><?= $contar->dia_pago?></td>
                                                               <td ><?= $contar->aplicaSabado?></td>
                                                              <?php if($cumplimiento > $empresa->porcentaje_empresa){?>
                                                                    <td style='background-color:#F9F4CB;' ><?= $cumplimiento ?>%</td>
                                                                    <td><?= 'GANA BONIFICACION' ?></td>
                                                               <?php }else{?> 
                                                                    <td style='background-color:#B6EFF5;' ><?= $cumplimiento ?>%</td>
                                                                    <td><?= 'NO GANA BONIFICACION' ?></td>
                                                               <?php }
                                                                 $contador += 1;
                                                                 $acumuladorEficiencia += $cumplimiento;
                                                               ?>     
                                                              <td ><?= $contar->usuariosistema ?></td>
                                                            </tr>
                                                        <?php }else{
                                                             $auxiliar = $eficiencia->dia_pago;
                                                        }  
                                                   }else{
                                                        if($eficiencia->id_operario != $auxiliar){
                                                           $auxiliar = $eficiencia->id_operario;
                                                            ?>
                                                            <tr style="font-size: 85%;">
                                                              <td ><?= $contar->operarioProduccion->documento ?></td>
                                                              <td ><?= $contar->operarioProduccion->nombrecompleto ?></td>
                                                              <td ><?= $contar->dia_pago?></td>
                                                              <td ><?= $contar->aplicaSabado?></td>
                                                              <?php if($cumplimiento > $empresa->porcentaje_empresa){?>
                                                                    <td style='background-color:#F9F4CB;' ><?= $cumplimiento ?>%</td>
                                                                    <td style='background-color:#F9F4CB;'><?= 'GANA BONIFICACION' ?></td>
                                                               <?php }else{?> 
                                                                    <td style='background-color:#B6EFF5;' ><?= $cumplimiento ?>%</td>
                                                                    <td><?= 'NO GANA BONIFICACION' ?></td>
                                                               <?php }?>     
                                                              <td ><?= $contar->usuariosistema ?></td>
                                                            </tr>
                                                        <?php }else{
                                                             $auxiliar = $eficiencia->id_operario;
                                                        }
                                                   }    
                                                }   
                                        endforeach;
                                        if($id_operario > 0 && $dia_pago <> '' && $fecha_corte <> '' && count($modelo2) > 0){
                                                   $totalEficiencia = (($acumuladorEficiencia - $conEficiencia)/($contador - $sumaSabado));
                                            ?>
                                            <tr>
                                                   <td colspan="3"></td>
                                                   <td align="right"><b>Eficiencia</b></td>
                                                   <td align="right" ><b><?= ''.number_format($totalEficiencia, 2); ?>%</b></td>
                                                   <td colspan="2"></td>
                                            </tr>
                                        <?php }
                                      
                                    } ?>  
                                           
                                </body>    
                        </table>
                         <?php $form->end() ?>
                    </div>
                </div>    
            </div>    
        </div>
        <!-- TERMINA TABS-->
        <?php if($bodega > 0 && $dia_pago <> '' && $fecha_corte <> '' && $tipo_servicio > 0){?>
            <div role="tabpanel" class="tab-pane" id="eficiencia_planta">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr style ='font-size:85%;'>    
                                    <td colspan="12" scope="col" style='background-color:#B9D5CE;'></td>
                               
                            </thead>
                            <body>
                                <?php
                                $Aux = '';
                                $suma = 0;
                                $nombre = 0;
                                $total = 0; $contador = 0; $granTotal = 0;
                                $listado = ValorPrendaUnidadDetalles::find()->where(['=','id_planta', $bodega])
                                                                             ->andWhere(['=', 'id_tipo', $tipo_servicio])
                                                                            ->andWhere(['=', 'dia_pago', $dia_pago])
                                                                            ->andWhere(['=','dia_pago', $fecha_corte])->orderBy('id_operario DESC')->all();
                                foreach ($listado as $listados):
                                    $conOperario = ValorPrendaUnidadDetalles::find()->where(['=','id_operario', $listados->id_operario])
                                                                                      ->andWhere(['=', 'id_tipo', $tipo_servicio])
                                                                                      ->andWhere(['=', 'dia_pago', $dia_pago])
                                                                                     ->andWhere(['=','dia_pago', $fecha_corte])->all();
                                    foreach ($conOperario as $consulta):
                                       $suma += $consulta->porcentaje_cumplimiento;
                                    endforeach; 
                                    if($nombre <> $listados->id_operario){
                                        $total += $suma;
                                        $suma = 0;
                                        $contador +=1;
                                        $nombre = $listados->id_operario;
                                    }else{
                                         $suma = 0;
                                    }
                                endforeach;
                                if($total == 0){
                                  $granTotal = 0;  
                                }else{
                                  $granTotal = ($total/$contador);
                                }  
                                ?>
                                <tr>
                                       <td colspan="2"></td>
                                       <td align="right"><b>Eficiencia planta</b></td>
                                       <td align="right" ><b><?= ''.number_format($granTotal, 2); ?>%</b></td>
                                       <td colspan="2"></td>
                                </tr>
                            </body>    
                        </table>
                    </div>
                </div>    
            </div>    
        </div>
        <?php } ?>
        <!-- TERMINA TABS-->
    </div>
 </div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

