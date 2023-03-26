<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\models\Cliente;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Entrada/Salida';
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
    "action" => Url::toRoute("orden-produccion/indexentradasalida"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$cliente = ArrayHelper::map(cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto');
$entrada = ArrayHelper::map(app\models\TipoEntrada::find()->orderBy('concepto ASC')->all(), 'id_entrada_tipo', 'concepto');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "idordenproduccion")->input("search") ?>
            <?= $formulario->field($form, "codigo_producto")->input("search") ?>
            <?= $formulario->field($form, 'fecha_desde')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
               <?= $formulario->field($form, 'fecha_hasta')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?= $formulario->field($form, 'idcliente')->widget(Select2::classname(), [
                'data' => $cliente,
                'options' => ['prompt' => 'Seleccione el cliente'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'tipo_proceso')->dropDownList(['' => 'TODOS', '1' => 'ENTRADA', '2' => 'SALIDA'],['prompt' => 'Seleccione el procesp ...']) ?>
        <?= $formulario->field($form, 'id_entrada_tipo')->widget(Select2::classname(), [
                'data' => $entrada,
                'options' => ['prompt' => 'Seleccione el cliente'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("orden-produccion/indexentradasalida") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <tr style ='font-size:85%;'>                
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>OP Int.</th>
                <th scope="col" style='background-color:#B9D5CE;'>OP Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proceso</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Tipo entrada</th>
                <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                <th scope="col" style='background-color:#B9D5CE;'>Entrada/Salida</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Tulas</th>
                 <th scope="col" style='background-color:#B9D5CE; width: 300px;' >Observación</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
              
            </tr>
            </thead>
            <tbody>
            <?php 
            foreach ($modelo as $val):?>
                <tr style='font-size:85%;'>  
                    <td><?= $val->id_salida?></td>
                    <td><?= $val->idordenproduccion ?></td>
                      <td><?= $val->ordenproduccion->ordenproduccion ?></td>
                    <td><?= $val->cliente->nombrecorto ?></td>
                    <td><?= $val->tipoProceso?></td>
                     <td><?= $val->tipoentrada->concepto?></td>
                     <td><?= $val->codigo_producto?></td>
                    <td align="right"><?= ''.number_format($val->total_cantidad,0) ?></td>
                    <td><?= $val->fecha_entrada_salida?></td>
                     <td><?= $val->numero_tulas?></td>
                    <td><?= $val->observacion?></td>
                
                    <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["orden-produccion/viewsalida", "id" => $val->id_salida ]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                    </td>
                    <td style= 'width: 25px; height: 25px;'>
                        <?php if($val->autorizado == 0){?>
                            <a href="<?= Url::toRoute(["orden-produccion/updatesalida", "id" => $val->id_salida ]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                        <?php }?>    
                    </td>
                </tr>
                    
            <?php endforeach; ?>
             </tbody>           
        </table>    
    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <a align="right" href="<?= Url::toRoute("orden-produccion/createsalida") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>
