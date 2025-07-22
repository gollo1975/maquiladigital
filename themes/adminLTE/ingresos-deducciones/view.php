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

$this->title = 'Ingresos / Deducciones';
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
    "action" => Url::toRoute(["ingresos-deducciones/view", 'id'=>$id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte ]),
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
    <a href="<?= Url::toRoute(["ingresos-deducciones/index"]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>  
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
      
            <?= $formulario->field($form, 'codigo_salario')->widget(Select2::classname(), [
                'data' => $conceptosalario,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);?>
              <?= $formulario->field($form, 'tipo_adicion')->dropDownList(['0' => "TODAS","1" => 'SUMA', "2" => 'RESTA'],['prompt' => 'Seleccione una opcion ...']) ?>
            
        </div> 
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute(["ingresos-deducciones/view" , "id" => $id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio])?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <th scope="col" style='background-color:#B9D5CE;'>Concpeto</th>
                <th scope="col" style='background-color:#B9D5CE;'>Valor pago</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Debito / Credito">D/C</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th score="col" style='background-color:#B9D5CE;'></th>                              
            </tr>
            </thead>
            <tbody>
                <?php foreach ($model as $val): ?>
                    <tr style="font-size: 85%;">                
                        <td><?= $val->id_detalle?></td>
                         <td><?= $val->empleado->nombrecorto?></td>
                        <td><?= $fecha_inicio ?></td>
                        <td><?= $fecha_corte ?></td>
                        <td><?= $val->codigoSalario->nombre_concepto?></td>
                        <td style="text-align: right"><?= '$'.number_format($val->valor_pagado,0)?></td>
                        <?php if($val->suma_resta !== 1){?>
                            <td style="background-color: #f5c6cb; "><?= $val->sumaResta?></td>
                        <?php }else{?>
                            <td style="background-color: #73a6c0"><?= $val->sumaResta?></td>
                        <?php }    
                        if($val->ingreso->estado_proceso == 0){ ?>   

                            <td style="width: 20px;">
                                <a href="<?= Url::toRoute(["ingresos-deducciones/vista",  'id' => $id , 'id_detalle' => $val->id_detalle, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                             </td>
                             <td style="width: 20px;">
                                 <a href="<?= Url::toRoute(["ingresos-deducciones/updatevista", "id"=>$id, "id_detalle" => $val->id_detalle, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                            </td>
                            <td style="width: 20px;">
                                <?= Html::a('', ['eliminar_adicional', 'id_detalle' => $val->id_detalle, 'id' => $id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio], [
                                    'class' => 'glyphicon glyphicon-trash',
                                    'data' => [
                                        'confirm' => 'Esta seguro de eliminar el registro?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            </td>
                        <?php }else{ ?>
                            <td style="width: 20px;">
                                <a href="<?= Url::toRoute(["ingresos-deducciones/vista", 'id' => $id , "id_detalle" => $val->id_detalle, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                             </td>   
                            <td></td>    
                             <td></td>
                        <?php     
                        } ?>     
                            
                        </tr>            

                <?php endforeach; ?>
            </tbody>                
        </table> 
      <?php if($modelo->estado_proceso == 0){?>
        <div class="panel-footer text-right" >   
            <?php if(app\models\IngresosDeduccionesDetalle::find()->where(['=','id_ingreso', $id])->one()){?>
                <?= Html::a('<span class="glyphicon glyphicon-export"></span> Expotar a excel', ['exportar_registros', 'id' => $id], ['class' => 'btn btn-primary btn-sm']);?>
            <?php }?>
            <a align="right" href="<?= Url::toRoute(["ingresos-deducciones/createadicion", 'id'=>$id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio])?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Ingresos</a>
            <a align="right" href="<?= Url::toRoute(["ingresos-deducciones/createdescuento", 'id'=> $id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio])?>" class="btn btn-info btn-sm"><span class='glyphicon glyphicon-minus-sign'></span> Deducciones</a>
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

