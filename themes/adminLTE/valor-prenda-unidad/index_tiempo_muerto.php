<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\db\Query;
use yii\db\Command;
use yii\db\ActiveQuery;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
use app\models\ValorPrendaUnidadDetalles;
use app\models\Matriculaempresa;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Tiempos muertos';
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
    "action" => Url::toRoute("valor-prenda-unidad/index-sam-muerto"),
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
	
    <div class="panel-body" id="filtro" style="display:block">
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
                    'todayHighlight' => true,
                    'orientation' => 'bottom']])
            ?>
            <?= $formulario->field($form, 'fecha_corte')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']])
            ?>
             <?= $formulario->field($form, 'inicio_hora_corte')->input ('time'); ?>
            <?= $formulario->field($form, 'final_hora_corte')->input ('time'); ?>
                    
        </div>
         
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/index-sam-muerto") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<?php
    $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>

<div class="table-responsive">
        <ul class="nav nav-tabs" role="tablist">
           <li role="presentation" class="active"><a href="#eficiencia" aria-controls="eficiencia" role="tab" data-toggle="tab">Listado de eficiencia <span class="badge"></span></a></li>
        </ul>
    <div class="tab-content">
      
        <!-- FIN TABS-->
         <div role="tabpanel" class="tab-pane active" id="eficiencia">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <?php
                             $empresa = Matriculaempresa::findOne(1);
                            
                            if($sw == 1) { ?> <!--<!-- SI SELECCIONA EL OPERARIO -->
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr style ='font-size:85%;'>    
                                        <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha operación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cumplimiento</th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Tiempo sobrante">Sam P.</span></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><span title="Tiempo negativo">Sam N.</span></th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Nota</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Total pagar</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Total venta</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Margen</th>

                                    </thead>
                                    <tbody>
                                        <?php

                                            
                                                // Variables para el cálculo del promedio total
                                            $total_eficiencia_acumulada = 0;
                                            $contador_dias = 1;
                                            $total_pagar_operario = 0;
                                            $total_venta_planta = 0;
                                            $total_margen = 0;
                                            $totalSamPositivo = 0;
                                            $totalSamNegativo = 0;
                                            $parametros = app\models\Parametros::findOne(1);
                                            
                                            // El bucle para mostrar la tabla y acumular los totales
                                            if (!empty($model)){ ?>

                                                <?php foreach ($model as $row):
                                                    $acumulado_venta = 0;
                                                     // valida los doas
                                                    $sam_pos = isset($row['sam_positivo']) ? (float)$row['sam_positivo'] : 0;
                                                    $sam_neg = isset($row['sam_negativo']) ? (float)$row['sam_negativo'] : 0;
                                                
                                                
                                                    $eficiencia = ($row['total_operaciones'] > 0) ? ($row['total_cumplimiento'] / $row['total_operaciones']) : 0;
                                                    $eficiencia_formateada = number_format($eficiencia, 2);
                                                    
                                                    //calcular el total de venta por dia
                                                    $total_valor_venta= $row['total_venta'];
                                                    $acumulado_venta = $total_valor_venta;
                                                    if($total_valor_venta > 0){
                                                        $empleado = $row['vinculado'];
                                                        $costo_operario = ($empleado == 1) ? ($parametros->valor_dia_empleado * $row['dias_laborados']) : $row['total_generado'];
                                                        $utilidad_bruta = $total_valor_venta - $costo_operario;
                                                        $margen = round(($utilidad_bruta / $total_valor_venta) * 100, 3);
                                                        
                                                    }else{
                                                       $margen = 0; 
                                                    }
                                                    
                                                    
                                                    // Acumular los totales para el cálculo global
                                                    $total_eficiencia_acumulada += $eficiencia;
                                                    $total_margen += $margen;
                                                    $contador_dias++; // Incrementar el contador de días
                                                    $total_pagar_operario += $row['total_generado'];
                                                    $total_venta_planta += $total_valor_venta;
                                                    $totalSamPositivo += $sam_pos;
                                                    $totalSamNegativo += $sam_neg;
                                                ?>
                                                    <tr style='font-size:85%;'>
                                                        <td><?= Html::a(Html::encode($row['documento']), ['view_listado_operacion', 'id_operario' => $row['id_operario'], 'dia_pago' => $row['dia_pago'], 'fecha_corte' => $fecha_corte], [
                                                                'target' => '_blank', 
                                                                'data-pjax' => '0', // Importante si usas Pjax
                                                            ]) ?>
                                                        </td>
                                                        <td><?= Html::encode($row['nombrecompleto']) ?></td>
                                                        <td><?= Html::encode($row['dia_pago']) ?></td>
                                                        <?php if($eficiencia_formateada > $empresa->porcentaje_empresa){?>
                                                            <td style="text-align: right; color: blue"><?= $eficiencia_formateada ?>% </td>
                                                            <td style="text-align: right"><?= number_format($sam_pos, 2) ?></td>
                                                            <td style="text-align: right; color: red"><?= number_format($sam_neg, 2)?></td>
                                                            <td style='background-color:#e9d8a6;'><?= 'GANA BONIFICACION' ?> <span class="glyphicon glyphicon-blackboard"></span></td>
                                                        <?php } else {
                                                            if($eficiencia_formateada > $empresa->porcentaje_minima_eficiencia){?>
                                                                <td style="text-align: right; color: green"><?= $eficiencia_formateada ?>% </td>
                                                                <td style="text-align: right"><?= number_format($sam_pos, 2) ?></td>
                                                                <td style="text-align: right; color: red"><?= number_format($sam_neg, 2) ?></td>
                                                                <td style='background-color:#83c5be;'><?= 'LE CUMPLE A LA EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-up"></span></td>
                                                            <?php } else {?>
                                                                <td style="text-align: right; color: red"><?= $eficiencia_formateada ?>% </td>
                                                                <td style="text-align: right"><?= number_format($sam_pos, 2) ?></td>
                                                                <td style="text-align: right; color: red"><?= number_format($sam_neg, 2) ?></td>
                                                                <td style='background-color:#b5c99a;'><?= 'NO CUMPLE LA EFICIENCIA DE EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-down"></span></td>
                                                            <?php }
                                                        }?>
                                                        
                                                        <td style="text-align: right"><?= Html::encode(number_format($row['total_generado'], 0)) ?></td>
                                                        <td style="text-align: right"><?= Html::encode(number_format($total_valor_venta,0)) ?></td>
                                                        <td style="text-align: right; color: <?= ($margen > 0 ? 'black' : 'red') ?>">
                                                            <?= number_format($margen, 2) ?>%
                                                        </td>
                                                      
                                                    </tr>
                                                <?php endforeach;
                                            }else{ ?>
                                               <tr>
                                                    <td colspan="10">
                                                        <div class="alert alert-info" role="alert" style="margin: 20px 0;">
                                                            No se encontraron datos de eficiencia para el proceso seleccionado.
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                        </tbody>
                                </table>    
                                <table class="table table-bordered table-hover" style="margin-left: auto; margin-right: auto;">
                                    
                                        <?php 
                                        $promedio_total_eficiencia = 0;
                                        if ($contador_dias  > 0) {
                                            $promedio_total_eficiencia  = round($total_eficiencia_acumulada / $contador_dias);
                                            $promedio_total_planta = $total_margen / $contador_dias;
                                        }
                                        ?>

                                        <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                                               <b>Sam postivo: <?= ''.number_format($totalSamPositivo, 0) ?> Minutos</b> 
                                        </td>
                                        <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                                               <b>Sam negativo: <?= ''.number_format($totalSamNegativo, 0) ?> Minutos</b> 
                                        </td>
                                        <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                                               <b>Eficiencia total: <?= ''.number_format($promedio_total_eficiencia, 0) ?> %</b> 
                                        </td>
                                        <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                                               <b>Total pagar operario: <?= '$ '.number_format($total_pagar_operario, 2) ?></b> 
                                        </td>
                                        <td colspan="4" style="font-size: 90%; background: #277da1; color: #FFFFFF; text-align: center;">
                                                <b>Total ventas: <?= '$ '.number_format($total_venta_planta, 2) ?></b> 
                                        </td>
                                        <td colspan="4" style="font-size: 90%; background: #277da1; color: #FFFFFF; text-align: center;">
                                                <b>Total margen: <?= ''.number_format($promedio_total_planta, 2) ?> %</b> 
                                        </td>
                                </table>
                            <?php }else{?>  <!--SI SELECCIONA SOLO LA PLANTA,-->
                                <table class="table table-bordered table-hover">
                                    <thead>
                                         <tr style ='font-size:85%;'>    
                                         <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Fecha corte</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Dias</th>
                                         <th scope="col" style='background-color:#B9D5CE;'><span title="Tiempo sobrante">Sam P</span></th>
                                         <th scope="col" style='background-color:#B9D5CE;'><span title="Tiempo perdido negativo">Sam N</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Eficiencia</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Nota</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Total pagar</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Total venta</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Margen</th>
                                     </thead>
                                    <tbody>
                                        <?php
                                        //valor dia
                                        $parametros = app\models\Parametros::findOne(1);
                                       
                                        
                                        $total_porcentaje_global = 0;
                                        $total_operaciones_global = 0;
                                        $total_venta_planta = 0;
                                        $total_pagar_operario = 0;
                                        $total_margen = 0;
                                        $gran_total_pos = 0; 
                                        $gran_total_neg = 0;                                       
                                        $totalSanNegativo =0;
                                        $totalSanPositivo = 0;
                                        $contador = 0;
                                            //termina la consulta
                                            if (!empty($model)){  //pregunta si hay datos                                 
                                                foreach ($model as $row):
                                                    $acumulado_venta = 0;
                                                    
                                                    $promedio_operario = ($row['total_operaciones'] > 0) ? ($row['total_porcentaje_cumplimiento'] / $row['total_operaciones']) : 0;
                                                    $promedio_formateado = number_format($promedio_operario, 2);
                                                   
                                                   //rescatamos los Sam del select
                                                    $sam_pos_operario = $row['total_sam_positivo'] ?? 0;
                                                    $sam_neg_operario = $row['total_sam_negativo'] ?? 0;
                                                   
                                                   //calcular el total de venta por dia
                                                   $total_valor_venta = $row['total_venta'] ?? 0;
                                                    if($total_valor_venta > 0){
                                                        $acumulado_venta += $total_valor_venta;
                                                        
                                                        $empleado = $row['vinculado'];
                                                        $costo_operario = ($empleado == 1) ? ($parametros->valor_dia_empleado * $row['dias_laborados']) : $row['total_generado'];
                                                        $utilidad_bruta = $total_valor_venta - $costo_operario;
                                                        $margen = round(($utilidad_bruta / $total_valor_venta) * 100, 2);
                                                    }else{
                                                        $margen = 0;
                                                      //  $acumulado_venta = 0;
                                                    }    

                                                    // ACUMULAMOS LOS VALORES PARA EL PROMEDIO GLOBAL
                                                    $total_porcentaje_global += $promedio_operario;
                                                    $total_operaciones_global += 1;
                                                    $gran_total_pos += $sam_pos_operario;
                                                    $gran_total_neg += $sam_neg_operario;
                                                    $total_margen += $margen;
                                                    $total_venta_planta += $acumulado_venta;
                                                    $total_pagar_operario += $row['total_generado'];
                                                    ?>
                                                    <tr style='font-size:85%;'>
                                                        <td><?= Html::a(Html::encode($row['documento']), ['view_listado_operacion', 'id_operario' => $row['id_operario'], 'dia_pago' => $dia_pago, 'fecha_corte' => $fecha_corte], [
                                                                'target' => '_blank', 
                                                                'data-pjax' => '0', // Importante si usas Pjax
                                                            ]) ?>
                                                        </td>
                                                        <td><?= Html::encode($row['nombrecompleto']) ?></td>
                                                        <td><?= Html::encode($dia_pago) ?></td>
                                                        <td><?= Html::encode($fecha_corte) ?></td>
                                                        <td style="text-align: center"><?= Html::encode($row['dias_laborados']) ?></td>
                                                        <td style="text-align: right"><?= number_format($gran_total_pos, 2) ?></td>
                                                        <td style="text-align: right; color: red"><?= number_format($gran_total_neg, 2) ?></td>
                                                        <?php
                                                        if($promedio_formateado > $empresa->porcentaje_empresa){?>
                                                        <td style="text-align: right; color: blue"><?= $promedio_formateado ?>% </td>
                                                        <td style='background-color:#e9d8a6;'><?= 'GANA BONIFICACION' ?> <span class="glyphicon glyphicon-blackboard"></span></td>
                                                        <?php } else {
                                                            if($promedio_formateado > $empresa->porcentaje_minima_eficiencia){?>
                                                                <td style="text-align: right; color: green"><?= $promedio_formateado ?>% </td>
                                                                <td style='background-color:#83c5be;'><?= 'LE CUMPLE A LA EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-up"></span></td>
                                                            <?php } else {?>
                                                                <td style="text-align: right; color: red"><?= $promedio_formateado ?>% </td>
                                                                <td style='background-color:#b5c99a;'><?= 'NO CUMPLE LA EFICIENCIA DE EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-down"></span></td>
                                                            <?php }
                                                        }?>
                                                        <td style="text-align: right"><?= Html::encode(number_format($row['total_generado'], 0)) ?></td>
                                                        <td style="text-align: right"><?= Html::encode(number_format($total_valor_venta,0)) ?></td>
                                                        <?php if($margen < 0){?>
                                                            <td style="text-align: right; color: red"><?= Html::encode(number_format($margen,2)) ?>%</td>
                                                        <?php }else{?>
                                                             <td style="text-align: right"><?= Html::encode(number_format($margen,2)) ?>%</td>
                                                        <?php }?>    
                                                    </tr>
                                                <?php
                                                $totalSanNegativo += $gran_total_neg;
                                                $totalSanPositivo += $gran_total_pos;
                                                $total_margen += $margen;
                                                $total_venta_planta += $acumulado_venta;
                                                $total_pagar_operario += $row['total_generado'];
                                                $gran_total_pos = 0;
                                                $gran_total_neg = 0;
                                                $contador++;
                                                endforeach;
                                            }else{?>
                                                  
                                                <tr>
                                                    <td colspan="12">
                                                        <div class="alert alert-info" role="alert" style="margin: 20px 0;">
                                                            No se encontraron datos de eficiencia para el operario en el rango de fechas.
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>  
                                    <table class="table table-bordered table-hover" style="margin-left: auto; margin-right: auto;">
                                        <tr>
                                         <?php
                                            $promedio_total_planta = 0; $promedio_total_margen = 0;
                                            if ($total_operaciones_global  > 0) {
                                                 $promedio_total_planta = ($total_porcentaje_global / $total_operaciones_global);
                                                 $promedio_total_margen = $total_margen / $total_operaciones_global;
                                             } ?>
                                            <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                                                <b>Empleados: <?= number_format($contador) ?></b> 
                                            </td>
                                            <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                                                <b>Total San Positivo: <?= number_format($totalSanPositivo, 2) ?> Minutos</b> 
                                            </td>
                                            <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                                                <b>Total San negativo: <?= number_format($totalSanNegativo, 2) ?> Minutos</b> 
                                            </td>
                                            <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                                                <b>Eficiencia total de la planta: <?= number_format($promedio_total_planta, 0) ?>%</b> 
                                            </td>
                                            <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                                                <b>Total pagar operarios: <?= '$    '.number_format($total_pagar_operario, 0) ?></b> 
                                            </td>
                                            <td colspan="4" style="font-size: 90%; background: #277da1; color: #FFFFFF; text-align: center;">
                                                <b>Total ventas: <?= '$'.number_format($total_venta_planta, 0) ?></b> 
                                            </td>
                                            <td colspan="4" style="font-size: 90%; background: #277da1; color: #FFFFFF; text-align: center;">
                                                <b>Total margen: <?= ''.number_format($promedio_total_margen, 2) ?> %</b> 
                                            </td>
                                          
                                        </tr>    
                                    </table> 
                                                            
                                <?php
                                }?>
                    </div>
                </div>    
            </div>    
        </div>
        <!-- TERMINA TABS-->
       
    </div>
 </div>
<?php $form->end() ?>
<?php if($model){?>
    <?= LinkPager::widget(['pagination' => $pagination]) ?>
<?php }?>
