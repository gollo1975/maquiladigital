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
use yii\grid\GridView;
//Modelos...
use app\models\Empleado;
use app\models\GrupoPago;
use app\models\ConceptoSalarios;
use app\models\Contrato;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LicenciaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Personal al contrato';
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
    "action" => Url::toRoute(["ingreso-personal-contrato/view", 'id'=>$id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte ]),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
$empleado = ArrayHelper::map(Empleado::find()->orderBy('nombrecorto ASC')->all(), 'id_empleado','nombrecorto');
$conceptosalario = ArrayHelper::map(ConceptoSalarios::find()->where(['tipo_adicion'=> 1])->orWhere(['tipo_adicion'=> 2])->all(), 'codigo_salario', 'nombre_concepto');
?>
<div class="panel-footer text-left"> 
    <a href="<?= Url::toRoute(["ingreso-personal-contrato/index"]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>  
</div>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
          
           <?= $formulario->field($form, 'id_empleado')->widget(Select2::classname(), [
                'data' => $empleado,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
      
        </div> 
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute(["ingreso-personal-contrato/view" , "id" => $id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio])?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
<div class="panel panel-success ">
    <div class="panel-heading">
       Registros <span class="badge"><?= $pagination->totalCount ?></span>
       
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style="font-size: 85%;">      
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Desde</th>
                <th scope="col" style='background-color:#B9D5CE;'>Hasta</th>
                <th scope="col" style='background-color:#B9D5CE;'>Nombre operación</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total dias</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                <th scope="col" style='background-color:#B9D5CE;'>Vl. unitario</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total pagar</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Linea importada">Imp.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th score="col" style='background-color:#B9D5CE;'></th>                              
            </tr>
            </thead>
            <tbody>
                <?php
                $auxiliar = 0;
                foreach ($model as $val): ?>
                    <tr style="font-size: 85%;">                
                        <td><?= $val->id?></td>
                         <td><?= $val->empleado->nombrecorto?></td>
                        <td><?= $fecha_inicio ?></td>
                        <td><?= $fecha_corte ?></td>
                        <td><?= $val->operacion?></td>
                         <td style="text-align: right"><?= $val->total_dias?></td>
                        <td style="text-align: right"><?=$val->cantidad?></td>
                        <td style="text-align: right"><?= '$'.number_format($val->valor_unitario,0)?></td>
                        <td style="text-align: right"><?= '$'.number_format($val->total_pagar,0)?></td>
                          <td><?= $val->importadoRegistro ?></td>
                        <?php 
                        if($val->ingreso->estado_proceso == 0){ ?>   

                            <td style="width: 20px;">
                                <a href="<?= Url::toRoute(["ingreso-personal-contrato/vista",  'id' => $id , 'id_detalle' => $val->id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                            </td>
                            <?php
                            if($val->importado == 0){ ?>  
                                <td style="width: 20px;">
                                 <a href="<?= Url::toRoute(["ingreso-personal-contrato/updatevista", "id"=>$id, "id_detalle" => $val->id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                                </td>
                                <td style="width: 20px;">
                                    <?= Html::a('', ['eliminar_adicional', 'id_detalle' => $val->id, 'id' => $id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio], [
                                        'class' => 'glyphicon glyphicon-trash',
                                        'data' => [
                                            'confirm' => 'Esta seguro de eliminar el registro?',
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                </td>
                            <?php 
                            }else{
                                if($auxiliar <> $val->id_empleado){?>
                                    <td>
                                        <a href="<?= Url::toRoute(['ingreso-personal-contrato/crear_otro_contrato', 'id' => $id,'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio, 'id_empleado' => $val->id_empleado,'id_detalle' => $val->id
                                                ])?>"
                                       class="btn btn-primary btn-sm"
                                       onclick="return confirm('¿Estás seguro de generar el OTRO SI AL CONTRATO? ¡Esta acción no se puede deshacer!');">
                                       <?= 'Crear otro si'?></a>
                                       <?php  $auxiliar = $val->id_empleado;?>
                                    </td>    
                                <?php }else{
                                    $auxiliar = $val->id_empleado;?>
                                      <td style="width: 20px;"></td>                             

                                <?php }?>
                                    <td style="width: 20px;"></td>
                            <?php }    
                        }else{ ?>
                            <td style="width: 20px;">
                                <a href="<?= Url::toRoute(["ingreso-personal-contrato/vista", 'id' => $id , "id_detalle" => $val->id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                            </td>   
                            <td style="width: 20px;"></td>
                            <td style="width: 20px;"></td>
                        <?php     
                        } ?>     
                            
                        </tr>            

                <?php endforeach; ?>
            </tbody>                
        </table> 
      <?php if($modelo->estado_proceso == 0){?>
        <div class="panel-footer text-right" >   
            <?= Html::a('<span class="glyphicon glyphicon-import"></span> Importar desde excel', ['ingreso-personal-contrato/importar_conceptos_excel', 'id' => $id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte], ['class' => 'btn btn-info btn-sm'])?>
            <?php if(app\models\IngresosDeduccionesDetalle::find()->where(['=','id_ingreso', $id])->one()){?>
                <?= Html::a('<span class="glyphicon glyphicon-export"></span> Expotar a excel', ['exportar_registros', 'id' => $id], ['class' => 'btn btn-primary btn-sm']);?>
            <?php }?>
            <a align="right" href="<?= Url::toRoute(["ingreso-personal-contrato/createadicion", 'id'=>$id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio])?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Crear ingresos</a>
           
        </div>
      <?php }else{?>
       <div class="panel-footer text-right" >  
            <?= Html::a('<span class="glyphicon glyphicon-export"></span> Expotar a excel', ['exportar_registros', 'id' => $id], ['class' => 'btn btn-primary btn-sm']);?>
        </div>
      <?php }?>
       <?php $form->end() ?>
     </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>


