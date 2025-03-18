<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\DocumentoElectronico;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Resoluciones';
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
    "action" => Url::toRoute("resolucion/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$documentos = ArrayHelper::map(DocumentoElectronico::find()->where(['=','ver_documentos_resolucion', 1])->all(), 'id_documento', 'nombre_documento');

?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
            <?= $formulario->field($form, 'tipo_documento')->widget(Select2::classname(), [
                'data' => $documentos,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'numero')->textInput(['maxlength' => true]) ?>  
          <?= $formulario->field($form, 'estado')->dropDownList(['' => 'TODAS', '0' => 'ACTIVA', '1' => 'INACTIVA'],['prompt' => 'Seleccione una opcion ...']) ?>
        </div>
        
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("resolucion/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
            <tr style= 'font-size:85%;'>                
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>Numero</th>
                <th scope="col" style='background-color:#B9D5CE;'>Tipo documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. inicio</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. Vencto</th>                
                <th scope="col" style='background-color:#B9D5CE;'>Rango inicial</th> 
                <th scope="col" style='background-color:#B9D5CE;'>Rango final</th> 
                <th colspan="3" style='background-color:#B9D5CE;'><p style="color:blue;" align="center">Opciones</p></th>
                
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
            <tr style= 'font-size:85%;'>                
                <td><?= $val->idresolucion?></td>
                <td><?= $val->nroresolucion?></td>
                <td><?= $val->documentoelectronico->nombre_documento?></td>
                <td><?= $val->fechacreacion?></td>
                <td><?= $val->fechavencimiento?></td>
                <td><?= $val->inicio_rango?></td>
                <td><?= $val->final_rango?></td>
                <td style='width: 20px; height: 20px;'>
                     <a href="<?= Url::toRoute(["resolucion/view", "id" => $val->idresolucion, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                 </td>
                <?php if($val->activo == 0){?>
                    <td style='width: 20px; height: 20px;'>
                        <a href="<?= Url::toRoute(["resolucion/update", "id" => $val->idresolucion]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                    </td>
                <?php }else{?>
                    <td style='width: 20px; height: 20px;'></td>
                <?php }?>    
                
            </tr>            
            </tbody>            
            <?php endforeach; ?>
        </table> 
        <div class="panel-footer text-right" >     
            <a align="right" href="<?= Url::toRoute("resolucion/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nueva resolucion</a>
            
        </div>        
        <?php $form->end() ?>
       
     </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

