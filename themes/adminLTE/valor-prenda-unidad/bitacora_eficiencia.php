<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\db\Query;
use yii\db\Command;
use yii\db\ActiveQuery;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Resumen (Bitacora)';
$this->params['breadcrumbs'][] = $this->title;

?>
  
    <script language="JavaScript">
        function mostrarfiltro() {
            divC = document.getElementById("filtro");
            if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
        }
    </script>
    <?php Pjax::begin(['id' => 'filtro-bitacora', 'timeout' => false]); ?>

    <?php $formulario = ActiveForm::begin([
        "method" => "get",
        "action" => Url::toRoute("valor-prenda-unidad/bitacora_eficiencia"),
        "options" => ['data-pjax' => true, 'class' => 'form-horizontal'],
        'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],
    ]); 

    $ordenProduccion = ArrayHelper::map(\app\models\Ordenproduccion::find()->all(), 'idordenproduccion', 'OrdenProduccion');
    $conOperario = ArrayHelper::map(\app\models\Operarios::find()->orderBy('nombrecompleto asc')->all(), 'id_operario', 'nombrecompleto');
    ?>
    <div class="panel panel-success panel-filters">
        <div class="panel-heading" style="cursor:pointer" onclick="mostrarfiltro()">
            Filtros de búsqueda <i class="glyphicon glyphicon-filter"></i>
        </div>
           

        <div class="panel-body" id="filtro" style="display:block">
            <div class="row" >
               <?= $formulario->field($form, 'orden_produccion')->widget(Select2::classname(), [
                    'data' => $ordenProduccion,
                    'options' => ['prompt' => 'Seleccione la orden'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>

                <?= $formulario->field($form, 'operario')->widget(Select2::classname(), [
                    'data' => $conOperario,
                    'options' => ['prompt' => 'Seleccione el operario'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>

                <?= $formulario->field($form, 'desde')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                    'value' => date('d-M-Y', strtotime('+2 days')),
                    'options' => ['placeholder' => 'Seleccione una fecha ...'],
                    'pluginOptions' => [
                        'format' => 'yyyy-m-d',
                        'todayHighlight' => true,
                        'orientation' => 'bottom']])
                ?>
                <?= $formulario->field($form, 'hasta')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                    'value' => date('d-M-Y', strtotime('+2 days')),
                    'options' => ['placeholder' => 'Seleccione una fecha ...'],
                    'pluginOptions' => [
                        'format' => 'yyyy-m-d',
                        'todayHighlight' => true,
                        'orientation' => 'bottom']])
                ?>
                <?= $formulario->field($form, 'inicio_hora_corte')->input ('time'); ?>
                <?= $formulario->field($form, 'final_hora_corte')->input ('time'); ?>

                <?php if(!empty($conOperaciones)){ ?>
                    <?= $formulario->field($form, 'operacion')->widget(Select2::classname(), [
                        'data' => $conOperaciones,
                        'options' => ['prompt' => 'Seleccione la operación'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);  
                }?>
            </div>

            <div class="panel-footer text-right">
                <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
                <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/bitacora_eficiencia") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
            </div>
        </div>
    </div>

   <?php $formulario->end() ?>
    
    
    <!-- inicia nuevo formulario-->
   
    <div class="table-responsive">
        <div class="panel panel-success ">
            <div class="panel-heading">
                Registros <span class="badge"><?= isset($model) ? count($model) : 0 ?></span>
            </div>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr style ='font-size:85%;'>                
                        <th scope="col" style='background-color:#B9D5CE;'>Op</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Operacion</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Fecha confección</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Hora de corte</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Sam</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Sam final</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Eficiencia</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Nota</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($model)): ?>
                        <?php foreach ($model as $val):
                            $ComSam = app\models\FlujoOperaciones::find()->where([
                                'idproceso' => $val->idproceso,
                                'idordenproduccion' => $val->idordenproduccion])->one();
                               
                            ?>
                            <tr style ='font-size:85%;'>
                                <td><?= Html::encode($val->idordenproduccion) ?></td>
                                <td><?= Html::encode($val->ordenproduccion->codigoproducto) ?></td>
                                <td><?= Html::encode($val->detalleorden->productodetalle->prendatipo->talla->talla) ?></td>
                                <td><?= Html::encode($val->operario->nombrecompleto) ?></td>
                                <td><?= Html::encode($val->proceso->proceso) ?></td>
                               <td><?= Yii::$app->formatter->asDate($val->fecha_confeccion, 'php:Y-m-d') ?></td>
                                <td><?= Html::encode($val->hora_corte) ?></td>
                                <td style="text-align: right"><?= $ComSam->minutos ?></td>
                                <td style="text-align: right"><?= $val->tiempo_real_confeccion ?></td>
                                <?php if($val->porcentaje_eficiencia > 0){?>
                                    <td style="text-align: right"><?= $val->porcentaje_eficiencia ?></td>
                                <?php }else{?>
                                    <td style="text-align: right; color: red"><?= $val->porcentaje_eficiencia ?></td>
                                <?php }?>    
                                 <td><?= Html::encode($val->concepto) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center">No se encontraron resultados para mostrar.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table> 
            <div class="panel-footer text-right" >            
            <?php if (!empty($model)){ ?>
                <?php
                    $form = ActiveForm::begin([
                                "method" => "post",                            
                            ]);
                    ?>
                 <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar a excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <?php $form->end() ?>
            <?php } ?>    
        </div>
        </div>
         
    </div>    

<?php Pjax::end(); ?>
   
<script>
    function mostrarfiltro() {
        var x = document.getElementById("filtro");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }
</script>