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


$this->title = 'Listado modulos';
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
    "action" => Url::toRoute("balanceo/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$cliente = ArrayHelper::map(Cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
                <?= $formulario->field($form, 'idcliente')->widget(Select2::classname(), [
                   'data' => $cliente,
                   'options' => ['prompt' => 'Seleccione...'],
                   'pluginOptions' => [
                       'allowClear' => true
                   ],
               ]); ?>
             <?= $formulario->field($form, "idordenproduccion")->input("search") ?>
            <?= $formulario->field($form, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?= $formulario->field($form, 'activo')->dropdownList(['0' => 'SI', '1' => 'NO'], ['prompt' => 'Seleccione...']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("balanceo/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
          Registros: <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr >         
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Nro del modulo">Mod.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Orden produccion interna">Op</span></th>
                 <th scope="col" style='background-color:#B9D5CE;'><span title="Referencia">Ref.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Operarios"># Ope.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'>F. Inicio</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. Final</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Minutos balanceo">M. Bal.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'> <span title="Minutos por operarios">M/O</span></th>
                <th scope="col" style='background-color:#B9D5CE;'> <span title="Unidades">Uni.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'> <span title="Proceso de confeccion">Proceso</span></th>
                <th scope="col" style='background-color:#B9D5CE;'> <span title="Cerrado o abierto el modulo">C/A</span></th>
                <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                 <th scope="col" style='background-color:#B9D5CE;'> <span title="Eficiencia del modulo">%</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th score="col" style='background-color:#B9D5CE;'></th>                              
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val):?>
            <tr style ='font-size: 85%;'>                
                <td><?= $val->id_balanceo ?></td>
                 <td><?= $val->modulo ?></td>
                <td><?= $val->idordenproduccion ?></td>
                  <td><?= $val->ordenproduccion->codigoproducto ?></td>
                <td><?= $val->cantidad_empleados ?></td>
                <td><?= $val->fecha_inicio ?></td>
                <td><?= $val->fecha_terminacion ?></td>
                <td><?= $val->cliente->nombrecorto ?></td>
                <td><?= $val->ordenproduccion->sam_balanceo ?></td>
                <td><?= $val->tiempo_operario ?></td>
                <td><?= $val->ordenproduccion->cantidad ?></td>
                 <?php if($val->id_proceso_confeccion == 1){?>
                             <td style='background-color:#A1D2D8;'><?= $val->procesoconfeccion->descripcion_proceso ?></td>
                    <?php }else{
                          if($val->id_proceso_confeccion == 2){?>
                                <td style='background-color:#F1E4F4 ;'><?= $val->procesoconfeccion->descripcion_proceso ?></td> 
                          <?php }else{?> 
                                <td style='background-color:#F5DB90;'><?= $val->procesoconfeccion->descripcion_proceso ?></td> 
                          <?php } 
                 }?>      
                <td><?= $val->estadomodulo ?></td>
                <td style="background-color: <?= $val->plantaempresa->nombre_color?> "><?= $val->plantaempresa->nombre_planta ?></td>
                <td><?= ''.number_format($val->total_eficiencia,1) ?></td>
                 <?php 
                    if($val->estado_modulo == 0){?>
                        <td style= 'width: 25px; height: 25px;'>
                          <a href="<?= Url::toRoute(["balanceo/view", "id" => $val->id_balanceo, 'idordenproduccion' => $val->idordenproduccion, 'id_proceso_confeccion' =>$val->id_proceso_confeccion, 'id_planta' =>$val->id_planta]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                        </td>
                        <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["balanceo/update", "id" => $val->id_balanceo, 'idordenproduccion' => $val->idordenproduccion, 'id_proceso_confeccion' =>$val->id_proceso_confeccion]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                       </td>
                        <td style= 'width: 25px; height: 25px;'>
                            <?= Html::a('', ['eliminar', 'id' => $val->id_balanceo], [
                                'class' => 'glyphicon glyphicon-trash',
                                'data' => [
                                    'confirm' => 'Esta seguro de eliminar el registro?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </td>
                    <?php }else {?>
                         <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["balanceo/view", "id" => $val->id_balanceo, 'idordenproduccion' => $val->idordenproduccion, 'id_proceso_confeccion' =>$val->id_proceso_confeccion, 'id_planta' =>$val->id_planta]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                         </td>   
                         <td></td>
                         <td></td>
                    <?php } ?>     
            </tr>            
            <?php endforeach; ?>
            </tbody>    
        </table> 
        <div class="panel-footer text-right" >            
           <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
           <?php $form->end() ?>
        </div>
     </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

