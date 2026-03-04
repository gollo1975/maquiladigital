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
$this->title = 'Listado de operaciones';
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
    "action" => Url::toRoute("valor-prenda-unidad/valor_prenda_app"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);


?>
<div class="panel panel-success">
        <div class="panel-heading">
            Detalle del registro  
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?>:</th>
                    <td><?= Html::encode($model->id_operario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'documento') ?>:</th>
                    <td><?= Html::encode($model->documento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Operario') ?>:</th>
                    <td><?= Html::encode($model->nombrecompleto) ?></td>
                                      
                </tr>                
            </table>
        </div>
    <?php $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
    <div class="table-responsive">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#listado" aria-controls="listado" role="tab" data-toggle="tab">Listado <span class="badge"><?= $pagination->totalCount?></span></a></li>
        </ul>
  
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
                                        if ($val['id_operario'] !== $current_operario_id):
                                            // Si es diferente, mostramos una fila de encabezado para el nuevo operario.
                                            // Esto crea el efecto de "agrupación".
                                    ?>
                                        <tr style='font-size:95%; font-weight:bold; background-color:#F5F5F5;'>
                                            <td colspan="15">
                                                <?= $val['operarioProduccion']['nombrecompleto'] ?? 'Sin Nombre' ?>
                                            </td>
                                        </tr>
                                    <?php
                                            // Actualizamos la variable con el ID del nuevo operario
                                            $current_operario_id = $val['id_operario'];
                                        endif;
                                    ?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $val['idordenproduccion'] ?></td>
                                            <td><?= $val['ordenproduccion']['codigoproducto'] ?? 'N/A' ?></td>
                                            <td><?= $val['operaciones']['idproceso'] ?? 'NO FOUND' ?></td>
                                            <td><?= $val['operaciones']['proceso'] ?? 'NO FOUND' ?></td>
                                            <td><?= $val['detalleOrdenProduccion']['productodetalle']['prendatipo']['talla']['talla'] ?? 'NO FOUND' ?></td>

                                            <td><?= $val['dia_pago'] ?></td>
                                            <td align="right"><?= number_format($val['cantidad'], 0) ?></td>
                                            <td><?= $val['porcentaje_cumplimiento'] ?> %</td>
                                            <td><?= $val['planta']['nombre_planta'] ?? 'N/A' ?></td>
                                            <td><?= $val['hora_inicio'] ?></td>
                                            <?php
                                            // Usamos una variable para almacenar la clase CSS
                                            if (!empty($val['hora_inicio_desayuno'])) {?>
                                               
                                                <td style="background-color: #c9e2b3; color: #385d2a;"><?= $val['hora_inicio_desayuno'] ?></td>
                                               
                                            <?php } elseif (!empty($val['hora_inicio_almuerzo'])) {?>
                                                 <td style="background-color: #ffe6b3; color: #664d03;"><?= $val['hora_inicio_almuerzo'] ?></td>
                                                 
                                            <?php } else {?>
                                                 <td style="background-color: #e9ecef; color: #495057;"><?= $val['hora_corte'] ?></td>
                                           <?php } ?>
                                            <td><?= $val['minuto_prenda'] ?></td>
                                            <td><?= $val['tiempo_real_confeccion'] ?></td>
                                            <?php
                                            if($val['diferencia_tiempo'] > 0){?>
                                                <td style="background-color: #ffe6b3; color: green;"><?= $val['diferencia_tiempo'] ?></td>
                                            <?php }else{?>
                                                <td style="background-color: #ffe6b3; color: red;"><?= $val['diferencia_tiempo'] ?></td>
                                            <?php }
                                            if($val['diferencia_tiempo'] > 0){?>
                                                <td style="background-color: #c9e2b3 ; color: green;"><?= round($val['diferencia_tiempo'] * 60) ?></td>
                                            <?php }else{?>
                                                <td style="background-color: #c9e2b3; color: red;"><?= round($val['diferencia_tiempo'] * 60) ?></td>
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
    </div>
    </div>
    
    <?php $form->end() ?>
</div>    

<?php if($modelo){?>
    <?= LinkPager::widget(['pagination' => $pagination]) ?>
<?php }?>
