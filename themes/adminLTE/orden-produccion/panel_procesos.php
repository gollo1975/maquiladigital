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

$this->title = 'Capacidad instalada';
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
    "action" => Url::toRoute("orden-produccion/panel_procesos"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$horario = ArrayHelper::map(\app\models\Horario::find()->all(), 'id_horario', 'horario');
$tipoServicio = ArrayHelper::map(Ordenproducciontipo::find()->all(), 'idtipo', 'tipo');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
            <?= $formulario->field($form, 'horario')->widget(Select2::classname(), [
                'data' => $horario,
                'options' => ['prompt' => 'Seleccione un horario ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
          
            <?= $formulario->field($form, 'tipo_servicio')->widget(Select2::classname(), [
                'data' => $tipoServicio,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("orden-produccion/panel_procesos") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>
 <?php $form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
            ]);?>
        <!--TERMINA TABS-->
 <div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#panel_proceso" aria-controls="panel_proceso" role="tab" data-toggle="tab">Capacidad instalada <span class="badge"><?= 1 ?></span></a></li>
        <li role="presentation"><a href="#programacion" aria-controls="programacion" role="tab" data-toggle="tab">Programacion de entrega <span class="badge"><?= count($programacionOrdenes) ?></span></a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="panel_proceso">
             <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr style="font-size: 85%;">                
                                <th scope="col" style='background-color:#B9D5CE;'>Horas x Operario</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Minutos planta diario</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Minutos de carga</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Dias cargado de trabajo</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Nota</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php $dias_carga = 0;?>
                                <tr style="font-size: 85%;">  
                                    <?php
                                     if($capacidad != '' && $totalMinutosCarga != '' && $minutos != '' && $totalMinutosCarga){
                                          $dias_carga = $totalMinutosCarga / $capacidad ;
                                     }?>

                                    <td><?= $minutos ?> (Minutos)</td>
                                    <td><?= ''.number_format($capacidad,0) ?> (Minutos)</td>
                                    <td><?= ''.number_format($totalMinutosCarga,0) ?> (Minutos)</td>
                                    <td><?= ''.number_format($dias_carga ,2) ?> (Dias)</td>
                                    <?php
                                    if($dias_carga < 3){?>
                                        <td><span style="color: red;"><?= 'Esta planta esta proxima a quedarse sin trabajo.' ?></span></td>
                                    <?php }else{
                                        if($dias_carga > 2 && $dias_carga < 9){?>
                                             <td><span style="color: blue;"><?= 'Esta planta esta bien de trabajo.' ?></span></td>
                                        <?php }else{?>
                                            <td><span style="color: magenta;"><?= 'Esta planta esta cargada con mas de 9 DIAS.' ?></span></td> 
                                        <?php }
                                    }?>  
                                </tr>
                            </tbody>
                        </table> 
                    </div>
                </div>
             </div>     
        </div>
        
        <div role="tabpanel" class="tab-pane" id="programacion">
             <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr style="font-size: 85%;">                
                                    <th scope="col" style='background-color:#B9D5CE;'>Op interna</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Op cliente</th>
                                     <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>F. ingreso</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>F. entrega</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Nueva fecha</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Tipo proceso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($programacionOrdenes as $key => $fechas) {?>
                                    <tr style="font-size: 85%;">
                                        <td><?= $fechas->idordenproduccion?></td>
                                        <td><?= $fechas->ordenproduccion?></td>
                                        <td><?= $fechas->codigoproducto?></td>
                                        <td><?= $fechas->cliente->nombrecorto?></td>
                                        <td><?= $fechas->cantidad?></td>
                                        <td><?= $fechas->fechallegada?></td>
                                        <td style='background-color:#BCE7E0;'><?= $fechas->fechaentrega?></td>
                                        <td style="padding-center: 1;padding-right: 1;"><input type="date" name="nueva_fecha[]" value="" size="16"></td>
                                        <td><?= $fechas->tipo->tipo?></td>
                                        <input type="hidden" name="listado_programacion[]" value="<?= $fechas->idordenproduccion ?>">

                                    </tr>
                                <?php }?>
                            </tbody>
                        </table> 
                    </div>
                    <div class="panel-footer text-right" >            
                            <?= Html::submitButton("<span class='glyphicon glyphicon-plus'></span> Crear nueva fecha", ['name' => 'crear_nueva_fecha','class' => 'btn btn-warning btn-sm ']); ?>                
                    </div>
                </div>
             </div>     
        </div>
        
        <!--TERMINA EL TABS-->
    </div>   
     <?php ActiveForm::end(); ?>
</div>





