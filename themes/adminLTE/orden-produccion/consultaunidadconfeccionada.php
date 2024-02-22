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

/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Unidades confeccionadas';
$this->params['breadcrumbs'][] = $this->title;
?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtro");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("orden-produccion/consultaunidadconfeccionada"),
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
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "idordenproduccion")->input("search") ?>
              <?= $formulario->field($form, "id_balanceo")->input("search") ?>
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
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("orden-produccion/consultaunidadconfeccionada") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
        Registros <span class="badge"> <?= number_format($pagination->totalCount,0) ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>                
                <th scope="col" style='background-color:#B9D5CE;'>Nro balanceo</th>
                <th scope="col" style='background-color:#B9D5CE;'>No_Operarios</th>
                <th scope="col" style='background-color:#B9D5CE;'>OP</th>
                <th scope="col" style='background-color:#B9D5CE;'>Ref.</th>
                <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                <th scope="col" style='background-color:#B9D5CE;'>Facturado</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. confeccion</th>
                <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
              
            </tr>
            </thead>
            <tbody>
                <?php 
                $unidades = 0;
                $facturado = 0;
                foreach ($modelo as $val):?>
                    <tr style='font-size:85%;'>  
                    
                        <td><?= $val->id_balanceo ?></td>
                        <td><?= $val->nro_operarios ?></td>
                        <td><?= $val->idordenproduccion ?></td>
                        <td><?= $val->ordenproduccion->codigoproducto ?></td>
                        <td><?= $val->detalleorden->productodetalle->prendatipo->talla->talla?></td>
                        <td><?= $val->ordenproduccion->cliente->nombrecorto ?></td>
                        <td align="right"><?= ''.number_format($val->cantidad_terminada,0)?></td>
                        <td align="right"><?= ''. number_format($val->detalleorden->vlrprecio * $val->cantidad_terminada,0) ?></td>
                        <td><?= $val->fecha_entrada ?></td>
                        <td><?= $val->detalleorden->plantaProduccion->nombre_planta ?></td>

                <?php
                $unidades +=  $val->cantidad_terminada;
                $facturado += $val->detalleorden->vlrprecio * $val->cantidad_terminada;
                endforeach; ?>
            </tbody>           
            <td colspan="4"><td></td><td ></td> <td style="font-size: 85%; width: 110px; text-align: left; background: #4B6C67; color: #FFFFFF;"><b>Total:</b> <?= ''.number_format($unidades,0) ?></td><td style="font-size: 85%; width: 160px; text-align: left; background: #4B6C67; color: #FFFFFF;"><b>Facturado:</b> <?= ''.number_format($facturado,0) ?></td> <td colspan="1">
        </table>    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

