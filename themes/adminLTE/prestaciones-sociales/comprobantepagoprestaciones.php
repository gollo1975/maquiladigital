<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Empleado;
use app\models\GrupoPago;
use app\models\TipoNomina;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Prestaciones';
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
    "action" => Url::toRoute("prestaciones-sociales/comprobantepagoprestaciones"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$empleado = ArrayHelper::map(Empleado::find()->orderBy('nombrecorto ASC')->all(), 'id_empleado', 'nombrecorto');
$grupo = ArrayHelper::map(GrupoPago::find()->orderBy('grupo_pago ASC')->all(), 'id_grupo_pago', 'grupo_pago');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, 'id_empleado')->widget(Select2::classname(), [
                'data' => $empleado,
                'options' => ['prompt' => 'Seleccione el empleado ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
           
            <?= $formulario->field($form, 'id_grupo_pago')->widget(Select2::classname(), [
                'data' => $grupo,
                'options' => ['prompt' => 'Seleccione el grupo de pago...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>

             <?= $formulario->field($form, 'fecha_inicio_contrato')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?= $formulario->field($form, 'fecha_termino_contrato')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
             <?= $formulario->field($form, "documento")->input("search") ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("prestaciones-sociales/comprobantepagoprestaciones") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
            <tr style='font-size:85%;'align="center" >                
                <th scope="col" style='background-color:#B9D5CE;'>Nro_pago</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Nro_cont.</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Grupo pago</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha final</th>
                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                <th scope="col" style='background-color:#B9D5CE;'>Salario</th>
                <th scope="col" style='background-color:#B9D5CE;'>Devengado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Deducción</th>
                <th scope="col" style='background-color:#B9D5CE;'>Neto pagar</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>                               
            </tr>
            </thead>
            <tbody>
            <?php $prestacion = 0; 
                foreach ($modelo as $val):
                $prestacion += $val->total_pagar
                ?>
                <tr style='font-size:85%;'>                
                    <td><?= $val->nro_pago ?></td>
                    <td><?= $val->id_contrato ?></td>
                    <td><?= $val->documento ?></td>
                    <td><?= $val->empleado->nombrecorto ?></td>
                    <td><?= $val->grupoPago->grupo_pago ?></td>
                    <td><?= $val->fecha_inicio_contrato ?></td>
                    <td><?= $val->fecha_termino_contrato ?></td>
                     <td><?= $val->usuariosistema ?></td>
                    <td align="right"><?= number_format($val->salario,0) ?></td>
                    <td align="right"><?= number_format($val->total_devengado,0) ?></td>
                    <td align="right"><?= number_format($val->total_deduccion,0) ?></td>
                    <td align="right"><?= number_format($val->total_pagar,0) ?></td>
                    <td style= 'width: 40px;'>				
                    <a href="<?= Url::toRoute(["prestaciones-sociales/view", "id" => $val->id_prestacion, 'pagina' =>$pagina]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                
                    </td>
                </tr>
            </tbody>
            <?php endforeach; ?>
            <tr>
                <td colspan="10"></td>
                <td align="right"><b>Valor total</b></td>
                <td align="right" ><b><?= '$ '.number_format($prestacion,0); ?></b></td>
                <td colspan="1"></td>
            </tr>
        </table>    
        <div class="panel-footer text-right" >            
            <?php
                $form = ActiveForm::begin([
                            "method" => "post",                            
                        ]);
                ?>    
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>







