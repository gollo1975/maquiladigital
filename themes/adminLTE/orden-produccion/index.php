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
    "action" => Url::toRoute("orden-produccion/index"),
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
$tipoProducto = ArrayHelper::map(app\models\TipoProducto::find()->all(), 'id_tipo_producto', 'concepto');
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
            <?= $formulario->field($form, 'ordenproduccionint')->widget(Select2::classname(), [
                'data' => $tipoProducto,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, "ordenproduccioncliente")->input("search") ?>
            <?= $formulario->field($form, 'tipo')->dropDownList($tipos, ['prompt' => 'Seleccione un tipo...']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("orden-produccion/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros  <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
            <tr style="font-size: 85%;">                
                <th style='background-color:#F0F3EF;' scope="col">Op</th>
                <th style='background-color:#F0F3EF;' scope="col">Cedula/Nit</th>
                <th style='background-color:#F0F3EF;' scope="col">Cliente</th>
                <th style='background-color:#F0F3EF;' scope="col">Codigo</th>                
                <th style='background-color:#F0F3EF;' scope="col">Op</th>
                <th style='background-color:#F0F3EF;' scope="col">Cant.</th>
                <th style='background-color:#F0F3EF;' scope="col">Time</th>
                 <th style='background-color:#F0F3EF;' scope="col">Sam</th>
                <th style='background-color:#F0F3EF;' scope="col">F. llegada</th>
                <th style='background-color:#F0F3EF;' scope="col">F. entrega</th>
                <th style='background-color:#F0F3EF;' scope="col">Total</th>                
                <th style='background-color:#F0F3EF;' scope="col"><span title="Autorizado el registro">Aut.</span></th>
                <th style='background-color:#F0F3EF;' scope="col"><span title="Facturado la orden">Fac.</span></th>
                <th style='background-color:#F0F3EF;' scope="col"><span title="Lleva o viene de lavanderia">Lav.</span></th>
                <th style='background-color:#F0F3EF; width: 150px;' scope="col">Tipo servicio</th>
                <th style='background-color:#F0F3EF;' scope="col" colspan="3"></th>                               
               
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
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
                <?php if($val->lavanderia == 0){?>
                      <td><?= $val->lavanderiaPrenda ?></td>
                <?php }else{ ?>
                      <td style='background-color:#BCE7E0; color: black;'><?= $val->lavanderiaPrenda ?></td>
                <?php } ?>      
                <td style='background-color:<?= $val->tipo->color?>; color: black;'><?= $val->tipo->tipo ?></td>
               <td style="width: 25px;">				
                     <a href="<?= Url::toRoute(["orden-produccion/view", "id" => $val->idordenproduccion, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                
                </td>
                <td style="width: 25px;">				
                     <a href="<?= Url::toRoute(["orden-produccion/update", "id" => $val->idordenproduccion]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                
                </td>
                <td style='width: 25px;'>
                      <?= Html::a('', ['delete', 'id' => $val->idordenproduccion], [
                        'class' => 'glyphicon glyphicon-trash',
                        'data' => [
                            'confirm' => 'Esta seguro de eliminar el registro?',
                            'method' => 'post',
                        ],
                      ]) ?>
                </td>
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
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary ']); ?>
                <a align="right" href="<?= Url::toRoute("orden-produccion/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>







