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
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Ingresos/ Deducciones';
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
    "action" => Url::toRoute("ingresos-deducciones/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form,'fecha_inicio')->widget(DatePicker::className(),['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?= $formulario->field($form,'fecha_corte')->widget(DatePicker::className(),['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        </div> 
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("ingresos-deducciones/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh '></span> Actualizar</a>
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
         Registros:<span class="badge"><?= $pagination->totalCount ?></span>
       
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style="font-size: 85%">                
                <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha corte</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha hora</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proceso abierto</th>                
                <th scope="col" style='background-color:#B9D5CE;'>User name</th> 
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                                     
            </tr>
            </thead>
            <tbody>
                <?php foreach ($model as $val): ?>
                    <tr style="font-size: 85%">                
                        <td><?= $val->id_ingreso?></td>
                        <td><?= $val->fecha_inicio?></td>
                        <td><?= $val->fecha_corte?></td>
                        <td><?= $val->fecha_hora_proceso?></td>
                         <td>
                            <?php if ($val->estado_proceso === 0){ ?>
                                <a href="<?= Url::toRoute(['ingresos-deducciones/proceso_cerrado', 'id' => $val->id_ingreso])?>"
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('¿Estás seguro de que deseas cerrar este proceso? ¡Esta acción no se puede deshacer!');">
                                    <?= $val->estadoProceso?></a>
                            <?php }else{?>
                                <?= $val->estadoProceso?></a>
                           <?php }      ?>
                        </td>
                        <td><?= $val->user_name?></td>
                        <?php if($val->estado_proceso == 0){?>
                            <td style="width: 20px; height: 20px;">
                                    <a href="<?= Url::toRoute(["ingresos-deducciones/view", "id" => $val->id_ingreso, 'fecha_inicio' => $val->fecha_inicio, 'fecha_corte' => $val->fecha_corte]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                   
                            </td>
                            <?php 
                                 $detalle = app\models\IngresosDeduccionesDetalle::find()->where(['=','id_ingreso', $val->id_ingreso])->one();
                                if(!$detalle){?>
                                    <td style="width: 20px; height: 20px">
                                        <a href="<?= Url::toRoute(["ingresos-deducciones/update", "id" => $val->id_ingreso]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                                    </td>
                                    
                                <?php }else{?>
                                    <td style="width: 20px; height: 20px">
                                      
                                <?php } 
                        
                        }else{?> 
                           <td style="width: 20px; height: 20px">
                                <a href="<?= Url::toRoute(["ingresos-deducciones/view", "id" => $val->id_ingreso, 'fecha_inicio' => $val->fecha_inicio, 'fecha_corte' => $val->fecha_corte]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                                    
                           </td>
                           <td style="width: 20px; height: 20px">
                              
                           </td>
                           
                        <?php }?>    
                    </tr>            
                <?php endforeach; ?>
            </tbody>    
        </table> 
      <div class="panel-footer text-right" >            
                <a align="right" href="<?= Url::toRoute("ingresos-deducciones/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
            <?php $form->end() ?>
        </div>
     </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

