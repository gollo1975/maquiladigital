<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Horario;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;


$this->title = 'Simulador de tiempo';
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
    "action" => Url::toRoute("costo-produccion-diaria/simuladortiempo"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$horario = ArrayHelper::map(Horario::find()->orderBy('horario ASC')->all(), 'id_horario', 'horario');
$cliente = ArrayHelper::map(app\models\Cliente::find()->where(['=','proceso', 1])->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading">
        Parametros de entrada
    </div>
	
    <div class="panel-body" id="simuladortiempo">
        <div class="row" >
           
            <?= $formulario->field($form, 'id_cliente')->widget(Select2::classname(), [
                'data' => $cliente,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'horario_trabajo')->widget(Select2::classname(), [
                'data' => $horario,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, "cantidad_operarios")->input("search") ?>
            <?= $formulario->field($form, "unidades")->input("search") ?>
            <?= $formulario->field($form, "eficiencia")->input("search") ?>
            <?= $formulario->field($form, "tiempo_confeccion")->input("search") ?>
            <?= $formulario->field($form, 'vinculado')->dropDownList(['1' => 'SI', '2' => 'NO'],['prompt' =>'Seleccione...','onchange' => 'mostrarcampo()', 'id' => 'vinculado']) ?>
            <div id="vlr_minuto_contrato" style="display:none"><?= $formulario->field($form, "vlr_minuto_contrato")->input("search") ?></div>
            <div id="salario" style="display:block"> <?= $formulario->field($form, "salario")->input("search") ?></div>
             <?=  $formulario->field($form, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
       </div>
    </div>    
    <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Generar proceso", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("costo-produccion-diaria/simuladortiempo") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Limpiar</a>
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
        Registros..
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>                
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Numero de operarios" ># Oper.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'>Horario</th>
                <th scope="col" style='background-color:#B9D5CE;'>Salario</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Eficiencia de trabajo" >% Efi.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Cantidad unidad lote" >Cant.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'>Sam</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Unidades por dia" >Uni. x dia</span></th>
                <th scope="col" style='background-color:#B9D5CE;'>F. inicio</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. final</th>
                <th scope="col" style='background-color:#B9D5CE;'>Dias</th>
                <th scope="col" style='background-color:#B9D5CE;'>Venta</th>
                <th scope="col" style='background-color:#B9D5CE;'>Costo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Utilidad</th>
                <th scope="col" style='background-color:#B9D5CE;'>%</th>
              
            </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($model as $val):?>
                    <tr style='font-size:85%;'>             
                        <td><?= $val->cliente->nombrecorto ?></td>    
                        <td style="text-align: right"><?= $val->cantidad_operarios ?></td>
                        <td><?= $val->horario->horario ?></td>
                         <td style="text-align: right"><?= ''.number_format($val->salario,0)?></td>
                        <td style="text-align: right"><?= $val->eficiencia?>%</td>
                        <td style="text-align: right"><?= ''.number_format($val->unidades_lote,0) ?></td>
                        <td style="text-align: right"><?= $val->sam_prenda ?></td>
                        <td style="text-align: right"><?= $val->unidades_por_dia ?></td>
                        <td><?= $val->fecha_inicio ?></td>
                        <td><?= $val->fecha_final ?></td>
                        <td style="text-align: right"><?= $val->dias_reales?></td>
                        <td style="text-align: right"><?= ''.number_format($val->valor_lote,0)?></td>
                        <td style="text-align: right"><?= ''.number_format($val->valor_costo_lote,0)?></td>
                        <td style="text-align: right"><?= ''.number_format($val->utilidad_lote, 0)?></td>
                        <td style="text-align: right"><?= ''.number_format((($val->utilidad_lote / $val->valor_lote)*100), 2)?></td>

                    </tr>    
                <?php endforeach; ?>
            </tbody> 
        </table>    
        <div class="panel-footer text-right" >    
            <?= Html::a('<span class="glyphicon glyphicon-export"></span> Exportar excel', ['excelsimulacion', 'id' => $val->id_simulador], ['class' => 'btn btn-primary btn-sm']);?>
          
        </div>
    </div>
</div>
   
<?php $formulario->end() ?>
<script type="text/javascript">
    function mostrarcampo(){
        vinculado = document.getElementById('vinculado').value;
        if(vinculado == '1'){
           salario.style.display = "block";
           vlr_minuto_contrato.style.display = "none";
        } else {
            salario.style.display = "none";
            vlr_minuto_contrato.style.display = "block";
        }
    }
</script>    


