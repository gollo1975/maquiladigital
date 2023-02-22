<?php
//modelos
use app\models\Operarios;

use app\models\TiposMaquinas;
//clases
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Recibocaja */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Editar operacion';
$operarios = ArrayHelper::map(Operarios::find()->where(['=','estado', 1])->andWhere(['=','id_planta', $id_planta])->orderBy('nombrecompleto ASC')->all(), 'id_operario', 'nombrecompleto');
$tipos = ArrayHelper::map(TiposMaquinas::find()->where(['=','estado', 1])->orderBy('descripcion ASC')->all(), 'id_tipo', 'descripcion');

?>

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

<div class="panel panel-success">
    <div class="panel-heading">
        Detalle del registro
    </div>
    <div class="panel-body">        
        
         <div class="row" col>
            <?= $form->field($model, 'id_operario')->widget(Select2::classname(), [
                'data' => $operarios,                
                'options' => ['prompt' => 'Seleccione el operario'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $form->field($model, 'id_tipo')->widget(Select2::classname(), [
                'data' => $tipos,                
                'options' => ['prompt' => 'Seleccione la maquina'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="row" col>
            <?= $form->field($model, 'minutos')->textInput(['maxlength' => true]) ?>
           <?= $form->field($model, 'segundos')->textInput(['maxlength' => true]) ?>
        </div>
          <div class="row" col>
            <?= $form->field($model, 'ordenamiento')->textInput(['maxlength' => true]) ?>
           <?= $form->field($model, 'estado_operacion')->dropDownList(['0'=> 'ACTIVO', '1' => 'INACTIVO'], ['prompt' => 'Seleccione...']) ?>
         </div>
       
      
        <div class="panel-footer text-right">                        
            <a href="<?= Url::toRoute(["balanceo/view", 'id' => $balanceo->id_balanceo, 'idordenproduccion' => $idordenproduccion,'id_proceso_confeccion' => $id_proceso_confeccion, 'id_planta' => $id_planta]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>