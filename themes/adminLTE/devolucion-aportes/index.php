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

$this->title = 'Devolución de aportes';
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
    "action" => Url::toRoute("devolucion-aportes/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$concepto = ArrayHelper::map(\app\models\ConceptoSalarios::find()->orderBy('nombre_concepto ASC')->all(),'codigo_salario' , 'nombre_concepto');
$conEmple = ArrayHelper::map(Empleado::find()->orderBy('nombrecorto ASC')->all(),'id_empleado' , 'nombrecorto');
$fecha_inicio = \Yii::$app->formatter->asDate($form->fecha_inicio, 'php:Y-m-d');
$fecha_corte = \Yii::$app->formatter->asDate($form->fecha_corte, 'php:Y-m-d');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
             <?= $formulario->field($form, 'concepto')->widget(Select2::classname(), [
                'data' => $concepto,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'id_empleado')->widget(Select2::classname(), [
                'data' => $conEmple,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]); ?>
            <?= $formulario->field($form, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']
                ])
            ?>
        
            <?= $formulario->field($form, 'fecha_corte')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom'
                ]])
            ?>
           
       
        </div>
        
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("devolucion-aportes/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
        <?php if($model){?>
          Registros: <span class="badge"> <?= $pagination->totalCount ?></span>
        <?php }?>    
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>                
                    <th scope="col" style='background-color:#B9D5CE;'>ID</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Concepto</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Desde</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Hasta</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Valor concepto</th>                
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;                  

                if (!empty($model)): 
                   
                       foreach ($model as $detalle):?>
                            <tr style="font-size:85%;">
                                <td><?= $detalle->id_devolucion ?></td>
                                <td><?= $detalle->empleado->identificacion ?></td>
                                <td><?= $detalle->empleado->nombrecorto ?? 'N/A' ?></td>
                                <td><?= $detalle->codigoSalario->nombre_concepto ?? 'N/A' ?></td>
                                <td><?= $detalle->fecha_inicio ?? 'N/A' ?></td>
                                <td><?= $detalle->fecha_corte ?? 'N/A' ?></td>
                                <td style="text-align: right"><?= '$' . number_format($detalle->total_devolucion, 0) ?></td>
                                
                            </tr>
                        <?php endforeach; ?>
                       
                <?php else: ?>
                    <tr>
                        <td colspan="12" style="text-align: center">
                            <div class="alert alert-info" role="alert" style="margin: 20px 0;">
                                No hay registros en la búsqueda...
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>                 
        </table>
        
     </div>
    <?php if (!empty($model)){ ?>
        <div class="panel-footer text-right" >     
             <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar a excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
             
        </div>
    <?php }?>
      
</div>
  <?php $form->end() ?>
<?php if($model){?>
    <?= LinkPager::widget(['pagination' => $pagination]) ?>
<?php }?>
