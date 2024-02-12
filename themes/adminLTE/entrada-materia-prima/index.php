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
//Modelos...
use app\models\Proveedor;

$this->title = 'Entrada (Materia prima)';
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
    "action" => Url::toRoute("entrada-materia-prima/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
$proveedor = ArrayHelper::map(Proveedor::find()->orderBy ('nombrecorto ASC')->all(), 'idproveedor', 'nombrecorto');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            
            
           
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
             <?= $formulario->field($form, 'proveedor')->widget(Select2::classname(), [
                'data' => $proveedor,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?> 
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("entrada-materia-prima/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <tr style ='font-size: 90%;'>         
                
                <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proveedor</th>
                <th scope="col" style='background-color:#B9D5CE;'>Soporte</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. entrada</th>
                <th scope="col" style='background-color:#B9D5CE;'>Subtotal</th>
                <th scope="col" style='background-color:#B9D5CE;'>Impuesto</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total </th>
                <th scope="col" style='background-color:#B9D5CE;'>User name </th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Autorizado">Aut.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th score="col" style='background-color:#B9D5CE;'></th>                              
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): 
                $detalle = app\models\EntradaMateriaPrimaDetalle::find()->where(['=','id_entrada', $val->id_entrada])->one();
                ?>
                <tr style ='font-size: 90%;'>                
                    <td><?= $val->id_entrada?></td>
                    <td><?= $val->proveedor->nombrecorto?></td>
                    <td><?= $val->numero_soporte?></td>
                    <td><?= $val->fecha_proceso?></td>
                    <td style="text-align: right;"><?= ''.number_format($val->subtotal,0)?></td>
                    <td style="text-align: right"><?= ''.number_format($val->impuesto,0)?></td>
                    <td style="text-align: right"><?= ''.number_format($val->total_salida,0)?></td>
                    <td><?= $val->user_name_crear?></td>
                    <td><?= $val->autorizadoEntrada?></td>
                    <td style= 'width: 20px; height: 20px;'>
                            <a href="<?= Url::toRoute(["entrada-materia-prima/codigo_barra_ingreso", "id" => $val->id_entrada]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                    </td>
                    <?php if(!$detalle){?>
                        <td style= 'width: 20px; height: 20px;'>
                            <a href="<?= Url::toRoute(["entrada-producto-terminado/update", "id" => $val->id_entrada, 'sw' => 1]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                        </td>
                    <?php }else{?>    
                        <td style= 'width: 20px; height: 20px;'>
                    <?php }?>  
                </tr>            
            <?php endforeach; ?>
            </tbody>    
        </table> 
        <div class="panel-footer text-right" >            
           <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
            <a align="right" href="<?= Url::toRoute(["entrada-materia-prima/create"]) ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
        <?php $form->end() ?>
        </div>
     </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>
