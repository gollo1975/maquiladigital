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
use app\models\TipoPagoCredito;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Consulta de abonos a creditos';
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
    "action" => Url::toRoute("credito-operarios/search_abono_credito"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$concepto = ArrayHelper::map(\app\models\ConceptoSalarios::find()->where(['=','tipo_adicion', 2])->orderBy('nombre_concepto ASC')->all(),'codigo_salario' , 'nombre_concepto');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
            <?= $formulario->field($form, "numero_credito")->input("search") ?>
             <?= $formulario->field($form, 'codigo_credito')->widget(Select2::classname(), [
                'data' => $concepto,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
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
           
       
        </div>
        
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("credito-operarios/search_abono_credito") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Tipo credito</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Fecha corte</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Valor abono</th>                
                </tr>
            </thead>
            <tbody>
                <?php 
                if($model){
                    foreach ($model as $val): ?>
                        <tr style= 'font-size:85%;'>                
                            <td><?= $val->pago->operarios->documento?></td>
                             <td><?= $val->pago->operarios->nombrecompleto?></td>
                             <td><?= $val->codigoSalario->nombre_concepto?></td>
                             <td><?= $val->fecha_corte?></td>
                             <td style="text-align: right"><?= '$'.number_format($val->deduccion,0)?></td>
                        </tr>            
                    <?php endforeach;
                }   ?>
            </tbody>                 
        </table> 
     </div>
    <div class="panel-footer text-right" >     
             <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar a excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
        </div>        
        <?php $form->end() ?>
</div>
<?php if($model){?>
    <?= LinkPager::widget(['pagination' => $pagination]) ?>
<?php }?>
