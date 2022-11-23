<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Operarios;
use app\models\TipoNovedad;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Novedades';
$this->params['breadcrumbs'][] = $this->title;


?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtro");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("novedad-operario/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$operario = ArrayHelper::map(Operarios::find()->orderBy('nombrecompleto ASC')->all(), 'id_operario', 'nombrecompleto');
$novedad = ArrayHelper::map(TipoNovedad::find()->orderBy('novedad ASC')->all(), 'id_tipo_novedad', 'novedad');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "documento")->input("search") ?>
            <?= $formulario->field($form, 'id_operario')->widget(Select2::classname(), [
                'data' => $operario,
                'options' => ['prompt' => 'Seleccione el operario...'],
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
             <?= $formulario->field($form, 'tipo_novedad')->widget(Select2::classname(), [
                'data' => $novedad,
                'options' => ['prompt' => 'Seleccione la novedad...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'cerrado')->dropDownList(['' => 'TODOS', '1' => 'SI', '0' => 'NO'],['prompt' => 'Seleccione el estado ...']) ?>
            <?= $formulario->field($form, 'autorizado')->dropDownList(['' => 'TODOS', '1' => 'SI', '0' => 'NO'],['prompt' => 'Seleccione el estado ...']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("novedad-operario/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
        Registros <span class="badge"> <?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:95%;'>                
                <th scope="col" style='background-color:#B9D5CE;'>Nro</th>
                <th scope="col" style='background-color:#B9D5CE;'>Novedad</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha final</th>
                <th scope="col" style='background-color:#B9D5CE;'>Hora inicio</th>
                <th scope="col" style='background-color:#B9D5CE;'>Hora final</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Autorizado" >Aut.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Proceso cerrado" >Cerrado</span></th>
                 <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
              
            </tr>
            </thead>
            <tbody>
            <?php 
             
            foreach ($modelo as $val):?>
                <tr style='font-size:85%;'>                
                <td><?= $val->nro_novedad ?></td>
                 <td><?= $val->tipoNovedad->novedad ?></td>
                <td><?= $val->documento?></td>
                <td><?= $val->operario->nombrecompleto?></td>
                <td><?= $val->fecha_inicio_permiso ?></td>
                <td><?= $val->fecha_final_permiso ?></td>
                <td><?= $val->hora_inicio_permiso ?></td>
                <td><?= $val->hora_final_permiso ?></td>
                <td><?= $val->estadoAutorizado?></td>
                <td><?= $val->procesoCerrado?></td>
                <td><?= $val->usuario ?></td>
                <td style= 'width: 25px; height: 25px;'>
                        <a href="<?= Url::toRoute(["novedad-operario/view", "id" => $val->id_novedad, ]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                </td>
                <?php if($val->cerrado == 0){?>
                    <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["novedad-operario/update", "id" => $val->id_novedad, ]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                    </td>
                <?php }else{?>
                    <td style= 'width: 25px; height: 25px;'></td>
                <?php }?>    
             
            </tbody>            
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <a align="right" href="<?= Url::toRoute("novedad-operario/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>


