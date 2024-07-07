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

$this->title = 'Pago x servicios';
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
    "action" => Url::toRoute("valor-prenda-unidad/searchpageprenda"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
$plantaBodega= ArrayHelper::map(\app\models\PlantaEmpresa::find()->all(), 'id_planta', 'nombre_planta');
$operario= ArrayHelper::map(\app\models\Operarios::find()->orderBy('nombrecompleto asc')->all(), 'id_operario', 'nombrecompleto');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "documento")->input("search") ?>
             <?= $formulario->field($form, 'id_operario')->widget(Select2::classname(), [
                'data' => $operario,
                'options' => ['prompt' => 'Seleccione el operario'],
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
            <?php if($tokenPlanta == null){?>
              <?= $formulario->field($form, 'planta')->widget(Select2::classname(), [
                'data' => $plantaBodega,
                'options' => ['prompt' => 'Seleccione la planta...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?php }?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/searchpageprenda") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                <th scope="col" style='background-color:#B9D5CE;'>Desde</th>
                <th scope="col" style='background-color:#B9D5CE;'>Hasta</th>
                <th scope="col" style='background-color:#B9D5CE;'>No dias</th>
                <th scope="col" style='background-color:#B9D5CE;'>Devengado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Deducción</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total pagar</th>
                <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                <th scope="col" style='background-color:#B9D5CE;'>Observacion</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($modelo as $val):?>
                    <tr style='font-size:85%;'>  
                        <td><?= $val->id_pago ?></td>
                        <td><?= $val->documento ?></td>
                        <td><?= $val->operario ?></td>
                        <td><?= $val->fecha_inicio?></td>
                        <td><?= $val->fecha_corte ?></td>
                        <td><?= $val->total_dias ?></td>
                        <td align="right"><?= ''.number_format($val->devengado,0) ?></td>
                        <td align="right"><?= ''.number_format($val->deduccion,0) ?></td>
                        <td align="right"><?= ''.number_format($val->total_pagar,0) ?></td>
                        <td><?= $val->planta->nombre_planta ?></td>
                        <td><?= $val->observacion?></td>
                        <td style=' width: 25px;'>
                            <a href="<?= Url::toRoute(["valor-prenda-unidad/consultadetallepago", 'id_pago' => $val->id_pago, 'fecha_inicio' =>$val->fecha_inicio, 'fecha_corte' => $val->fecha_corte, 'autorizado' => $val->autorizado, 'bodega' => $val->id_planta]) ?>" ><span class="glyphicon glyphicon-eye-open "></span></a>
                        </td>
                        <td style="width: 25px;">				
                            <a href="<?= Url::toRoute(["imprimircolillaconfeccion",'id_pago' => $val->id_pago, 'fecha_inicio' =>$val->fecha_inicio, 'fecha_corte' => $val->fecha_corte]) ?>" ><span class="glyphicon glyphicon-print" title="Imprimir "></span></a>
                        </td>
                    </tr>    
                <?php endforeach; ?>
          </tbody>                  
        </table>    
        <div class="panel-footer text-right" >   
            <?= Html::submitButton("<span class='glyphicon glyphicon-download-alt'></span> Exportar pagos", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
       </div>
    </div>
</div>
<?php $form->end() ?>
<?= LinkPager::widget(['pagination' => $pagination]) ?>
