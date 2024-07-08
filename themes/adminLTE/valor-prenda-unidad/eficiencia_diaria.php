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
                            <thead>
                                <tr style ='font-size:85%;'>    
                                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Cumplimiento</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Nota</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                            </thead>
                            <body>
                                 <?php
                                    $auxiliar = ''; $sumarPorcentaje = 0; $cont = 0; $promedio = 0;
                                    $empresa = Matriculaempresa::findOne(1);
                                    if($modelo){
                                        if($sw == 1){ //buscar la eficiencia por operario en un rago de fechas
                                            foreach ($modelo as $val):
                                              $sumarPorcentaje += $val->porcentaje_cumplimiento;
                                              if($auxiliar <> $val->dia_pago){
                                                  $cont += 1;
                                                  $auxiliar = $val->dia_pago;
                                              }else{
                                                 $auxiliar = $val->dia_pago; 
                                              } 
                                            endforeach;
                                            $promedio = ''.number_format($sumarPorcentaje / $cont,2);
                                            foreach ($modelo as $validar):
                                                if($auxiliar <> $validar->id_operario){
                                                   $auxiliar = $validar->id_operario;?>
                                                    <tr>
                                                        <td><?= $validar->operarioProduccion->documento?></td>
                                                        <td><?= $validar->operarioProduccion->nombrecompleto?></td>
                                                         <?php if($cont > 0){?>
                                                            <td><?= $promedio?>%</td>
                                                            <?php if($promedio >= $empresa->porcentaje_empresa){?>
                                                                <td style='background-color:#e9d8a6;'><?= 'GANA BONIFICACION'  ?>   <span class="glyphicon glyphicon-blackboard"></span></td>
                                                            <?php } else{
                                                                    if($promedio >= $empresa->porcentaje_minima_eficiencia){ ?>
                                                                        <td style='background-color:#83c5be;'><?= 'LE CUMPLE A LA EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-up"></span></td>
                                                                    <?php }else{ ?>
                                                                        <td style='background-color:#b5c99a;'><?= 'NO CUMPLE LA EFICIENCIA DE EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-down"></span></td>
                                                                    <?php }      
                                                            }?>
                                                        <?php } else{ ?>
                                                             <td><?= 'NO FOUNT'?></td>
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
                                                foreach ($modelo as $val):
                                                    $conBuscar = ValorPrendaUnidadDetalles::find()->where(['=','id_operario', $val->id_operario])->andWhere(['>=','dia_pago', $dia_pago])
                                                                                                  ->andWhere(['<=','dia_pago', $fecha_corte])
                                                                                                  ->andWhere(['=','aplica_sabado', 0])
                                                                                                  ->orderBy('dia_pago ASC')->all();
                                                    $cont = 0;
                                                    foreach ($conBuscar as $buscar):
                                                       $sumarPorcentaje += $buscar->porcentaje_cumplimiento;
                                                        if($auxiliar <> $buscar->dia_pago){
                                                            $cont += 1;
                                                            $auxiliar = $buscar->dia_pago;
                                                        }else{
                                                           $auxiliar = $buscar->dia_pago; 
                                                        } 
                                                    endforeach;
                                                    if($cont > 0){
                                                        $promedio = round($sumarPorcentaje / $cont,0);
                                                    }
                                                    $total_registro = $cont;?>
                                                    <tr>
                                                        <td><?= $val->operarioProduccion->documento?></td>
                                                        <td><?= $val->operarioProduccion->nombrecompleto?></td>
                                                        <?php if($cont > 0){?>
                                                            <td><?= $promedio?>%</td>
                                                            <?php if($promedio >= $empresa->porcentaje_empresa){?>
                                                                <td style='background-color:#e9d8a6;'><?= 'GANA BONIFICACION'  ?>   <span class="glyphicon glyphicon-blackboard"></span></td>
                                                            <?php } else{
                                                                    if($promedio >= $empresa->porcentaje_minima_eficiencia){ ?>
                                                                        <td style='background-color:#83c5be;'><?= 'LE CUMPLE A LA EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-up"></span></td>
                                                                    <?php }else{ ?>
                                                                        <td style='background-color:#b5c99a;'><?= 'NO CUMPLE LA EFICIENCIA DE EMPRESA' ?> <span class="glyphicon glyphicon-thumbs-down"></span></td>
                                                                    <?php }      
                                                            }?>
                                                        <?php } else{ ?>
                                                             <td><?= 'NO FOUNT'?></td>
                                                             <td><?= 'NO GANA BONIFICACION'?></td>
                                                        <?php }?>    
                                                        <td><?= $val->planta->nombre_planta?></td>
                                                    </tr> 
                                                   <?php
                                                    $sumarPorcentaje = 0;
                                                    $auxiliar = 0;
                                                   
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

