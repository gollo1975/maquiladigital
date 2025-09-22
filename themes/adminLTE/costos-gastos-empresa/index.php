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

$this->title = 'Costos / Gastos';
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
    "action" => Url::toRoute("costos-gastos-empresa/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

//$tipo = ArrayHelper::map(Ordenproducciontipo::find()->orderBy('idtipo ASC')->all(), 'idtipo', 'tipo');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
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
            
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("costos-gastos-empresa/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha corte</th>
                <th scope="col" style='background-color:#B9D5CE;'>Nómina</th>
                <th scope="col" style='background-color:#B9D5CE;'>Seguridad</th>
                <th scope="col" style='background-color:#B9D5CE;'>Gastos fijos</th>
                <th scope="col" style='background-color:#B9D5CE;'>Servicios</th>
                <th scope="col" style='background-color:#B9D5CE;'>Compras</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total costos</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total ingresos</th>
                <th scope="col" style='background-color:#B9D5CE;'>Utilidad</th>
                <th scope="col" style='background-color:#B9D5CE;'>%</th>
                <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Autorizado" >Aut.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'>Observacion</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
            <?php 
             $total = 0; $porcentaje = 0;
            foreach ($modelo as $val):
                 $total = $val->total_ingresos- $val->total_costos;
                if($val->autorizado == 1){
                    $porcentaje = number_format((100 * $total)/$val->total_ingresos, 2);
                }else{
                    $porcentaje = 0;
                }    
                ?>
                <tr style='font-size:85%;'>  
                    <td><?= $val->id_costo_gasto ?></td>
                    <td><?= $val->fecha_inicio ?></td>
                    <td><?= $val->fecha_corte ?></td>
                    <td align="right"><?= ''.number_format($val->total_nomina,0) ?></td>
                    <td align="right"><?= ''.number_format($val->total_seguridad_social, 0) ?></td>
                    <td align="right"><?= ''.number_format($val->gastos_fijos,0) ?></td>
                    <td align="right"><?= ''.number_format($val->servicios,0) ?></td>
                      <td align="right"><?= ''.number_format($val->compras,0) ?></td>
                    <td align="right" style='background:#DAF7A6;'><?= ''.number_format($val->total_costos,0) ?></td>
                    <td align="right" style='background:#b3d4fc;'><?= ''.number_format($val->total_ingresos,0) ?></td>
                    <?php if($total < 0){?>
                        <td align="right" style='color:#E72F1D;'><?= ''.number_format($total,0) ?></td>
                        <td align="right" style='color:#E72F1D;'><b><?= ($porcentaje) ?></b></td>
                    <?php }else{?>
                        <td align="right" style='color:#0A3DB1;'><b><?= ''.number_format($total,0) ?></b></td>
                        <td align="right" style='color:#0A3DB1;'><b><?= ($porcentaje) ?></b></td>
                    <?php }?>    
                   <td>
                        <?php if ($val->planta): ?>
                            <?= $val->planta->nombre_planta ?>
                        <?php else: ?>
                            <span class="text-danger">Planta no asignada</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $val->autorizadoCosto?></td>
                    <td><?= $val->observacion?></td>
                    <td style= 'width: 25px; height: 25px;'>
                               <a href="<?= Url::toRoute(["costos-gastos-empresa/view", "id" => $val->id_costo_gasto]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                    </td>
                    <td style= 'width: 25px; height: 25px;'>
                               <a href="<?= Url::toRoute(["costos-gastos-empresa/update", "id" => $val->id_costo_gasto]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                    </td>
            <?php endforeach; ?>
             </tbody>                               
        </table>    
        <div class="panel-footer text-right" >            
            <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
              <!-- Inicio Nuevo Detalle proceso -->
                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Generar',
                    ['/costos-gastos-empresa/generarcostogastos'],
                    [
                        'title' => 'Generar los costos y gastos',
                        'data-toggle'=>'modal',
                        'data-target'=>'#modalgenerarcostogastos',
                        'class' => 'btn btn-info btn-xs'
                    ])    
                    ?>
             <div class="modal remote fade" id="modalgenerarcostogastos" data-backdrop="static">
                     <div class="modal-dialog modal-lg-centered">
                         <div class="modal-content"></div>
                     </div>
             </div>
        </div>      
            <?php $form->end() ?>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>
