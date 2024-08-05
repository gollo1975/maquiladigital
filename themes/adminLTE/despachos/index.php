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
use app\models\TipoEntrada;
use app\models\Municipio;


$this->title = 'Despachos/Fletes';
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
    "action" => Url::toRoute("despachos/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$conProveedor = ArrayHelper::map(app\models\Proveedor::find()->orderBy ('nombrecorto ASC')->all(), 'idproveedor', 'nombrecorto');
$conSalida = \app\models\SalidaEntradaProduccion::find()->where(['=','id_entrada_tipo', 8])
                                                        ->orWhere(['=','id_entrada_tipo', 9])
                                                        ->orWhere(['=','id_entrada_tipo', 10])
                                                         ->orWhere(['=','id_entrada_tipo', 11])->orderBy('id_salida DESC')->all();
$conSalida = ArrayHelper::map($conSalida, 'id_salida','nombreReferencia');

?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
             <?= $formulario->field($form, 'proveedor')->widget(Select2::classname(), [
                'data' => $conProveedor,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'salida')->widget(Select2::classname(), [
                'data' => $conSalida,
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
            <?= $formulario->field($form, "referencia")->input("search") ?>
           
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("despachos/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <tr >         
                 <th scope="col" style='background-color:#B9D5CE;'>Numero</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proveedor</th>
                <th scope="col" style='background-color:#B9D5CE;'>Tipo traslado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Origen</th>
                <th scope="col" style='background-color:#B9D5CE;'>Destino</th>
                <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                <th scope="col" style='background-color:#B9D5CE;'>Nro salida</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. despacho</th>
                <th scope="col" style='background-color:#B9D5CE;'>T. tulas</th>
                <th scope="col" style='background-color:#B9D5CE;'>T. reales</th>
                <th scope="col" style='background-color:#B9D5CE;'>Valor flete</th>                
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th score="col" style='background-color:#B9D5CE;'></th>                              
            </tr>
            </thead>
            <tbody>
            <?php foreach ($modelo as $val):
                ?>
                <tr style ='font-size: 85%;'>                
                    <td><?= $val->numero_despacho ?></td>
                    <td><?= $val->nombre_proveedor ?></td>
                    <td><?= $val->tipoEntrada->concepto ?></td>
                    <td><?= $val->municipio_origen ?></td>
                    <td><?= $val->municipio_destino ?></td>
                    <td><?= $val->codigo_producto ?></td>
                    <td><?= $val->id_salida ?></td>
                    <td><?= $val->fecha_despacho ?></td>
                    <?php if($val->total_tulas == $val->tulas_reales){?>
                        <td style="text-align: right"><?= $val->total_tulas ?></td>
                        <td style="text-align: right"><?= $val->tulas_reales ?></td>
                    <?php } else { ?>
                        <td style="text-align: right; background-color: #ffe5ec"><?= $val->total_tulas ?></td>
                        <td style="text-align: right; background-color: #ffe5ec"><?= $val->tulas_reales ?></td>
                    <?php } ?>    
                    <td style="text-align: right"><?= ''. number_format($val->valor_flete,0) ?></td>
                    <td style= 'width: 25px;'>
                        <a href="<?= Url::toRoute(["despachos/view", "id" => $val->id_despacho]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                    </td>
                    <?php if($val->autorizado == 0){?>
                        <td style= 'width: 25px; height: 25px'>
                            <a href="<?= Url::toRoute(["despachos/update", "id" => $val->id_despacho]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                        </td>
                    <?php }else{?>
                        <td style= 'width: 25px; height: 25px'></td>
                    <?php }?>    
                        
                </tr>            
            <?php endforeach; ?>
            </tbody>    
        </table> 
        <div class="panel-footer text-right" >            
           <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
            <a align="right" href="<?= Url::toRoute("despachos/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
        <?php $form->end() ?>
        </div>
     </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

