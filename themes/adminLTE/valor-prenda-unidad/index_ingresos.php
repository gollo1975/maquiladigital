<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Ordenproducciontipo;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ingresos operativos';
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
    "action" => Url::toRoute("valor-prenda-unidad/costo_gasto_operario"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$conOperacion = ArrayHelper::map(\app\models\ProcesoProduccion::find()->orderBy('proceso ASC')->all(), 'idproceso', 'proceso');
$planta = ArrayHelper::map(app\models\PlantaEmpresa::find()->all(), 'id_planta', 'nombre_planta');
$conOrden = ArrayHelper::map(app\models\Ordenproduccion::find()->orderBy('idordenproduccion DESC')->all(), 'idordenproduccion', 'ordenProduccion');
$conOperarios = ArrayHelper::map(\app\models\Operarios::find()->orderBy('nombrecompleto ASC')->all(), 'id_operario', 'nombrecompleto');

?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, 'idordenproduccion')->widget(Select2::classname(), [
                'data' => $conOrden,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'operario')->widget(Select2::classname(), [
                'data' => $conOperarios,
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
            <?= $formulario->field($form, 'operacion')->widget(Select2::classname(), [
                'data' => $conOperacion,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'planta')->widget(Select2::classname(), [
                'data' => $planta,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>º
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/costo_gasto_operario") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
        <?php if($tableexcel){?>
            Registros: <span class="badge"> <?= count($tableexcel) ?></span>
        <?php }?>    
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>        
                    <th scope="col" style='background-color:#B9D5CE;'>F.confeccion</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                    <th scope="col" style='background-color:#B9D5CE;'>OP</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Operacion</th>
                     <th scope="col" style='background-color:#B9D5CE;'>Cant.</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Sam</th>
                    <th scope="col" style='background-color:#B9D5CE;'>%</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Ingreso</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Costos</th>
                  
                </tr>
            </thead>
            <tbody>
                <?php 
                 $costo = 0;
                 $valor = 0; $ingreso = 0; $total = 0; $total2 = 0; 
                if($modelo){
                    $empresa = app\models\Matriculaempresa::findOne(1);
                    foreach ($modelo as $val):
                        if($val->operarioProduccion->vinculado == 0){ // personal al contrato
                                $valor = $val->vlr_prenda / $empresa->vlr_minuto_contrato;
                        }else{
                                $valor = $val->vlr_prenda / $empresa->vlr_minuto_vinculado;
                                 $costo = $val->costo_dia_operaria;
                        }?>
                        <tr style='font-size:85%;'>  
                            <td><?= $val->dia_pago ?></td>
                            <td><?= $val->operarioProduccion->nombrecompleto ?></td>
                            <td><?= $val->idordenproduccion ?></td>
                            <td><?= $val->ordenproduccion->cliente->nombrecorto ?></td>
                            <td><?= $val->ordenproduccion->codigoproducto ?></td>
                            <?php if($val->idproceso == null){?>
                                 <td><?= 'NO HAY TALLAS' ?></td>
                            <?php }else{?>
                                 <td><?= $val->operaciones->proceso ?></td>
                            <?php }?>
                           
                            <td><?= $val->cantidad ?></td>
                            <?php 
                            $tipoP =  $val->ordenproduccion->tipoProducto;
                            $tipoProceso =  $val->ordenproduccion->tipo;
                            if($tipoP){
                                    $table = \app\models\ClientePrendas::find()->where(['=','id_tipo_producto', $tipoP->id_tipo_producto])->one();
                                if($val->operarioProduccion->vinculado == 0){ //personal al contrato
                                    
                                    if($tipoProceso->idtipo == 2){ //proceso que busca  terminacion
                                        $total =  $valor * $table->valor_terminacion;
                                        $total2 = ($total * $val->cantidad) ;
                                    }else{
                                        $total =  $valor * $table->valor_confeccion;
                                        $total2 = ($total * $val->cantidad) ;
                                    }
                                    $costo = $val->vlr_pago;
                                }else{
                                    if($tipoProceso->idtipo == 2){ //proceso que busca  terminacion
                                        $total =  $valor * $table->valor_terminacion;
                                        $total2 = ($total * $val->cantidad) ;
                                    }else{
                                        $total =  $valor * $table->valor_confeccion;
                                        $total2 = ($total * $val->cantidad) ;
                                    }
                                }  
                            }    
                            ?>  
                            <td><?= ''.number_format($valor,3) ?></td>
                            <td style="text-align: right"><?= $val->porcentaje_cumplimiento ?>%</td>
                            <td style="text-align: right"><?= ''. number_format($total2,0) ?></td>
                              <td><?= ''.number_format($costo) ?></td>
                        </tr>
                     <?php
                   
                     endforeach; 
                } ?>
             </tbody>   
             
        </table>    
    </div>
     <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
        </div>
</div>
<?php if($modelo){?>
    <?= LinkPager::widget(['pagination' => $pagination]) ?>
<?php } ?>

<?php $form->end() ?>
