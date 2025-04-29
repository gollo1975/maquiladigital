<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Municipio;
use app\models\Departamento;
use app\models\TipoDocumento;
use yii\widgets\LinkPager;
use kartik\select2\Select2;
$this->title = 'Editar Cliente';
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<body onload= "mostrar()">
<!--<h1>Editar Cliente</h1>-->
<?php if ($tipomsg == "danger") { ?>
    <h3 class="alert-danger"><?= $msg ?></h3>
<?php } else{ ?>
    <h3 class="alert-success"><?= $msg ?></h3>
<?php } ?>

<?php $form = ActiveForm::begin([
    "method" => "post",
    'id' => 'formulario',
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
	'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
                'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],
]);
?>

<?php
$departamento= ArrayHelper::map(Departamento::find()->all(), 'iddepartamento','departamento');
$formapago = ArrayHelper::map(\app\models\FormaPago::find()->all(), 'id_forma_pago', 'concepto');
$tipodocumento = ArrayHelper::map(TipoDocumento::find()->all(), 'id_tipo_documento','descripcion');
?>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h4>Información Cliente</h4>
        </div>

        <div class="panel-body">
            <div class="row" id="personal">            
                <?= $form->field($model, 'id_tipo_documento')->dropDownList($tipodocumento, ['prompt' => 'Seleccione...', 'onload' => 'mostrar()', 'onchange' => 'mostrar()', 'id' => 'id_tipo_documento']) ?>
                <?= $form->field($model, 'cedulanit')->input('text', ['id' => 'cedulanit', 'onchange' => 'calcularDigitoVerificacion()']) ?>			
                <?= Html::textInput('dv', $model->dv, ['id' => 'dv', 'aria-required' => true, 'aria-invalid' => 'false', 'maxlength' => 1, 'class' => 'form-control', 'style' => 'width:35px', 'readonly' => true, 'aria-required' => true]) ?>
            </div>
            <div class="row">
                <div id="nombrecliente" style="display:block"><?= $form->field($model, 'nombrecliente')->input("text") ?></div>
                <div id="apellidocliente" style="display:block"><?= $form->field($model, 'apellidocliente')->input("text") ?></div>    
            </div>
            <div class="row">
                <div id="razonsocial" style="display:none"><?= $form->field($model, 'razonsocial')->input("text") ?></div>

            </div>
            <div class="row">
                <?= $form->field($model, 'emailcliente')->input("text") ?>
                <?= $form->field($model, 'celularcliente')->input("text") ?>	
            </div>
            <div class="row">
                <?= $form->field($model, 'telefonocliente')->input("text", ['id' => 'telefonocliente']) ?>
                <?= $form->field($model, 'direccioncliente')->input("text") ?>
            </div>
            <div class="row">
           <?= $form->field($model, 'iddepartamento')->widget(Select2::classname(), [
                'data' => $departamento,
                'options' => ['placeholder' => 'Seleccione un departamento'],
                'pluginOptions' => ['allowClear' => true],
                'pluginEvents' => [
                    "change" => 'function() { $.get( "' . Url::toRoute('clientes/municipio') . '", { id: $(this).val() } )
                            .done(function( data ) {
                                $( "#' . Html::getInputId($model, 'idmunicipio') . '" ).html( data );
                        });
                    }',
                    ],
                ]);
            ?>
                <?= $form->field($model, 'idmunicipio')->dropDownList($municipio, ['prompt' => 'Seleccione...']) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'contacto')->input("text") ?>
                <?= $form->field($model, 'telefonocontacto')->input("text") ?>
            </div>	
            <div class="row">
                <?= $form->field($model, 'celularcontacto')->input("text") ?>
                <?= $form->field($model, 'proceso')->dropdownList(['0' => 'N/A', '1' => 'MAQUILA', '2' => 'PAQUETE COMPLETO'], ['prompt' => 'Seleccione...']) ?>			
            </div>    
            <div class="row">
                <?= $form->field($model, 'formapago')->dropdownList($formapago, ['prompt' => 'Seleccione...']) ?>
                <?= $form->field($model, 'plazopago')->input("text",['id' => 'plazopago']) ?>			
            </div>
            <div class="row">
                <?= $form->field($model, 'tiporegimen')->dropdownList(['1' => 'COMÚN', '2' => 'SIMPLIFICADO'], ['prompt' => 'Seleccione...', 'onchange' => 'tregimen()', 'id' => 'tiporegimen']) ?>
                <?= $form->field($model, 'autoretenedor')->dropdownList(['1' => 'SI', '0' => 'NO'], ['prompt' => 'Seleccione...', 'onchange' => 'retener()', 'id' => 'autoretenedor']) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'retencioniva')->dropdownList(['0' => 'NO', '1' => 'SI'], ['id' => 'retencioniva', 'readonly' => 'readonly']) ?>
                <?= $form->field($model, 'retencionfuente')->dropdownList(['0' => 'NO', '1' => 'SI'], ['id' => 'retencionfuente', 'readonly' => 'readonly']) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'minuto_confeccion')->input("text") ?>
                <?= $form->field($model, 'minuto_terminacion')->input("text") ?>			
            </div>
            <div class="row">
                <div class="field-tblclientes-observaciones_cliente has-success">
                    <?= $form->field($model, 'email_envio_factura_dian')->input("text") ?>
                    <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
                </div>
            </div>        
        </div>
        <div class="panel-footer text-right">        
            <a href="<?= Url::toRoute("clientes/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
<?php $form->end() ?>
