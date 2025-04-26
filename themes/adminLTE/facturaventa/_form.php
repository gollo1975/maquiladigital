<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Cliente;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\Facturaventa */
/* @var $form yii\widgets\ActiveForm */

$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$Fecha =  $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;
?>

<?php if($sw == 1){
     Yii::$app->getSession()->setFlash('warning', 'Informacion importante: La resolucion de facturación electrónica se vence el dia (' .$resolucion->fechavencimiento.'). Notificacion de aviso.');
}?>
<?php $form = ActiveForm::begin([
		'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
                'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-3 control-label'],
                    'options' => []
                ],
	]); ?>

 <div class="panel panel-success">
    <div class="panel-heading">
        <td style="text-align: right">  <?= $Fecha ?></td>
    </div>
    <div class="panel-body">
       
        <div class="row">
            <?= $form->field($model, 'idcliente')->widget(Select2::classname(), [
            'data' => $clientes,
            'options' => ['placeholder' => 'Seleccione un cliente...'],
            'pluginOptions' => ['allowClear' => true],
            'pluginEvents' => [
                "change" => 'function() { $.get( "' . Url::toRoute('facturaventa/ordenp') . '", { id: $(this).val() } )
                        .done(function( data ) {
                            $( "#' . Html::getInputId($model, 'idordenproduccion') . '" ).html( data );
                        });
                }',
            ],
            ]); ?>
  
        </div>
         
        <div class="row">                        
            <?= $form->field($model, 'id_factura_venta_tipo')->dropDownList($facturastipo, ['prompt' => 'Seleccione un tipo...']) ?>
        </div>
        <div class="row">            
            <?= $form->field($model, 'idordenproduccion')->widget(Select2::classname(), [
            'data' => $ordenesproduccion,
            'options' => ['placeholder' => 'Seleccione una orden de producción...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>
        </div>
        <div class="row">
            <?= $form->field($model,'fecha_inicio')->widget(DatePicker::className(),['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    ]]) ?>
                    
        </div>
        <div class="row">
            <?= $form->field($model, 'tipo_facturacion')->dropDownList(['0' => 'Completo', '1' => 'Parcial'], ['prompt' => 'Seleccione una opcion...']) ?>
        </div>   
        <div class="row">
            <?= $form->field($model, 'observacion')->textArea(['maxlength' => true]) ?>
        </div>
        <div class="panel-footer text-right">            
            <a href="<?= Url::toRoute("facturaventa/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>		
        </div>
	</div>
</div>



<?php ActiveForm::end(); ?>

