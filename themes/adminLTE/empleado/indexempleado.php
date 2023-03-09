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

$this->title = 'Empleados';
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
    "action" => Url::toRoute("empleado/indexempleado"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$empleado = ArrayHelper::map(Empleado::find()->orderBy('nombrecorto ASC')->all(), 'id_empleado', 'nombrecorto');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "identificacion")->input("search") ?>
             <?= $formulario->field($form, 'id_empleado')->widget(Select2::classname(), [
                'data' => $empleado,
                'options' => ['prompt' => 'Seleccione el empleado...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'fechaingreso')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        
            <?= $formulario->field($form, 'fecharetiro')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
             <?= $formulario->field($form, 'contrato')->dropDownList(['' => 'TODOS', '1' => 'SI', '0' => 'NO'],['prompt' => 'Seleccione el estado ...']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("empleado/indexempleado") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Télefono</th>
                <th scope="col" style='background-color:#B9D5CE;'>Celular</th>
                <th scope="col" style='background-color:#B9D5CE;'>Dirección</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. Contrato</th>   
                <th scope="col" style='background-color:#B9D5CE;'>F. Termino</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Contrato activo" >Act.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'>Accion</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                 <th scope="col" style='background-color:#B9D5CE;'></th>
              
            </tr>
            </thead>
            <tbody>
            <?php 
             
            foreach ($modelo as $val):?>
                <tr style='font-size:85%;'>                
                <td><?= $val->id_empleado ?></td>
                <td><?= $val->identificacion?></td>
                <td><?= mb_strtoupper($val->nombrecorto)?></td>
                <td><?= $val->telefono ?></td>
                <td><?= $val->celular ?></td>
                <td><?= $val->direccion ?></td>
                <td><?= $val->fechaingreso ?></td>
                <td><?= $val->fecharetiro; ?></td>
                <td><?= $val->contratado?></td>
                 <td style= 'width: 5px; height: 5px;'>
                    <?php
                    if($val->contrato == 0){?>
                        <div class="btn-group btn-xs">
                            <button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              Nuevo <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                              <li><?= Html::a('<span class="glyphicon glyphicon"></span>Contrato', ['/contrato/create', 'id' => $val->id_empleado], ['target' => '_blank']) ?></li>
                            </ul>
                        </div>
                    <?php }?> 
                 </td>     
                <td style= 'width: 25px; height: 25px;'>
                        <a href="<?= Url::toRoute(["empleado/view", "id" => $val->id_empleado, ]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                </td>
                <td style= 'width: 25px; height: 25px;'>
                        <a href="<?= Url::toRoute(["empleado/update", "id" => $val->id_empleado, ]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
             
            </tbody>            
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <?= Html::a('<span class="glyphicon glyphicon-check"></span> Importar operarios', ['empleado/importar_operarios'], ['class' => 'btn btn-warning btn-sm']) ?>
                <a align="right" href="<?= Url::toRoute("empleado/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>


