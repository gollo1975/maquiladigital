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
use app\models\Cargos;

$this->title = 'AGENTES COMERCIALES';
$this->params['breadcrumbs'][] = $this->title;
?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtroagentecomercial");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista proveedor</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("agentes-comerciales/index"),
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
	
    <div class="panel-body" id="filtroagentecomercial" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "documento")->input("search") ?>
            <?= $formulario->field($form, "nombre_completo")->input("search") ?>
            <?= $formulario->field($form, 'estado')->dropdownList(['0' => 'SI', '1' => 'NO'], ['prompt' => 'Seleccione...']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("agentes-comerciales/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
           <tr style="font-size: 90%;">    
               <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>Tipo documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Nombre completo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Celular</th>
                <th scope="col" style='background-color:#B9D5CE;'>Dirección</th>
                <th scope="col" style='background-color:#B9D5CE;'>Email</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>  
                <th scope="col" style='background-color:#B9D5CE;'></th>  
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
            <tr style="font-size: 90%;">
                <?php if($val->estado == 0){?>
                    <td><?= $val->id_agente ?></td>
                    <td><?= $val->tipoDocumento->tipo ?></td>
                    <td><?= $val->nit_cedula ?></td>
                    <td><?= $val->nombre_completo ?></td>
                    <td><?= $val->celular_agente ?></td>
                    <td><?= $val->direccion ?></td>
                    <td><?= $val->email_agente ?></td>
                <?php }else{?>
                    <td style='background-color:#CEECD4;'><?= $val->id_agente ?></td>
                    <td style='background-color:#CEECD4;'><?= $val->tipoDocumento->tipo ?></td>
                    <td style='background-color:#CEECD4;'><?= $val->nit_cedula ?></td>
                    <td style='background-color:#CEECD4;'><?= $val->nombre_completo ?></td>
                    <td style='background-color:#CEECD4;'><?= $val->celular_agente ?></td>
                    <td style='background-color:#CEECD4;'><?= $val->direccion ?></td>
                    <td style='background-color:#CEECD4;'><?= $val->email_agente ?></td>
                <?php }?>    
                <td style= 'width: 25px; height: 20px;'>
                    <a href="<?= Url::toRoute(["agentes-comerciales/view", "id" => $val->id_agente]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                </td>
                <td style= 'width: 25px; height: 20px;'>
                    <a href="<?= Url::toRoute(["agentes-comerciales/update", "id" => $val->id_agente])?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
            </tr>
            </tbody>
            <?php endforeach; ?>
        </table>
        <div class="panel-footer text-right" >
             <?php
                $form = ActiveForm::begin([
                            "method" => "post",                            
                        ]);
                ?>    
            
            <a align="right" href="<?= Url::toRoute("agentes-comerciales/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
              <?php $form->end() ?>
            
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>