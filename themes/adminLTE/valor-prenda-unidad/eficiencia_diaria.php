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
$this->title = 'Eficiencia diaria';
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
    "action" => Url::toRoute("valor-prenda-unidad/eficiencia_diaria"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
$bodegaPlanta= ArrayHelper::map(\app\models\PlantaEmpresa::find()->all(), 'id_planta', 'nombre_planta');
$operario= ArrayHelper::map(\app\models\Operarios::find()->orderBy('nombrecompleto asc')->all(), 'id_operario', 'nombrecompleto');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, 'id_operario')->widget(Select2::classname(), [
                'data' => $operario,
                'options' => ['prompt' => 'Seleccione el operario'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'id_planta')->widget(Select2::classname(), [
                'data' => $bodegaPlanta,
                'options' => ['prompt' => 'Seleccione la planta...'],
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
            
        </div>
         
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/eficiencia_diaria") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>
   
 <div>
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active" ><a href="#eficiencia" aria-controls="eficiencia" role="tab" data-toggle="tab">Eficiencia <span class="badge"></span></a></li>
    </ul>
    <div class="tab-content">
         <div role="tabpanel" class="tab-pane active" id="eficiencia">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                           <?php if($sw == 1){?>
                                <thead>
                                    <tr style ='font-size:85%;'>    
                                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Fecha operación</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Sabado</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Cumplimiento</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Total pagar</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Nota</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                                </thead>
                            <?php }else{?>
                                <thead>
                                    <tr style ='font-size:85%;'>    
                                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Cumplimiento</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Total pagar</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Nota</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                                </thead>
                            <?php }?>    
                            <body>
                                 <?php
                                    if($modelo){
                                        $auxiliar = ''; $sumarPorcentaje = 0; $cont = 0; $promedio = 0; $contarDia = 0; $totalPagar = 0;
                                        $empresa = Matriculaempresa::findOne(1);
                                        if($sw == 1){ //buscar la eficiencia por operario en un rago de fechas
                                            foreach ($modelo as $val):
                                                $totalPagar += $val->vlr_pago;
                                                if($val->hora_descontar == 1){
                                                   $contarDia += 1;  
                                                }
                                                $sumarPorcentaje += $val->porcentaje_cumplimiento;
                                                if($auxiliar <> $val->dia_pago){
                                                  $cont += 1;
                                                  $auxiliar = $val->dia_pago;
                                                }else{
                                                 $auxiliar = $val->dia_pago; 
                                                } 
                                            endforeach;
                                            $promedio = ''.number_format($sumarPorcentaje / $contarDia,2);
                                            foreach ($modelo as $validar):
                                                if($auxiliar <> $validar->id_operario){
                                                   $auxiliar = $validar->id_operario;?>
                                                    <tr style ='font-size:85%;'>
                                                        <td><?= $validar->operarioProduccion->documento?></td>
                                                        <td><?= $validar->operarioProduccion->nombrecompleto?></td>
                                                        <td><?= $validar->dia_pago?></td>
                                                        <td><?= $validar->aplicaSabado?></td>


                                                         <?php if($cont > 0){?>
                                                            <td style="background-color:#83c5be; text-align: right"><?= $promedio?>%</td>
                                                            <?php if($promedio >= $empresa->porcentaje_empresa){?>
                                                                 <td style="text-align: right"><?= ''. number_format($totalPagar,0)?></td>
                                                                <td style='background-color:#e9d8a6;'><?= 'GANA BONIFICACION'  ?>   <span class="glyphicon glyphicon-blackboard"></span></td>
                                                            <?php } else{
                                                                if($promedio >= $empresa->porcentaje_minima_eficiencia){ ?>
                                                                          <td style="text-align: right"><?= ''. number_format($totalPagar,0)?></td>
                                                                        <td style='background-color:#83c5be;'><?= 'LE CUMPLE A LA EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-up"></span></td>
                                                                <?php }else{ ?>
                                                                        <td style="text-align: right"><?= ''. number_format($totalPagar,0)?></td>
                                                                    <td style='background-color:#b5c99a;'><?= 'NO CUMPLE LA EFICIENCIA DE EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-down"></span></td>
                                                                <?php }      
                                                            }?>
                                                        <?php } else{ ?>
                                                              <td style="text-align: right"><?= ''. number_format($totalPagar,0)?></td>
                                                             <td><?= 'NO GANA BONIFICACION'?></td>
                                                        <?php }?>   
                                                        <td><?= $validar->planta->nombre_planta?></td>
                                                    </tr>   

                                                <?php
                                                }else{
                                                   $auxiliar = $validar->id_operario; 
                                                }
                                            endforeach;
                                            
                                        }else{  //consulta que permite mostrar el cumplimiento por planta
                                            if($sw == 2){
                                                $query = ValorPrendaUnidadDetalles::find()
                                                    ->joinWith('operarioProduccion')
                                                    ->where(['valor_prenda_unidad_detalles.id_planta' => $id_planta])
                                                    ->andWhere(['between', 'dia_pago', $dia_pago, $fecha_corte])
                                                    ->groupBy('id_operario')
                                                    ->orderBy('id_operario ASC');
                                                $resultados = $query->all();
                                                $con = 0; $totalPorcentaje = 0; $totalDiaCorte = 0; $totalPagado = 0;
                                                foreach ($resultados as $resultado):
                                                    $con += 1;
                                                    $buscarEficiencia = ValorPrendaUnidadDetalles::find ()->where (['=','id_operario', $resultado->id_operario])
                                                                                                           ->andWhere(['between','dia_pago', $dia_pago, $fecha_corte])->all ();
                                                    foreach ($buscarEficiencia as $buscar):
                                                        $sumarPorcentaje += $buscar->porcentaje_cumplimiento;
                                                        $totalPagar += $buscar->vlr_pago;
                                                        if($buscar->hora_descontar == 1){
                                                            $contarDia += 1;
                                                        }
                                                    endforeach;
                                                    //actualiza y descarga variables
                                                    $totalPorcentaje = $sumarPorcentaje;
                                                    $totalDiaCorte = $contarDia;
                                                    $totalPagado = $totalPagar;
                                                    if($totalDiaCorte){
                                                        $promedio = round(($totalPorcentaje / $totalDiaCorte),2);
                                                    }else{
                                                         $promedio = 0;
                                                    }  ?>  
                                                    <tr style ='font-size:85%;'>
                                                        <td><?= $resultado->operarioProduccion->documento?></td>
                                                        <td><?= $resultado->operarioProduccion->nombrecompleto?></td>
                                                        <td style="text-align: right"><?= $promedio ?> %</td>
                                                        <?php if($promedio > $empresa->porcentaje_empresa){?>
                                                            <td style="text-align: right"><?= '$'. number_format($totalPagado,0)?></td>
                                                            <td style='background-color:#e9d8a6;'><?= 'GANA BONIFICACION'  ?>   <span class="glyphicon glyphicon-blackboard"></span></td>
                                                        <?php }else{
                                                            if($promedio > $empresa->porcentaje_minima_eficiencia){?>
                                                                <td style="text-align: right"><?= '$'. number_format($totalPagado,0)?></td>
                                                                <td style='background-color:#83c5be;'><?= 'LE CUMPLE A LA EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-up"></span></td>
                                                            <?php }else{?>
                                                                <td style="text-align: right"><?= '$'. number_format($totalPagado,0)?></td>
                                                                <td style='background-color:#b5c99a;'><?= 'NO CUMPLE LA EFICIENCIA DE EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-down"></span></td>
                                                            <?php }
                                                        }?> 
                                                         <td><?= $resultado->planta->nombre_planta?></td>        
                                                    </tr>
                                                    <?php
                                                    $sumarPorcentaje = 0;
                                                    $promedio = 0;
                                                    $contarDia = 0; 
                                                    $totalPagar = 0;
                                                endforeach;
                                                
                                            }
                                        }    
                                    }?>  
                                           
                                </body>    
                        </table>
                     
                    </div>
                </div>    
            </div>    
        </div>
    </div>
 </div>

