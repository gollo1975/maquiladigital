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
$this->title = 'Resume de pago (APP)';
$this->params['breadcrumbs'][] = $this->title;
?>
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
    
    <div class="modal remote fade" id="modalpagarserviciosoperarios"  data-backdrop="static">
        <div class="modal-dialog modal-lg" style ="width: 700px;">
            <div class="modal-content"></div>
        </div>
    </div>
</div>    
    
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtro");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("valor-prenda-unidad/valor_prenda_app"),
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
                    
        </div>
          <div class="row checkbox checkbox-success" align ="center">
                <?= $formulario->field($form, 'validar_eficiencia')->checkbox(['label' => 'Buscar eficiencia', '1' =>'small', 'class'=>'bs_switch','style'=>'margin-bottom:10px;', 'id'=>'validar_eficiencia']) ?>
            </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/valor_prenda_app") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
    <?php if($validar_eficiencia == 1){?>
        <ul class="nav nav-tabs" role="tablist">
           <li role="presentation" class="active"><a href="#listado" aria-controls="listado" role="tab" data-toggle="tab">Listado <span class="badge"><?= $pagination->totalCount ?></span></a></li>
           <li role="presentation" ><a href="#eficiencia" aria-controls="eficiencia" role="tab" data-toggle="tab">Eficiencia <span class="badge"></span></a></li>
        </ul>
    <?php }else{ ?>
        <ul class="nav nav-tabs" role="tablist">
            <?php if($modelo){?>
                <li role="presentation" class="active"><a href="#listado" aria-controls="listado" role="tab" data-toggle="tab">Listado <span class="badge"><?= $pagination->totalCount ?></span></a></li>
            <?php }?>
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
                                <th scope="col" style='background-color:#B9D5CE;'>OP</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Ref.</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Operación</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                                <th scope="col" style='background-color:#B9D5CE;'>F. confe.</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Cant.</th>
                                <th scope="col" style='background-color:#B9D5CE;'><span title="Porcentaje de cumplimiento">%</span></th>
                                <th scope="col" style='background-color:#B9D5CE;'><span title="Bodega o planta" >Planta</span></th>
                                <th scope="col" style='background-color:#B9D5CE;'>H. inicio</th>
                                <th scope="col" style='background-color:#B9D5CE;'>H. corte</th>
                                <th scope="col" style='background-color:#B9D5CE;'><span title="Tiempo de la operación">Sam</span></th>
                                <th scope="col" style='background-color:#B9D5CE;'><span title="Tiempo real de la operación">Sam_real</span></th>
                                <th scope="col" style='background-color:#B9D5CE;'><span title="Diferencia de confeccion en minutos">Dif. M.</span></th>
                                <th scope="col" style='background-color:#B9D5CE;'><span title="Diferencia de confeccion en segundos">Dif. S.</span></th>
                            </thead>
                            <body>
                                <?php 
                                if($modelo){
                                    $current_operario_id = null;
                                    foreach ($modelo as $val):
                                        // Verificamos si el operario actual es diferente al del registro anterior
                                        if ($val->id_operario !== $current_operario_id):
                                            // Si es diferente, mostramos una fila de encabezado para el nuevo operario.
                                            // Esto crea el efecto de "agrupación".
                                    ?>
                                        <tr style='font-size:95%; font-weight:bold; background-color:#F5F5F5;'>
                                            <td colspan="15">
                                                <?= $val->operarioProduccion->nombrecompleto ?>
                                            </td>
                                        </tr>
                                    <?php
                                            // Actualizamos la variable con el ID del nuevo operario
                                            $current_operario_id = $val->id_operario;
                                        endif;
                                    ?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $val->idordenproduccion ?></td>
                                            <td><?= $val->ordenproduccion->codigoproducto ?></td>
                                            <td><?= $val->operaciones->idproceso ?? 'NO FOUND' ?></td>
                                            <td><?= $val->operaciones->proceso ?? 'NO FOUND' ?></td>
                                            <td><?= $val->detalleOrdenProduccion->productodetalle->prendatipo->talla->talla ?? 'NO FOUND' ?></td>
                                            <td><?= $val->dia_pago ?></td>
                                            <td align="right"><?= number_format($val->cantidad, 0) ?></td>
                                            <td><?= $val->porcentaje_cumplimiento ?> %</td>
                                            <td><?= $val->planta->nombre_planta ?></td>
                                            <td><?= $val->hora_inicio ?></td>
                                            <?php
                                            // Usamos una variable para almacenar la clase CSS
                                            if (!empty($val->hora_inicio_desayuno)) {?>
                                               
                                                <td style="background-color: #c9e2b3; color: #385d2a;"><?= $val->hora_inicio_desayuno ?></td>
                                               
                                            <?php } elseif (!empty($val->hora_inicio_almuerzo)) {?>
                                                 <td style="background-color: #ffe6b3; color: #664d03;"><?= $val->hora_inicio_almuerzo ?></td>
                                                 
                                            <?php } else {?>
                                                 <td style="background-color: #e9ecef; color: #495057;"><?= $val->hora_corte ?></td>
                                           <?php } ?>
                                            <td><?= $val->minuto_prenda ?></td>
                                            <td><?= $val->tiempo_real_confeccion ?></td>
                                            <?php
                                            if($val->diferencia_tiempo > 0){?>
                                                <td style="background-color: #ffe6b3; color: green;"><?= $val->diferencia_tiempo ?></td>
                                            <?php }else{?>
                                                <td style="background-color: #ffe6b3; color: red;"><?= $val->diferencia_tiempo ?></td>
                                            <?php }
                                            if($val->diferencia_tiempo > 0){?>
                                                <td style="background-color: #c9e2b3 ; color: green;"><?= round($val->diferencia_tiempo * 60) ?></td>
                                            <?php }else{?>
                                                <td style="background-color: #c9e2b3; color: red;"><?= round($val->diferencia_tiempo * 60) ?></td>
                                            <?php }?>     
                                        </tr>
                                    <?php endforeach;
                                }?>
                                            
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
                        <?php
                        if($modelo){// SI HAY REGISTROS
                            $empresa = Matriculaempresa::findOne(1);
                            if($sw == 1) { ?>
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr style ='font-size:85%;'>    
                                        <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha operación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cumplimiento</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Nota</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Total pagar</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Total venta</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Margen</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                                    </thead>
                                    <tbody>
                                        <?php

                                            
                                                // Variables para el cálculo del promedio total
                                            $total_eficiencia_acumulada = 0;
                                            $contador_dias = 0;
                                            $total_pagar_operario = 0;
                                            $total_venta_planta = 0;
                                            $total_margen = 0;
                                             //CONSULTA PARA BUSCAR LAS OPERACIONES
                                             $parametros = app\models\Parametros::findOne(1);
                                            $query = new Query();
                                            $resultados = $query->select([
                                                't1.id_operario',
                                                't2.documento',
                                                't2.nombrecompleto',
                                                't1.dia_pago',
                                                't2.vinculado',
                                                't3.nombre_planta',
                                                'SUM(t1.porcentaje_cumplimiento) AS total_cumplimiento',
                                                'SUM(t1.vlr_pago) AS total_generado',
                                                'SUM(t1.total_valor_venta) AS total_venta',
                                                'COUNT(*) AS total_operaciones'
                                            ])
                                            ->from(['t1' => 'valor_prenda_unidad_detalles'])
                                            ->leftJoin(['t2' => 'operarios'], 't1.id_operario = t2.id_operario')
                                            ->leftJoin(['t3' => 'planta_empresa'], 't1.id_planta = t3.id_planta')
                                            ->where(['between', 't1.dia_pago', $dia_pago, $fecha_corte])
                                            ->andWhere(['t1.id_operario' => $id_operario])
                                            ->andWhere(['t1.tipo_aplicacion' => 1])        
                                            ->groupBy(['t1.id_operario', 't1.dia_pago', 't2.nombrecompleto', 't2.documento', 't3.nombre_planta'])
                                            ->orderBy(['t1.dia_pago' => SORT_DESC, 't1.id_operario' => SORT_ASC])
                                            ->all();

                                            // El bucle para mostrar la tabla y acumular los totales
                                            if (!empty($resultados)){ ?>

                                                <?php foreach ($resultados as $row):
                                                    $acumulado_venta = 0;
                                                    $eficiencia = ($row['total_operaciones'] > 0) ? ($row['total_cumplimiento'] / $row['total_operaciones']) : 0;
                                                    $eficiencia_formateada = number_format($eficiencia, 2);
                                                    
                                                    //calcular el total de venta por dia
                                                    $total_valor_venta= $row['total_venta'];
                                                    if($total_valor_venta > 0){
                                                        $acumulado_venta += $total_valor_venta;
                                                        $empleado = $row['vinculado'];
                                                        if($empleado == 0){
                                                            $utilidad_bruta = $total_valor_venta - $row['total_generado'];
                                                            $margen = round(($utilidad_bruta  / $total_valor_venta)* 100,2);
                                                        }else{
                                                            $utilidad_bruta = $total_valor_venta - $parametros->valor_dia_empleado;
                                                            $margen = round(($utilidad_bruta  / $total_valor_venta)* 100,2);
                                                        }
                                                       $margen = round(($utilidad_bruta / $total_valor_venta) * 100,2);
                                                    }else{
                                                       $margen = 0; 
                                                    }
                                                    
                                                    
                                                    // Acumular los totales para el cálculo global
                                                    $total_eficiencia_acumulada += $eficiencia;
                                                    $total_margen += $margen;
                                                    $contador_dias++; // Incrementar el contador de días
                                                    $total_pagar_operario += $row['total_generado'];
                                                    $total_venta_planta += $acumulado_venta;
                                                ?>
                                                    <tr style='font-size:85%;'>
                                                        <td><?= Html::encode($row['documento']) ?></td>
                                                        <td><?= Html::encode($row['nombrecompleto']) ?></td>
                                                        <td><?= Html::encode($row['dia_pago']) ?></td>
                                                        <?php if($eficiencia_formateada > $empresa->porcentaje_empresa){?>
                                                            <td style="text-align: right; color: blue"><?= $eficiencia_formateada ?>% </td>
                                                            <td style='background-color:#e9d8a6;'><?= 'GANA BONIFICACION' ?> <span class="glyphicon glyphicon-blackboard"></span></td>
                                                        <?php } else {
                                                            if($eficiencia_formateada > $empresa->porcentaje_minima_eficiencia){?>
                                                                <td style="text-align: right; color: green"><?= $eficiencia_formateada ?>% </td>
                                                                <td style='background-color:#83c5be;'><?= 'LE CUMPLE A LA EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-up"></span></td>
                                                            <?php } else {?>
                                                                <td style="text-align: right; color: red"><?= $eficiencia_formateada ?>% </td>
                                                                <td style='background-color:#b5c99a;'><?= 'NO CUMPLE LA EFICIENCIA DE EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-down"></span></td>
                                                            <?php }
                                                        }?>
                                                        <td style="text-align: right"><?= Html::encode(number_format($row['total_generado'], 0)) ?></td>
                                                        <td style="text-align: right"><?= Html::encode(number_format($total_valor_venta,0)) ?></td>
                                                        <td style="text-align: right"><?= Html::encode(number_format($margen,2)) ?>%</td>
                                                        <td style="text-align: right"><?= Html::encode($row['nombre_planta']) ?></td>
                                                    </tr>
                                                <?php endforeach;
                                            }else{ ?>
                                                <div class="alert alert-info" role="alert">
                                                    No se encontraron datos de eficiencia para el operario en el rango de fechas.
                                                </div>
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
                            <?php }else{?>  <!--TERMINA SI EL SW == 1,-->
                                <table class="table table-bordered table-hover">
                                    <thead>
                                         <tr style ='font-size:85%;'>    
                                         <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Fecha corte</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Cumplimiento</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Nota</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Total pagar</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Total venta</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Margen</th>
                                     </thead>
                                    <tbody>
                                        <?php
                                        //valor dia
                                        $parametros = app\models\Parametros::findOne(1);
                                       $query = new Query();
                                        $resultados = $query->select([
                                            't1.id_operario',
                                            't2.documento',
                                            't2.nombrecompleto',
                                            't3.nombre_planta',
                                            't2.vinculado',
                                            'SUM(t1.porcentaje_cumplimiento) AS total_porcentaje_cumplimiento',
                                            'SUM(t1.vlr_pago) AS total_generado',
                                            'SUM(t1.total_valor_venta) AS total_venta',
                                            'COUNT(*) AS total_operaciones',
                                        ])
                                        ->from(['t1' => 'valor_prenda_unidad_detalles'])
                                        ->leftJoin(['t2' => 'operarios'], 't1.id_operario = t2.id_operario')
                                        ->leftJoin(['t3' => 'planta_empresa'], 't1.id_planta = t3.id_planta')
                                        ->where(['between', 't1.dia_pago', $dia_pago, $fecha_corte])
                                        ->andWhere(['t1.id_planta' => $id_planta])
                                        ->andWhere(['t1.tipo_aplicacion' => 1])        
                                        ->groupBy(['t1.id_operario', 't2.nombrecompleto', 't2.documento', 't3.nombre_planta'])
                                        ->orderBy(['t1.id_operario' => SORT_ASC])
                                        ->all();
                                        
                                        $total_porcentaje_global = 0;
                                        $total_operaciones_global = 0;
                                        $total_venta_planta = 0;
                                        $total_pagar_operario = 0;
                                        $total_margen = 0;
                                            //termina la consulta
                                            if (!empty($resultados)){  //pregunta si hay datos                                 
                                                foreach ($resultados as $row):
                                                   $acumulado_venta = 0;
                                                   $promedio_operario = ($row['total_operaciones'] > 0) ? ($row['total_porcentaje_cumplimiento'] / $row['total_operaciones']) : 0;
                                                   $promedio_formateado = number_format($promedio_operario, 2);

                                                    //calcular el total de venta por dia
                                                    $total_valor_venta= $row['total_venta'];
                                                    if($total_valor_venta > 0){
                                                        $acumulado_venta += $total_valor_venta;
                                                        $empleado = $row['vinculado'];
                                                        if($empleado == 0){
                                                            $utilidad_bruta = $total_valor_venta - $row['total_generado'];
                                                            $margen = round(($utilidad_bruta  / $total_valor_venta)* 100,2);
                                                        }else{
                                                            $utilidad_bruta = $total_valor_venta - $parametros->valor_dia_empleado;
                                                            $margen = round(($utilidad_bruta  / $total_valor_venta)* 100,2);
                                                        }
                                                        
                                                    }else{
                                                        $margen = 0;
                                                        $acumulado_venta = 0;
                                                    }    

                                                    // ACUMULAMOS LOS VALORES PARA EL PROMEDIO GLOBAL
                                                    $total_porcentaje_global += $promedio_formateado;
                                                    $total_operaciones_global += 1;
                                                    ?>
                                                    <tr style='font-size:85%;'>
                                                        <td><?= Html::encode($row['documento']) ?></td>
                                                        <td><?= Html::encode($row['nombrecompleto']) ?></td>
                                                        <td><?= Html::encode($dia_pago) ?></td>
                                                        <td><?= Html::encode($fecha_corte) ?></td>
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
                                                $total_margen += $margen;
                                                $total_venta_planta += $acumulado_venta;
                                                $total_pagar_operario += $row['total_generado'];
                                                endforeach;
                                            }else{?>
                                                  
                                                <tr>
                                                    <td colspan="7">
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
                                }
                           } ?>   
                    </div>
                </div>    
            </div>    
        </div>
        <!-- TERMINA TABS-->
       
    </div>
 </div>
<?php $form->end() ?>
<?php if($modelo){?>
    <?= LinkPager::widget(['pagination' => $pagination]) ?>
<?php }?>
