<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Operarios;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Operarios';
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
    "action" => Url::toRoute("operarios/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$operario = ArrayHelper::map(Operarios::find()->orderBy('id_operario ASC')->all(), 'id_operario', 'nombrecompleto');
$planta = ArrayHelper::map(app\models\PlantaEmpresa::find()->all(), 'id_planta', 'nombre_planta');
$AreaTrabajo = ArrayHelper::map(app\models\Ordenproducciontipo::find()->all(), 'idtipo', 'tipo');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "documento")->input("search") ?>
             <?= $formulario->field($form, 'id_operario')->widget(Select2::classname(), [
                'data' => $operario,
                'options' => ['prompt' => 'Seleccione el operario...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'estado')->dropDownList(['' => 'TODOS', '1' => 'SI', '0' => 'NO'],['prompt' => 'Seleccione el estado ...']) ?>
            <?= $formulario->field($form, 'vinculado')->dropDownList(['' => 'TODOS', '1' => 'SI', '0' => 'NO'],['prompt' => 'Seleccione el estado ...']) ?>
             <?= $formulario->field($form, 'planta')->widget(Select2::classname(), [
                'data' => $planta,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'tipo_operaria')->widget(Select2::classname(), [
                'data' => $AreaTrabajo,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("operarios/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <th scope="col" style='background-color:#B9D5CE;'>Tipo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Departamento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Municipio</th>
                <th scope="col" style='background-color:#B9D5CE;'>Celular</th>
                <th scope="col" style='background-color:#B9D5CE;'>Planta/Bodega</th>
                  <th scope="col" style='background-color:#B9D5CE;'>Area</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Personal activo" >Act.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Polivalente" >Pol.</span></th>
                 <th scope="col" style='background-color:#B9D5CE;'><span title="Vinculado" >Vin.</span></th>
                  <th scope="col" style='background-color:#B9D5CE;'><span title="Aplica para nomina alterna" >No/Alt.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
              
            </tr>
            </thead>
            <tbody>
            <?php 
             
            foreach ($modelo as $val):?>
                <tr style='font-size:85%;'>                
                <td><?= $val->id_operario ?></td>
                 <td><?= $val->tipoDocumento->tipo ?></td>
                <td><?= $val->documento?></td>
                <td><?= mb_strtoupper($val->nombrecompleto)?></td>
                <td><?= $val->departamento->departamento ?></td>
                <td><?= $val->municipio->municipio ?></td>
                <td><?= $val->celular ?></td>
                   <td><?= $val->planta->nombre_planta ?></td>
                <td><?= $val->tipoOperaria->tipo ?></td>
                <td><?= $val->estadopago?></td>
                <td><?= $val->polivalenteOperacion?></td>
                 <td><?= $val->vinculadoOperacion?></td>
                 <td><?= $val->nominaAlterna?></td>
                <td style= 'width: 25px; height: 25px;'>
                        <a href="<?= Url::toRoute(["operarios/view", "id" => $val->id_operario, 'token' => $token,]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                </td>
                <td style= 'width: 25px; height: 25px;'>
                        <a href="<?= Url::toRoute(["operarios/update", "id" => $val->id_operario ]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
             
            </tbody>            
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <!-- Inicio Nuevo Detalle proceso -->
                <?= Html::a('<span class="glyphicon glyphicon-search"></span> Actualizar salarios',
                    ['/operarios/actualizarsalarios'],
                    [
                        'title' => 'Actualizar salario minimo',
                        'data-toggle'=>'modal',
                        'data-target'=>'#modalactualizarsalarios',
                        'class' => 'btn btn-info btn-xs'
                    ])    
                    ?>
                <div class="modal remote fade" id="modalactualizarsalarios">
                        <div class="modal-dialog modal-lg-centered">
                            <div class="modal-content"></div>
                        </div>
                </div>
                 <a align="right" href="<?= Url::toRoute("operarios/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
        </div>
      <?php $form->end() ?>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>


