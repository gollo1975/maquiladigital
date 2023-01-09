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
use app\models\Ordenproducciontipo;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Orden de Producción';
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
    "action" => Url::toRoute("orden-produccion/indexconsulta"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$clientes = ArrayHelper::map(Cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombreClientes');
$tipos = ArrayHelper::map(Ordenproducciontipo::find()->all(), 'idtipo', 'tipo');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, 'idcliente')->widget(Select2::classname(), [
                'data' => $clientes,
                'options' => ['prompt' => 'Seleccione un cliente ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, "codigoproducto")->input("search") ?>
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
            <?= $formulario->field($form, 'facturado')->dropDownList(['0' => 'NO', '1' => 'SI'],['prompt' => 'Seleccione una opcion ...']) ?>
            <?= $formulario->field($form, "ordenproduccionint")->input("search") ?>
            <?= $formulario->field($form, "ordenproduccioncliente")->input("search") ?>
            <?= $formulario->field($form, 'tipo')->dropDownList($tipos, ['prompt' => 'Seleccione un tipo...']) ?>
        </div>
        <div class="row checkbox checkbox-success" align ="center">
                <?= $formulario->field($form, 'mostrar_resultado')->checkbox(['label' => 'Totalizar', '1' =>'small', 'class'=>'bs_switch','style'=>'margin-bottom:10px;', 'id'=>'mostrar_resultado']) ?>
            </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("orden-produccion/indexconsulta") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros: <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
            <tr style="font-size: 85%;">                
                <th style='background-color:#F0F3EF;' scope="col">Op</th>
                <th style='background-color:#F0F3EF;' scope="col">Cedula/Nit</th>
                <th style='background-color:#F0F3EF;' scope="col">Cliente</th>
                <th style='background-color:#F0F3EF;' scope="col">Codigo</th>                
                <th style='background-color:#F0F3EF;' scope="col">Op cliente</th>
                <th style='background-color:#F0F3EF;' scope="col">Cant.</th>
                <th style='background-color:#F0F3EF;' scope="col">Time</th>
                 <th style='background-color:#F0F3EF;' scope="col">Sam</th>
                <th style='background-color:#F0F3EF;' scope="col">F. llegada</th>
                <th style='background-color:#F0F3EF;' scope="col">F. entrega</th>
                <th style='background-color:#F0F3EF;' scope="col">Total</th>                
                <th style='background-color:#F0F3EF;' scope="col">Aut.</th>
                <th style='background-color:#F0F3EF;' scope="col">Fact.</th>
                <th style='background-color:#F0F3EF; width: 150px;' scope="col">Tipo servicio</th>
                <th style='background-color:#F0F3EF;' scope="col" colspan="3"></th>                               
               
            </tr>
            </thead>
            <tbody>
            <?php
            $saldo = 0;
            foreach ($model as $val):
                 $saldo += $val->totalorden;
                ?>
            <tr style="font-size: 85%;">                
                <td><?= $val->idordenproduccion ?></td>
                <td><?= $val->cliente->cedulanit ?></td>
                <td><?= $val->cliente->nombrecorto ?></td>
                <td><?= $val->codigoproducto ?></td>
                <td><?= $val->ordenproduccion ?></td>
                <td><?= $val->cantidad ?></td>
                <td><?= $val->duracion ?></td>
                <td><?= $val->sam_operativo?></td>
                <td><?= date("Y-m-d", strtotime("$val->fechallegada")) ?></td>
                <td><?= date("Y-m-d", strtotime("$val->fechaentrega")) ?></td>
                <td align = "right"><?= number_format($val->totalorden,0) ?></td>              
                <td><?= $val->autorizar ?></td>
                <td><?= $val->facturar ?></td>
                <?php if($val->idtipo == 1){?>
                     <td style='background-color:#138D75; color: white;'><?= $val->tipo->tipo ?></td>
                <?php }else{?>
                     <td style='background-color:#D3EBDD; color: black;'><?= $val->tipo->tipo ?></td>
                <?php }?>     
                <td style="width: 25px;">				
                <a href="<?= Url::toRoute(["orden-produccion/viewconsulta", "id" => $val->idordenproduccion]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                
                </td>
            </tr>
            </tbody>
            <?php endforeach;
            if($mostrar_resultado == 1){?>
                <tr>
                    <td colspan="9"></td>
                    <td align="right"><b>Totales</b></td>
                    <td align="right" ><b><?= '$ '.number_format($saldo,0); ?></b></td>
                    <td colspan="4"></td>
                </tr>
            <?php }?>
            
        </table>    
        <div class="panel-footer text-right" >            
            <?php
                $form = ActiveForm::begin([
                            "method" => "post",                            
                        ]);
                ?>    
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary ']); ?>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>







