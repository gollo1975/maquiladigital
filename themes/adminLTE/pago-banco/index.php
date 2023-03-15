<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Banco;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Pago banco';
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
    "action" => Url::toRoute("pago-banco/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$banco = ArrayHelper::map(Banco::find()->orderBy('idbanco ASC')->all(), 'idbanco', 'entidad');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
             <?= $formulario->field($form, 'id_banco')->widget(Select2::classname(), [
                'data' => $banco,
                'options' => ['prompt' => 'Seleccione el banco...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'tipo_pago')->dropDownList(['' => 'TODOS', '220' => 'PAGO PROVEEDORES', '225' => 'PAGO NOMINA']) ?>
            <?= $formulario->field($form, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
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
             <?= $formulario->field($form, 'tipo_proceso')->dropDownList(['' => 'TODOS', '1' => 'PAGO VINCULADOS', '2' => 'PAGO PRESTACION DE SERVICIOS']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("pago-banco/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <tr style ='font-size:85%;'>                
                <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                <th scope="col" style='background-color:#B9D5CE;'>Banco</th>
                <th scope="col" style='background-color:#B9D5CE;'>Tipo archivo</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Tipo pago</th>
                <th scope="col" style='background-color:#B9D5CE;'>Aplicacion</th>
                <th scope="col" style='background-color:#B9D5CE;'>Secuencia</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha aplicacion</th>
                <th scope="col" style='background-color:#B9D5CE;'>Descripción</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Autorizado proceso" >Autorizado</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Cerrado el proceso" >Cerrado</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
              
            </tr>
            </thead>
            <tbody>
            <?php 
             
            foreach ($modelo as $val):?>
                <tr style='font-size:85%;'>                
                <td><?= $val->id_pago_banco ?></td>
                 <td><?= $val->banco->entidad ?></td>
                <?php if($val->tipo_pago == 220){?>
                   <td><?= 'PAGO PROVEEDORES' ?></td>
                <?php }else{ ?>
                   <td><?= 'PAGO NOMINA' ?></td>
                <?php }?>  
                  <td><?= $val->tipoNomina->tipo_pago ?></td>  
                <?php if($val->aplicacion == 'I'){?>
                   <td><?= 'INMEDIATO' ?></td>
                <?php }else{
                    if($val->aplicacion == 'M'){?>
                       <td><?= 'MEDIO DIA' ?></td>
                    <?php }else{?>
                       <td><?= 'NOCHE' ?></td>
                    <?php }
                   
                }?>      
                <td><?= $val->secuencia ?></td>
                <td><?= $val->fecha_creacion ?></td>
                <td><?= $val->fecha_aplicacion ?></td>
                <td><?= $val->descripcion?></td>
                <td><?= $val->estadoAutorizado?></td>
                <td><?= $val->estadoCerrado?></td>
                <td style= 'width: 25px; height: 25px;'>
                        <a href="<?= Url::toRoute(["pago-banco/view", "id" => $val->id_pago_banco, 'tipo_proceso' => $val->id_tipo_nomina]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                </td>
                <?php if($val->autorizado == 0){?>
                    <td style= 'width: 25px; height: 25px;'>
                         <a href="<?= Url::toRoute(["pago-banco/update", "id" => $val->id_pago_banco, ]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                    </td>
                <?php }else{?>
                    <td style= 'width: 25px; height: 25px;'></td>
                <?php } ?>        
            <?php endforeach; ?>
            </tbody>        
        </table>    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <!-- Inicio Nuevo Detalle proceso -->
                 <a align="right" href="<?= Url::toRoute("pago-banco/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
        </div>
      <?php $form->end() ?>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>


