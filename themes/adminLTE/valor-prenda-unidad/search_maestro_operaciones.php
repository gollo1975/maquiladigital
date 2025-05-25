<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
use yii\widgets\LinkPager;
//model
use app\models\FlujoOperaciones;

$this->title = 'Maestro operaciones (Consulta)';
$this->params['breadcrumbs'][] = ['label' => 'Maestro operaciones', 'url' => ['maestro_operaciones']];
if($sw == 1){
    $listado_talla = ArrayHelper::map(\app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $form->idordenproduccion])->all(), 'iddetalleorden', 'listadoTalla');
}
if($form->idordenproduccion > 0){
    $operaciones = ArrayHelper::map(FlujoOperaciones::find()->where(['=','idordenproduccion', $form->idordenproduccion])->orderBy('idproceso DESC')->all(), 'idproceso', 'mostrarOperacion');
}
?>
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("valor-prenda-unidad/maestro_operaciones"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading">
        Parametros de consulta
    </div>
     <div class="panel-body" id="maestro_operaciones">
        <div class="row">
            <?= $formulario->field($form, 'idordenproduccion')->widget(Select2::classname(), [
            'data' => $orden,
            'options' => ['placeholder' => 'Seleccione la orden'],
            'pluginOptions' => [
                'allowClear' => true
            ],
            ]); ?>
            
            <?= $formulario->field($form, 'id_operario')->widget(Select2::classname(), [
            'data' => $operarios,
            'options' => ['placeholder' => 'Seleccione la orden'],
            'pluginOptions' => [
                'allowClear' => true
            ],
            ]); ?>
            <?php if($form->idordenproduccion > 0){?>
                <?= $formulario->field($form, 'idproceso')->widget(Select2::classname(), [
                'data' => $operaciones,
                'options' => ['placeholder' => 'Seleccione la operacion'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
                ]); ?>
            <?php }
             if($sw == 1){?>
                <?= $formulario->field($form, 'iddetalleorden')->widget(Select2::classname(), [
                'data' => $listado_talla,
                'options' => ['placeholder' => 'Seleccione la talla'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
                ]); 
            }?>
        </div>
     </div>    
    <div class="panel-footer text-right">
        <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Search..", ["class" => "btn btn-primary btn-sm",]) ?>
        <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/maestro_operaciones") ?>" class="btn btn-default btn-sm"><span class='glyphicon glyphicon-refresh'></span> Limpiar</a>
    </div>
</div> 
<?php $formulario->end() ?>
<?php $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<div class="panel panel-success">
        <div class="panel-heading">
            <?php if($modelo){?>
                Registros: <span class="badge"> <?= $pagination->totalCount ?></span>
            <?php }?>    
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr style="font-size: 85%;">
                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Operacion</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                         <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Op</th>
                          <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Fecha</th>
                         <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Confeccion</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Valor</th>
                        <th scope="col" style='background-color:#B9D5CE;'>%</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Total pago</th>
                        
                    </tr>
                </thead>
                <?php 
                 if($modelo){
                    $previousIdProceso = null; // 
                    $colSpanCount = 12;
                    foreach ($modelo as $val):
                        if ($previousIdProceso !== null && $val->idproceso !== $previousIdProceso) {
                                                ?>
                            <tr style="background-color: #f0f0f0;"> <td colspan="<?= $colSpanCount ?>" style="text-align: center; font-weight: bold; padding: 10px;">
                                    --- Nueva Operación: <?= $val->operaciones->proceso ?? 'Desconocida' ?> ---
                                </td>
                            </tr>
                         <?php } ?>
                            <tr style="font-size: 85%;">
                                <td><?= $val->idproceso?></td>
                                <?php if($val->idproceso == null){?>
                                    <td><?= 'REGISTRO NO ENCONTRADO'?></td>
                                <?php }else{?>
                                    <td><?= $val->operaciones->proceso?></td>
                                <?php }
                                if($val->iddetalleorden == null){?>
                                    <td><?= 'REGISTRO NO ENCONTRADO'?></td>
                                <?php }else{?>
                                    <td><?= $val->detalleOrdenProduccion->productodetalle->prendatipo->talla->talla ?></td>    
                                <?php }?>    
                                <td><?= $val->operarioProduccion->nombrecompleto?></td>
                                <td><?= $val->idordenproduccion?></td>
                                 <td><?= $val->planta->nombre_planta?></td>
                                <td><?= $val->dia_pago?></td>
                                <?php if($val->iddetalleorden == null){?>
                                    <td><?= 'REGISTRO NO ENCONTRADO'?></td>
                                <?php }else{?>
                                   <td><?= $val->detalleOrdenProduccion->cantidad?></td> 
                                <?php }?>    
                                <td><?= $val->cantidad?></td>
                                <td><?= $val->vlr_prenda?></td>
                                 <td><?= $val->porcentaje_cumplimiento?></td>
                                <td style="text-align: right"><?= ''.number_format($val->vlr_pago,0)?></td>
                            </tr>
                        <?php  $previousIdProceso = $val->idproceso; 
                    endforeach;
                 }?>
            </table> 
            <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
                <?php $form->end() ?>
            </div>
        </div>
    </div>       

 <?php if($modelo){?>
    <?= LinkPager::widget(['pagination' => $pagination]) ?>
 <?php }?>

