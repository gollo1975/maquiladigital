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
                                <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Operación</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                                <th scope="col" style='background-color:#B9D5CE;'>F. pago</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Cant.</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Valor</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Total</th>
                                <th scope="col" style='background-color:#B9D5CE;'><span title="Porcentaje de cumplimiento">%</span></th>
                                <th scope="col" style='background-color:#B9D5CE;'><span title="Bodega o planta" >Planta</span></th>
                                <th scope="col" style='background-color:#B9D5CE;'>H. inicio</th>
                                <th scope="col" style='background-color:#B9D5CE;'>H. corte</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Linea</th>
                            </thead>
                            <body>
                                <?php 
                                if($modelo){
                                    foreach ($modelo as $val):?>
                                        <tr style='font-size:85%;'>  
                                            <td><?= $val->idordenproduccion ?></td>
                                            <td><?= $val->ordenproduccion->codigoproducto ?></td>
                                            <?php if($val->id_operario == (NULL)){?>
                                                <td style='background-color:#AFE7CB;'><?= 'REGISTRO EN CONSTRUCCION'?> </td> 
                                            <?php }else{?>
                                                <td><?= $val->operarioProduccion->nombrecompleto?> </td>
                                            <?php }
                                            if($val->idproceso == null){ ?>    
                                                <td style='background-color:#B9D5CE;'><?= 'NO FOUND'?></td>
                                            <?php }else{?>    
                                                <td><?= $val->operaciones->proceso?></td>
                                            <?php }?>    
                                            <?php if($val->iddetalleorden == null){?>
                                                <td style='background-color:#B9D5CE;'><?= 'NO FOUND'?></td>
                                            <?php }else{?>
                                                <td><?= $val->detalleOrdenProduccion->productodetalle->prendatipo->talla->talla ?></td> 
                                            <?php }?>    
                                             <td><?= $val->dia_pago ?></td> 
                                            <td align="right"><?= ''.number_format($val->cantidad,0) ?></td>
                                            <td align="right"><?= ''.number_format($val->vlr_prenda,0) ?></td>
                                            <td align="right"><?= ''.number_format($val->vlr_pago,0) ?></td>
                                              <td><?= $val->porcentaje_cumplimiento ?> %</td>
                                            <td><?= $val->planta->nombre_planta?></td>
                                            <td><?= $val->hora_inicio?></td>
                                            <td><?= $val->hora_corte?></td>
                                             <td><?= $val->hora_descontar?></td>

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
                        <table class="table table-bordered table-hover">
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
                                                <tr>
                                                    <td><?= $validar->operarioProduccion->documento?></td>
                                                    <td><?= $validar->operarioProduccion->nombrecompleto?></td>
                                                    <td><?= $validar->dia_pago?></td>
                                                    <td><?= $validar->aplicaSabado?></td>
                                                    

                                                     <?php if($cont > 0){?>
                                                        <td><?= $promedio?>%</td>
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
                                    }else{
                                        //CICLO QUE BUSQUEDA EFICIENCIA POR PLANTA
                                    }    
                               }     
                                      
                                ?>  
                                </body>    
                        </table>
                         <?php $form->end() ?>
                    </div>
                </div>    
            </div>    
        </div>
        <!-- TERMINA TABS-->
       
    </div>
 </div>
<?php if($modelo){?>
    <?= LinkPager::widget(['pagination' => $pagination]) ?>
<?php }?>
