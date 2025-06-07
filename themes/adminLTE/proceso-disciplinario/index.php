<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

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



$this->title = 'Proceso disciplinario';
$this->params['breadcrumbs'][] = $this->title;


?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtroproceso");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista proveedor</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("proceso-disciplinario/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
$conEmpleado = ArrayHelper::map(\app\models\Empleado::find()->orderBy('nombrecorto ASC')->all(), 'id_empleado', 'nombrecorto');
$conGrupo = ArrayHelper::map(\app\models\GrupoPago::find()->where(['=','estado', 1])->all(), 'id_grupo_pago', 'grupo_pago');
$conMotivo = ArrayHelper::map(\app\models\MotivoDisciplinario::find()->orderBy('concepto ASC')->all(), 'id_motivo', 'concepto');
$conProceso = ArrayHelper::map(\app\models\TipoProcesoDisciplinario::find()->all(), 'id_tipo_disciplinario', 'concepto');


?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtroproceso" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, 'empleado')->widget(Select2::classname(), [
                'data' => $conEmpleado,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?> 
            <?= $formulario->field($form, 'proceso')->widget(Select2::classname(), [
                'data' => $conProceso,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?> 
            <?= $formulario->field($form, 'desde')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?= $formulario->field($form, 'hasta')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?= $formulario->field($form, 'motivo')->widget(Select2::classname(), [
                'data' => $conMotivo,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?> 
            <?= $formulario->field($form, 'grupo_pago')->widget(Select2::classname(), [
                'data' => $conGrupo,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?> 
            
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("proceso-disciplinario/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        <?php if($model){?> 
            Registros <span class="badge"><?= $pagination->totalCount ?></span>
        <?php } ?>     
    </div>
        <table class="table table-bordered table-hover">
            <thead>
           <tr style="font-size: 85%;">    
                <th scope="col" style='background-color:#B9D5CE;'>Radicado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha hora actualizado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th>
                <th scope="col" style='background-color:#B9D5CE;'>Motivo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proceso</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Proceso cerrado">Cerrado</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th> 
                <th scope="col" style='background-color:#B9D5CE;'></th> 
            </tr>
            </thead>
            <tbody>
                <?php
                foreach ($model as $val): ?>
                    <tr style="font-size: 85%;">                   
                         <td><?= $val->numero_radicado ?></td>
                        <td><?= $val->empleado->identificacion ?></td>
                        <td><?= $val->empleado->nombrecorto ?></td>
                        <td><?= $val->fecha_hora_proceso?></td>
                        <td><?= $val->fecha_registro ?></td>
                        <?php if($val->id_motivo == null){?>
                            <td><?= 'NOT FOUND'?></td>
                        <?php }else{?>
                            <td><?= $val->motivo->concepto?></td>
                         <?php }?>    
                            <td style="background-color: <?= $val->tipoDisciplinario->color_proceso?>"><?= $val->tipoDisciplinario->concepto?></td>
                             <td><?= $val->procesoCerrado?></td>
                        <td style= 'width: 25px; height: 20px;'>
                            <a href="<?= Url::toRoute(["proceso-disciplinario/view", "id" => $val->id_proceso, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                        </td>
                        <?php 
                        if($val->autorizado == 0){?>
                            <td style= 'width: 25px; height: 20px;'>
                                <a href="<?= Url::toRoute(["proceso-disciplinario/update", "id" => $val->id_proceso])?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                            </td>
                        <?php }else{?>
                            <td style= 'width: 25px; height: 20px;'></td>
                        <?php }?>    
                    </tr>
                <?php endforeach; ?>
            </tbody>        
        </table>
        <div class="panel-footer text-right" >
             <?php
                $form = ActiveForm::begin([
                            "method" => "post",                            
                        ]);
                ?> 
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>
                <td style="width: 25px; height: 25px;">
                            <!-- Inicio Nuevo Detalle proceso -->
                              <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nuevo ',
                                  ['/proceso-disciplinario/crear_documento_nuevo'],
                                  [
                                      'title' => 'Crear nuevo proceso disciplinario',
                                      'data-toggle'=>'modal',
                                      'data-target'=>'#modalcreardocumentonuevo',
                                      'class' => 'btn btn-success btn-sm',
                                      'data-backdrop' => 'static',

                                  ])    
                             ?>
                </td> 
                <div class="modal remote fade" id="modalcreardocumentonuevo">
                    <div class="modal-dialog modal-lg" style ="width: 650px;">
                       <div class="modal-content"></div>
                    </div>
                </div>
              <?php $form->end() ?>
            
        </div>
    </div>
</div>
 
   <?= LinkPager::widget(['pagination' => $pagination]) ?>
