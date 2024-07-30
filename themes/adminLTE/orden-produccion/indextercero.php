<?php

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

// modelos

use app\models\Proveedor;
use app\models\Cliente;
use app\models\Ordenproducciontipo;

$this->title = 'Ordenes (Tercero)';
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
    "action" => Url::toRoute("orden-produccion/indextercero"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$listar_provedor = ArrayHelper::map(Proveedor::find()->where(['=','genera_moda', 1])->orderBy('nombrecorto ASC')->all(), 'idproveedor', 'nombrecorto');
$listar_clientes = ArrayHelper::map(Cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto');
$listar_proceso = ArrayHelper::map(Ordenproducciontipo::find()->orderBy('tipo ASC')->all(), 'idtipo', 'tipo');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "idordenproduccion")->input("search") ?>
             <?= $formulario->field($form, 'idproveedor')->widget(Select2::classname(), [
                'data' => $listar_provedor,
                'options' => ['prompt' => 'Seleccione el proveedor...'],
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
             <?= $formulario->field($form, 'idcliente')->widget(Select2::classname(), [
                'data' => $listar_clientes,
                'options' => ['prompt' => 'Seleccione el cliente...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'idtipo')->widget(Select2::classname(), [
                'data' => $listar_proceso,
                'options' => ['prompt' => 'Seleccione el proceso...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("orden-produccion/indextercero") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
        Registros: <span class="badge"> <?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr >                
                <th scope="col" style='background-color:#B9D5CE;'>Orden</th>
                <th scope="col" style='background-color:#B9D5CE;'>Tercero</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>OP cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proceso</th>
                <th scope="col" style='background-color:#B9D5CE;'>Vr. Minuto</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cant. Minutos</th>
                <th scope="col" style='background-color:#B9D5CE;'>T. Pagar</th>
                 <th scope="col" style='background-color:#B9D5CE;'>F. Proceso</th>
                 <th scope="col" style='background-color:#B9D5CE;'><span title="Autorizado">Aut.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
              
            </tr>
            </thead>
            <tbody>
            <?php 
             
            foreach ($modelo as $val):?>
                <tr style='font-size:85%;'>                
                <td><?= $val->id_orden_tercero ?></td>
                 <td><?= $val->proveedor->nombrecorto ?></td>
                <td><?= $val->cliente->nombrecorto?></td>
                <td><?= $val->idordenproduccion ?></td>
                 <td><?= $val->codigo_producto ?></td>
                <td style='background-color:<?= $val->tipo->color?>'><?= $val->tipo->tipo ?></td>
                <td><?= ''.number_format($val->vlr_minuto,0) ?></td>
                <td><?= $val->cantidad_minutos ?></td>
                <td><?= ''.number_format($val->total_pagar,0) ?></td>
                <td><?= $val->fecha_proceso ?></td>
                  <td><?= $val->autorizadotercero ?></td>  
                <td style= 'width: 25px; height: 25px;'>
                        <a href="<?= Url::toRoute(["orden-produccion/viewtercero", "id" => $val->id_orden_tercero]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                </td>
                <td style= 'width: 25px; height: 25px;'>
                        <a href="<?= Url::toRoute(["orden-produccion/editarordentercero", "id" => $val->id_orden_tercero, ]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
             
            </tbody>            
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <a align="right" href="<?= Url::toRoute("orden-produccion/nuevaordentercero") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>


