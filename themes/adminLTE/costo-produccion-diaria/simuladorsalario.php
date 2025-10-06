<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Arl;
use app\models\Horario;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;


$this->title = 'Simulador de salarios';
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
    "action" => Url::toRoute("costo-produccion-diaria/simuladorsalario"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
$arl = ArrayHelper::map(Arl::find()->orderBy('arl ASC')->all(), 'id_arl', 'arl');
$matriculaObjeto = \app\models\Matriculaempresa::findOne(1);

// Verificar si se encontró el objeto
if ($matriculaObjeto !== null) {
    // Encerrar el objeto en un array
    $datosParaMapa = [$matriculaObjeto];
    
    // Aplicar ArrayHelper::map
    $matricula = ArrayHelper::map($datosParaMapa, 'horas_realmente_trabajadas', 'horas_realmente_trabajadas');
} else {
    // Si no se encuentra, inicializar como array vacío
    $matricula = [];
}
$horario = ArrayHelper::map(Horario::find()->orderBy('horario ASC')->all(), 'id_horario', 'horario');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading">
        Parametros de entrada
    </div>
	
    <div class="panel-body" id="simuladortiempo">
        <div class="row" >
            <?= $formulario->field($form, "salario_basico")->input("search") ?>
            <?= $formulario->field($form, 'arl')->widget(Select2::classname(), [
                'data' => $arl,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'id_horario')->widget(Select2::classname(), [
                'data' => $horario,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, "eficiencia")->input("search") ?>
            <?= $formulario->field($form, "valor_minuto")->input("search") ?>
            <?= $formulario->field($form, "sam")->input("search") ?>
            <?= $formulario->field($form, 'dias_laborados')->dropdownList($matricula, ['prompt' => 'Seleccione...']) ?>			
            <?= $formulario->field($form, "otros_gastos")->input("search")?>
            
       </div>
        <div class="row checkbox checkbox-success" align ="center">
                <?= $formulario->field($form, 'aplica_auxilio')->checkbox(['label' => 'Aplica transporte', '1' =>'small','checked' => 'true', 'class'=>'bs_switch','style'=>'margin-bottom:10px;', 'id'=>'aplica_auxilio']) ?>
            </div>
    </div>    
    <div class="panel-footer text-right">
        <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Generar proceso", ["class" => "btn btn-primary btn-sm",]) ?>
        <a align="right" href="<?= Url::toRoute("costo-produccion-diaria/simuladorsalario") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Limpiar</a>
    </div>
    
</div>
<?php $formulario->end() ?>
<?php
    $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<div>
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#simuladorsalario" aria-controls="simuladorsalario" role="tab" data-toggle="tab">Detalle salario <span class="badge"><?= count($model) ?></span></a></li>
        <li role="presentation" ><a href="#rentabilidadoperario" aria-controls="rentabilidadoperario" role="tab" data-toggle="tab">Rentabilidad <span class="badge"><?= count($model) ?></span></a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="simuladorsalario">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr style ='font-size:90%;'>                
                                    <th scope="col" style='background-color:#B9D5CE;'>Salario</th>
                                    <th scope="col" style='background-color:#B9D5CE;'><span title="Auxilio de transporte" >Transporte</span></th>
                                    <th scope="col" style='background-color:#B9D5CE;'>V. pensión</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>V. caja</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>V. arl</th>
                                    <th scope="col" style='background-color:#B9D5CE;'><span title="Valor de cesanias" >Cesantias</span></th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Primas</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Interes</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Vacaciones</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Ajuste</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Gran total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($model as $val):?>
                                    <tr style='font-size:90%;'>             
                                        <td style="text-align: right"><?= ''.number_format($val->salario,0) ?></td>    
                                        <td style="text-align: right"><?= ''.number_format($val->auxilio_transporte,0) ?></td>
                                        <td style="text-align: right"><?= ''.number_format($val->valor_pension,0) ?></td>
                                        <td style="text-align: right"><?= ''.number_format($val->valor_caja,0)?></td>
                                        <td style="text-align: right"><?= ''.number_format($val->valor_arl,0)?></td> 
                                        <td style="text-align: right"><?= ''.number_format($val->valor_cesantia,0)?></td> 
                                        <td style="text-align: right"><?= ''.number_format($val->valor_prima,0)?></td> 
                                        <td style="text-align: right"><?= ''.number_format($val->valor_interes,0)?></td> 
                                        <td style="text-align: right"><?= ''.number_format($val->valor_vacacion,0)?></td> 
                                        <td style="text-align: right"><?= ''.number_format($val->ajuste_vacacion,0)?></td> 
                                        <td style="text-align: right"><?= ''.number_format($val->total_salarios,0)?></td> 
                                    </tr>    
                                <?php endforeach; ?>
                            </tbody> 
                        </table> 
                    </div>
                </div>    
                
            </div>
        </div>
        <!--- TERMINA TABS-->
        <div role="tabpanel" class="tab-pane" id="rentabilidadoperario">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr style ='font-size:90%;'>                
                                    <th scope="col" style='background-color:#B9D5CE;'>Horario</th>
                                    <th scope="col" style='background-color:#B9D5CE;'><span title="tiempo de la prenda" >Sam prenda</span></th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Valor prenda</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Eficiencia</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Cumplimiento</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Horas laboradas</th>
                                    <th scope="col" style='background-color:#B9D5CE;'><span title="unidades por dia" >Unidades x hora</span></th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Unidades x mes</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Valor venta</th>
                                     <th scope="col" style='background-color:#B9D5CE;'>Rentabilidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  $calculo = 0; $porcentaje = 0;
                                foreach ($model as $val):
                                    $calculo = (($val->valor_venta * 100)/$val->total_salarios) -100; 
                                    $porcentaje = (($val->total_salarios * $val->eficiencia )/$val->valor_venta); 
                                    ?>
                                    <tr style='font-size:90%;'>             
                                        <td><?= $val->horario->horario ?> - (<?= $val->horario->total_horas ?> horas)</td>    
                                        <td style="text-align: right"><?= ''.number_format($val->sam_prenda,0) ?></td>
                                        <td style="text-align: right"><?= ''.number_format($val->valor_prenda,0) ?></td>
                                        <td style="text-align: right"><?= $val->eficiencia?> %</td>
                                        <td style="text-align: right; background-color:#E7EA8E;" ><?= ''.number_format($porcentaje,2)?> %</td>
                                        <td style="text-align: right"><?= $val->dias_laborados?></td> 
                                        <td style="text-align: right"><?= ''.number_format($val->unidades_dia,2)?></td> 
                                        <td style="text-align: right"><?= ''.number_format($val->unidades_mes,0)?></td> 
                                        <td style="text-align: right"><?= ''.number_format($val->valor_venta,0)?></td> 
                                        <?php if ($calculo > 0){?>
                                        <td style="text-align: right; color: #080B7E"><b><?= ''.number_format($calculo ,1)?>%</b></td>
                                        <?php }else{?>
                                             <td style="text-align: right; color: #EF1522"><?= ''.number_format($calculo ,1)?>%</td>
                                        <?php } ?>     
                                    </tr>    
                                <?php endforeach; ?>
                            </tbody> 
                        </table> 
                    </div>
                </div>    
            </div>
        </div>
       <!--- TERMINA TABS--> 
    </div>
</div>
   
<?php $formulario->end() ?>



