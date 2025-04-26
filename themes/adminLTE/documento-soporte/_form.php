<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\PeriodoPago;
use app\models\Departamento;
use app\models\Municipio;
use app\models\Sucursal;
use kartik\date\DatePicker;
use kartik\select2\Select2;

?>
<?php if($Acceso == 1){
     Yii::$app->getSession()->setFlash('warning', 'Informacion importante: La resolucion de DOCUMENTO SOPORTE se vence el dia (' .$resolucion->fechavencimiento.'). Notificacion de aviso.');
}?>
<?php
$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
            'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
            'options' => []
        ],
        ]);
?>
<?php
   $formaPago = ArrayHelper::map(\app\models\FormaPago::find()->all(), 'id_forma_pago', 'concepto');
   $conProveedor = ArrayHelper::map(app\models\Proveedor::find()->orderBy('nombrecorto ASC')->all(), 'idproveedor', 'nombrecorto');
   $cuentaCobro = ArrayHelper::map(app\models\Compra::find()->where(['=','id_proveedor', $model->idproveedor])
                                                       ->andWhere(['=','genera_documento_soporte', 1])->andWhere(['=','documento_generado', 0])
                                                       ->orderBy('id_compra ASC')->all(), 'id_compra', 'Compras');
   
?>
<body>
<!--<h1>Editar Cliente</h1>-->

<div class="panel panel-success">
    <div class="panel-heading">
        Información del proveedor
    </div>
    <div class="panel-body">  
        <?php
        if ($sw == 1){
            if ($Token == 0){?>
                <div class="row">
                    <?= $form->field($model, 'idproveedor')->widget(Select2::classname(), [
                        'data' => $conProveedor,
                        'options' => ['placeholder' => 'Seleccione un cliente...'],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => [
                            "change" => 'function() { $.get( "' . Url::toRoute('documento-soporte/cargarcompras') . '", { id: $(this).val() } )
                                    .done(function( data ) {
                                        $( "#' . Html::getInputId($model, 'id_compra') . '" ).html( data );
                                });
                            }',
                            ],
                        ]);
                    ?>
                      
                        <?= $form->field($model, 'id_compra')->dropDownList(['prompt' => 'Seleccione...']) ?>
                </div>    
            <?php }else{ ?> 
                <div class="row">
                       <?= $form->field($model, 'idproveedor')->widget(Select2::classname(), [
                        'data' => $conProveedor,
                        'options' => ['placeholder' => 'Seleccione un cliente...'],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => [
                            "change" => 'function() { $.get( "' . Url::toRoute('documento-soporte/cargarcompras') . '", { id: $(this).val() } )
                                    .done(function( data ) {
                                        $( "#' . Html::getInputId($model, 'id_compra') . '" ).html( data );
                                    });
                            }',
                        ],
                        ]);
                    ?>
                    <?= $form->field($model, 'id_compra')->dropDownList($cuentaCobro, ['prompt' => 'Seleccione...']) ?>
                </div>    
            <?php } ?>  
            <div class="row">
                <?=$form->field($model, 'fecha_elaboracion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                    'value' => date('d-M-Y', strtotime('+2 days')),
                    'options' => ['placeholder' => 'Seleccione una fecha'],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'appendTo' => 'body'
                        ]])
                ?> 
                <?= $form->field($model, 'id_forma_pago')->dropDownList($formaPago, ['prompt' => 'Seleccione...']) ?>

            </div>

            <div class="row" col>
                <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
            </div>
        <?php }else{?>
            <div class="row">
                <?= $form->field($model, 'idproveedor')->widget(Select2::classname(), [
                              'data' => $conProveedor,
                              'options' => ['placeholder' => 'Seleccione el proveedor'],
                              'pluginOptions' => [
                                  'allowClear' => true ]]);
                ?>
                <?=$form->field($model, 'fecha_elaboracion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                        'value' => date('d-M-Y', strtotime('+2 days')),
                        'options' => ['placeholder' => 'Seleccione una fecha'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true]])
                ?> 
            </div> 
            <div class="row" col>
                <?= $form->field($model, 'id_forma_pago')->dropDownList($formaPago, ['prompt' => 'Seleccione...']) ?>
                <?= $form->field($model, 'documento_compra')->textInput(['maxlength' => true]) ?>
                  
            </div>
            <div class="row">
                <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
            </div>   
        <?php }?>

        <div class="panel-footer text-right">                
            <a href="<?= Url::toRoute("documento-soporte/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>