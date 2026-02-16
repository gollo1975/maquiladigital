<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Empleado;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Consulta otro si';
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
    "action" => Url::toRoute("ingreso-personal-contrato/index_search"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$empleado = ArrayHelper::map(Empleado::find()->orderBy('nombrecorto ASC')->all(), 'id_empleado', 'nombrecorto');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form,'fecha_inicio')->widget(DatePicker::className(),['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']])
            ?>
            <?= $formulario->field($form,'fecha_corte')->widget(DatePicker::className(),['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']])
            ?>
            <?= $formulario->field($form, 'id_empleado')->widget(Select2::classname(), [
                        'data' => $empleado,
                        'options' => ['placeholder' => 'Seleccione el empleado'],
                        'pluginOptions' => [
                            'allowClear' => true ]]);
            ?>
        </div> 
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("ingreso-personal-contrato/index_search") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh '></span> Actualizar</a>
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
         Registros:<span class="badge"><?= $pagination->totalCount ?></span>
       
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style="font-size: 85%">                
                <th scope="col" style='background-color:#B9D5CE;'>Numero</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio periodo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha final labores</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha corte periodo</th>                
                <th scope="col" style='background-color:#B9D5CE;'>Total Dias</th> 
                <th scope="col" style='background-color:#B9D5CE;'>Total pagar</th> 
                <th scope="col" style='background-color:#B9D5CE;'></th>
                
                                     
            </tr>
            </thead>
            <tbody>
                <?php foreach ($model as $val): ?>
                    <tr style="font-size: 85%">                
                        <td><?= $val->id?></td>
                        <td><?= $val->empleado->identificacion?></td>
                        <td><?= $val->empleado->nombrecorto?></td>
                        <td><?= $val->fecha_inicio_periodo?></td>
                        <td><?= $val->fecha_corte_labor?></td>
                        <td><?= $val->fecha_corte_periodo?></td>
                        <td style="text-align: right"><?= $val->dias_trabajo?></td>
                        <td style="text-align: right"><?= ''.number_format($val->total_pagar,0)?></td>
                        <td style="width: 20px; height: 20px">
                           <a href="<?= Url::toRoute(["ingreso-personal-contrato/imprimir_otrosi",'codigo' => $val->id]) ?>" ><span class="glyphicon glyphicon-print" title="Imprimir "></span></a>                                   
                        </td>
                    </tr>            
                <?php endforeach; ?>
            </tbody>    
        </table> 
     
     </div>
</div>
<?php $form->end() ?>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

