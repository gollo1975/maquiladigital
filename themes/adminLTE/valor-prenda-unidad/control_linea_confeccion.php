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

$this->title = 'Lineas de confeccion';
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
    "action" => Url::toRoute("valor-prenda-unidad/control_linea_confeccion"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$conOpeario = ArrayHelper::map(\app\models\Operarios::find()->orderBy('nombrecompleto ASC')->all(), 'id_operario', 'nombrecompleto');

?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
            <?= $formulario->field($form, 'operario')->widget(Select2::classname(), [
                'data' => $conOpeario,
                'options' => ['prompt' => 'Seleccione el operario...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>   
         <div class="row" >  
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
            
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/control_linea_confeccion") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
    <?php if($model){
        $contar = count($model);
    }else{
        $contar = 0;
    }?>
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" active = "panel"><a href="#detalle_lineas" aria-controls="detalle_linea" role="tab" data-toggle="tab">Detalle de lineas <span class="badge"><?= $contar ?></span></a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="detalle_lineas">
             <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr style="font-size: 85%;">                
                                    <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Op cliente</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Fecha de trabajo</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Eficiencia</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Hora inicio</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Hora corte</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Tipo de linea</th>
                                    <th scope="col" style='background-color:#B9D5CE;'></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if($model){
                                    foreach ($model as $lineas) {?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $lineas->consecutivo?></td>
                                            <td><?= $lineas->idordenproduccion?></td>
                                            <td><?= $lineas->ordenproduccion->codigoproducto?></td>
                                            <td><?= $lineas->dia_pago?></td>
                                            <td><?= $lineas->cantidad?></td>
                                            <td><?= $lineas->porcentaje_cumplimiento?> %</td>
                                            <td><?= $lineas->hora_inicio?></td>
                                            <td><?= $lineas->hora_corte?></td>
                                            <td><?= $lineas->hora_descontar?></td>
                                            <td style ="width: 20px; height: 20px">
                                              <!-- Inicio Nuevo Detalle proceso -->
                                                <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                                    ['/valor-prenda-unidad/editar_linea_confeccion', 'id_detalle' => $lineas->consecutivo],
                                                    [
                                                        'title' => 'Editar lineas de confeccion',
                                                        'data-toggle'=>'modal',
                                                        'data-target'=>'#modaleditarlineasconfeccion',
                                                        'class' => '',
                                                         'data-backdrop' => 'static',
                                                    ])    
                                               ?>
                                            </div> 
                                            <div class="modal remote fade" id="modaleditarlineasconfeccion">
                                                <div class="modal-dialog modal-lg" style ="width: 500px;">
                                                    <div class="modal-content"></div>
                                                </div>
                                            </div>
                                        </td>    
                                        </tr>
                                    <?php }
                                }    ?>
                            </tbody>
                        </table> 
                    </div>
                  
                </div>
             </div>     
        </div>
        
        <!--TERMINA EL TABS-->
    </div>   
     <?php ActiveForm::end(); ?>
</div>





