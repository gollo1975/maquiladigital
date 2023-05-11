 <?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use app\models\Cliente;

//clases
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
//Modelos...


$this->title = 'Eficiencia diaria';
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
    "action" => Url::toRoute("eficiencia-modulo-diario/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$plantas = ArrayHelper::map(\app\models\PlantaEmpresa::find()->all(), 'id_planta', 'nombre_planta');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
                <?= $formulario->field($form, 'id_planta')->widget(Select2::classname(), [
                   'data' => $plantas,
                   'options' => ['prompt' => 'Seleccione...'],
                   'pluginOptions' => [
                       'allowClear' => true
                   ],
               ]); ?>
            <?= $formulario->field($form, 'fecha_actual')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("eficiencia-modulo-diario/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
          Registros <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr >         
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>Bodega/Planta</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha actual</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total unidades</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total eficiencia</th>
                <th scope="col" style='background-color:#B9D5CE;'>User Create</th>
                <th scope="col" style='background-color:#B9D5CE;'>User editado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cerrado</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val):
                if($val->proceso_cerrado == 0){
                    $dato = app\models\EficienciaModuloDetalle::find()->where(['=','id_eficiencia', $val->id_eficiencia])->all();
                    ?>
                    <tr style ='font-size: 85%;'>                
                        <td><?= $val->id_eficiencia ?></td>
                        <td><?= $val->planta->nombre_planta ?></td>
                        <td><?= $val->fecha_actual ?></td>
                        <td><?= $val->fecha_proceso?></td>
                        <td style="text-align: right; color: green;"><b><?= ''.number_format($val->total_unidades,0) ?></b></td>
                        <td style="text-align: right; color: blue;"><b><?= ''.number_format($val->total_eficiencia_planta,0) ?>%</b></td>
                        <td><?= $val->usuario_creador ?></td>
                        <td><?= $val->usuario_editor ?></td>
                        <td><?= $val->procesoCerrado ?></td>
                         <?php 
                            if($dato){?>
                                 <td style= 'width: 25px; height: 25px;'>
                                    <a href="<?= Url::toRoute(["eficiencia-modulo-diario/view", "id" => $val->id_eficiencia]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                 </td> 
                            <?php }else {?>     
                                     <td style= 'width: 25px; height: 25px;'>
                                    <a href="<?= Url::toRoute(["eficiencia-modulo-diario/view", "id" => $val->id_eficiencia, 'id_planta' => $val->id_planta]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                 </td> 
                                    <td style= 'width: 25px; height: 25px;'>
                                          <a href="<?= Url::toRoute(["eficiencia-modulo-diario/update", "id" => $val->id_eficiencia, 'id_planta' => $val->id_planta]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                                    </td>    
                             <?php } ?>     
                    </tr>       
                <?php }else{?>
                    <tr style ='font-size: 85%;'>                
                        <td style='background-color:#F0F3EF;' ><?= $val->id_eficiencia ?></td>
                        <td style='background-color:#F0F3EF;'><?= $val->planta->nombre_planta ?></td>
                        <td style='background-color:#F0F3EF;'><?= $val->fecha_actual ?></td>
                        <td style='background-color:#F0F3EF;'><?= $val->fecha_proceso?></td>
                        <td style="background-color:#F0F3EF; text-align: right; color: green;"><b><?= ''.number_format($val->total_unidades,0) ?></b></td>
                        <td style='background-color:#F0F3EF; text-align: right; color: blue;'><b><?= ''.number_format($val->total_eficiencia_planta,0) ?>%</b></td>
                        <td style='background-color:#F0F3EF;'><?= $val->usuario_creador ?></td>
                        <td style='background-color:#F0F3EF;'><?= $val->usuario_editor ?></td>
                        <td style='background-color:#F0F3EF;'><?= $val->procesoCerrado ?></td>
                        <td style= 'width: 25px; height: 25px; background-color:#F0F3EF;'>
                        <a href="<?= Url::toRoute(["eficiencia-modulo-diario/view", "id" => $val->id_eficiencia]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                        </td>   
                        <td style= 'width: 25px; height: 25px; background-color:#F0F3EF;'></td>
                           
                    </tr>       
                <?php }    
                
            endforeach; ?>
            </tbody>    
        </table> 
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                 <a align="right" href="<?= Url::toRoute("eficiencia-modulo-diario/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Crear dia</a>
        </div>
      <?php $form->end() ?>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

