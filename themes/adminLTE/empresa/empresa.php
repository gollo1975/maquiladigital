<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Banco;
use app\models\Departamento;
use app\models\Municipio;
use app\models\TipoRegimen;
use app\models\Resolucion;

/* @var $this yii\web\View */
/* @var $model app\models\Parametros */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Empresa';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php
$form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-2 control-label'],
                'options' => []
            ],
        ]);
?>
<?php
$resoluciones = ArrayHelper::map(Resolucion::find()->all(), 'idresolucion', 'descripcion');
$bancos = ArrayHelper::map(Banco::find()->all(), 'idbanco', 'nombrecuenta');
$regimen = ArrayHelper::map(TipoRegimen::find()->all(), 'id_tipo_regimen', 'regimen');
$departamento = ArrayHelper::map(Departamento::find()->all(), 'iddepartamento', 'departamento');
$municipio = ArrayHelper::map(Municipio::find()->all(), 'idmunicipio', 'municipio');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Información Empresa
    </div>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'nitmatricula')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'dv')->textInput(['maxlength' => true]) ?>
        </div>                
        <div class="row">
            <?= $form->field($model, 'razonsocialmatricula')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'id_tipo_regimen')->dropDownList($regimen, ['prompt' => 'Seleccione un regimen...']) ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'nombrematricula')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'apellidomatricula')->textInput(['maxlength' => true]) ?>
        </div>        
        <div class="row">
            <?= $form->field($model, 'direccionmatricula')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'telefonomatricula')->textInput(['maxlength' => true]) ?>
        </div>        
        <div class="row">
            <?= $form->field($model, 'celularmatricula')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'emailmatricula')->textInput(['maxlength' => true]) ?>
        </div>        
        <div class="row">
            <?= $form->field($model, 'iddepartamento')->dropDownList($departamento, [ 'prompt' => 'Seleccione...', 'onchange' => ' $.get( "' . Url::toRoute('empresa/municipio') . '", { id: $(this).val() } ) .done(function( data ) {
                $( "#' . Html::getInputId($model, 'idmunicipio', ['required', 'class' => 'select-2']) . '" ).html( data ); });']); ?>
            <?= $form->field($model, 'idmunicipio')->dropDownList($municipio, ['prompt' => 'Seleccione...']) ?>
        </div>                
        <div class="row">
            <?= $form->field($model, 'porcentajeiva')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'porcentajeretefuente')->textInput(['maxlength' => true]) ?>
        </div>        
        <div class="row">
            <?= $form->field($model, 'porcentajereteiva')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'retefuente')->textInput(['maxlength' => true]) ?>    
        </div>                
        <div class="row">
            <?= $form->field($model, 'gran_contribuyente')->dropDownList(['0' => 'NO', '1' => 'SI'], ['prompt' => 'Seleccione una opcion...']) ?>
            <?= $form->field($model, 'agente_retenedor')->dropDownList(['0' => 'NO', '1' => 'SI'], ['prompt' => 'Seleccione una opcion...']) ?>
        </div>                
        
          <div class="row">
            <?= $form->field($model, 'nombresistema')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'paginaweb')->textInput(['maxlength' => true]) ?>    
        </div>      
         <div class="row">
            <?= $form->field($model, 'porcentaje_cesantias')->textInput(['maxlength' => true]) ?>    
            <?= $form->field($model, 'porcentaje_intereses')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="row">
           <?= $form->field($model, 'porcentaje_prima')->textInput(['maxlength' => true]) ?>    
           <?= $form->field($model, 'porcentaje_vacacion')->textInput(['maxlength' => true]) ?>    
        </div>
          <div class="row">
            <?= $form->field($model, 'ajuste_caja')->textInput(['maxlength' => true]) ?>  
           <?= $form->field($model, 'vlr_minuto_vinculado')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="row">
             <?= $form->field($model, 'vlr_minuto_contrato')->textInput(['maxlength' => true]) ?>    
              <?= $form->field($model, 'porcentaje_empresa')->textInput(['maxlength' => true]) ?> 
        </div>
        <div class="row">
             <?= $form->field($model, 'valor_minuto_confeccion')->textInput(['maxlength' => true]) ?>    
              <?= $form->field($model, 'valor_minuto_terminacion')->textInput(['maxlength' => true]) ?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'representante_legal')->textInput(['maxlength' => true]) ?> 
            <?= $form->field($model, 'cedula_representante_legal')->textInput(['maxlength' => true]) ?> 
           
        </div>
        <div class="row">
             <?= $form->field($model, 'porcentaje_minima_eficiencia')->textInput(['maxlength' => true]) ?> 
             <?= $form->field($model, 'dias_trabajados')->textInput(['maxlength' => true]) ?>   
            
        </div>
        <div class="row">
             <?= $form->field($model, 'horas_mensuales')->textInput(['maxlength' => true]) ?>  
             <?= $form->field($model, 'tiempo_maximo_operacion')->textInput(['maxlength' => true]) ?>  
            
        </div>
         <div class="row">
             <?= $form->field($model, 'sam_castigo')->textInput(['maxlength' => true]) ?>  
             <?= $form->field($model, 'total_eventos')->textInput(['maxlength' => true]) ?>  
            
        </div>
        <div class="row">
            <?= $form->field($model, 'sam_minimo')->textInput(['maxlength' => true]) ?>  
             <?= $form->field($model, 'horas_realmente_trabajadas')->textInput(['maxlength' => true]) ?>  
        </div>
        <div class="row">
            <?= $form->field($model, 'maneja_tablet_aplicacion')->dropDownList(['0' => 'NO', '1' => 'SI'], ['prompt' => 'Seleccione una opcion...']) ?>
             <?= $form->field($model, 'aplica_regla_castigo')->dropDownList(['0' => 'NO', '1' => 'SI'], ['prompt' => 'Seleccione una opcion...']) ?>
        </div>  
        <div class="row">
           <?= $form->field($model, 'id_banco_factura')->dropDownList($bancos, ['prompt' => 'Seleccione un cliente...']) ?>
        </div>  
        <div class="row">
             
            <?= $form->field($model, 'declaracion', ['template' => '{label}<div class="col-sm-10 form-group">{input}{error}</div>'])->textarea(['rows' => 3]) ?>
        </div>
        <div class="panel-footer text-right">			                        
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-success",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
