<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use kartik\select2\Select2;
use kartik\date\DatePicker;


$this->title = 'Documento soporte';
$this->params['breadcrumbs'][] = $this->title;


?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtrodocumento");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista proveedor</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("documento-soporte/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtrodocumento" style="display:none">
        <div class="row" >
             <?= $formulario->field($form, 'proveedor')->widget(Select2::classname(), [
                'data' => $conProveedor,
                'options' => ['prompt' => 'Seleccione el empleado...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
                <?= $formulario->field($form, "numero_soporte")->input("search") ?>
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
          
            <?= $formulario->field($form, "numero_compra")->input("search") ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("documento-soporte/index") ?>" class="btn btn-success  btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros: <?= $pagination->totalCount ?>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
           <tr style="font-size: 85%;">    
                <th scope="col" style='background-color:#B9D5CE;'>Documento soporte</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proveedor</th>
                <th scope="col" style='background-color:#B9D5CE;'>Numero de compra</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha elaboracion</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha recepcion dian</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Forma de pago</th>
                <th scope="col" style='background-color:#B9D5CE;'>Valor pagar</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>  
                <th scope="col" style='background-color:#B9D5CE;'></th>  
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
            <tr style="font-size: 85%;">                   
                 <td><?= $val->numero_soporte ?></td>
                <td><?= $val->proveedor->nombrecorto ?></td>
                <td><?= $val->documento_compra ?></td>
                <td><?= $val->fecha_elaboracion ?></td>
                <td><?= $val->fecha_recepcion_dian ?></td>
                <td><?= $val->formaPago->concepto ?></td>
                 <td style="text-align: right"><?= ''. number_format($val->valor_pagar,0) ?></td>
                <td style= 'width: 15px; height: 15px;'>
                    <a href="<?= Url::toRoute(["documento-soporte/view", "id" => $val->id_documento_soporte]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                </td>
                <?php if(!\app\models\DocumentoSoporteDetalle::find()->where(['=','id_documento_soporte', $val->id_documento_soporte])->one()){
                    if($val->id_compra != ''){?>
                        <td style= 'width: 20px; height: 20px;'>
                            <a href="<?= Url::toRoute(["documento-soporte/update", "id" => $val->id_documento_soporte,'sw' => 1])?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                        </td>
                    <?php }else{?>
                        <td style= 'width: 20px; height: 20px;'>
                            <a href="<?= Url::toRoute(["documento-soporte/update", "id" => $val->id_documento_soporte, 'sw' => 0])?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                        </td>
                    <?php }
                }else{?>
                        <td style= 'width: 15px; height: 15px;'></td> 
                <?php }?>        
            </tr>
            </tbody>
            <?php endforeach; ?>
        </table>
        <div class="panel-footer text-right" >
             <?php
                $form = ActiveForm::begin([
                            "method" => "post",                            
                        ]);
                ?>    
            <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>
            <a align="right" href="<?= Url::toRoute(["documento-soporte/create",'sw' => 0]) ?>" class="btn btn-info btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo sin CC </a>
            <a align="right" href="<?= Url::toRoute(["documento-soporte/create",'sw' => 1 ]) ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo con CC</a>
            <?php $form->end() ?>
            
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>