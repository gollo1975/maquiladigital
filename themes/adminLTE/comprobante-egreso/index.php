<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Cliente;
use app\models\Proveedor;
use app\models\TipoRecibo;
use app\models\ComprobanteEgresoTipo;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Comprobantes de egreso';
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
    "action" => Url::toRoute("comprobante-egreso/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$proveedores = ArrayHelper::map(Proveedor::find()->orderBy('nombrecorto ASC')->all(), 'idproveedor', 'nombreProveedores');
$tipos = ArrayHelper::map(ComprobanteEgresoTipo::find()->orderBy('concepto ASC')->all(), 'id_comprobante_egreso_tipo', 'concepto');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, 'idproveedor')->widget(Select2::classname(), [
                'data' => $proveedores,
                'options' => ['prompt' => 'Seleccione un cliente ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, "numero")->input("search") ?>
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
              <?= $formulario->field($form, 'tipo')->widget(Select2::classname(), [
                'data' => $tipos,
                'options' => ['prompt' => 'Seleccione ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
          
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("comprobante-egreso/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
        Registros: <?= $pagination->totalCount ?>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
            <tr>                
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Numero</th>                
                <th scope="col" style='background-color:#B9D5CE;'>Tipo Pago</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. Pago</th>                
                <th scope="col" style='background-color:#B9D5CE;'>F. Proceso</th> 
                <th scope="col" style='background-color:#B9D5CE;'>Banco</th> 
                <th scope="col" style='background-color:#B9D5CE;'>Cedula/Nit</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proveedor</th>
                <th scope="col" style='background-color:#B9D5CE;'>Valor</th>                
                <th scope="col" style='background-color:#B9D5CE;'>Aut.</th>                
                <th scope="col" style='background-color:#B9D5CE;'>Estado</th>  
                <th scope="col" style='background-color:#B9D5CE;'></th>                               
                <th scope="col" style='background-color:#B9D5CE;'></th> 
            </tr>
            </thead>
            <tbody>
                <?php foreach ($model as $val): ?>
                    <tr style="font-size: 85%;">                
                        <td><?= $val->id_comprobante_egreso ?></td>
                         <td><?= $val->numero ?></td>
                        <td><?= $val->comprobanteEgresoTipo->concepto ?></td>                
                        <td><?= $val->fecha_comprobante ?></td>                
                        <td><?= $val->fecha ?></td>        
                        <td><?= $val->banco->entidad ?></td>
                        <td><?= $val->proveedor->cedulanit ?></td>
                        <td><?= $val->proveedor->nombrecorto ?></td>
                        <td><?= number_format($val->valor,0) ?></td>                
                        <td><?= $val->autorizar ?></td>   
                         <td><?= $val->estadoRegistro ?></td>
                        <td style="width: 25px;">			
                        <a href="<?= Url::toRoute(["comprobante-egreso/view", "id" => $val->id_comprobante_egreso, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                
                        </td>
                        <td style="width: 25px;">			
                        <a href="<?= Url::toRoute(["comprobante-egreso/update", "id" => $val->id_comprobante_egreso]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                
                        </td>
                   </tr>
                <?php endforeach; ?>
            </tbody>       
        </table>    
        <div class="panel-footer text-right" >            
            <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-default btn-sm ']); ?>                
                <a align="right" href="<?= Url::toRoute("comprobante-egreso/createlibre") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo libre</a> 
                <a align="right" href="<?= Url::toRoute("comprobante-egreso/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a> 
                 <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>







