<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\TipoPagoCredito;
use kartik\select2\Select2;
use kartik\date\DatePicker;

$this->title = 'Abonos';
$this->params['breadcrumbs'][] = ['label' => 'Abonos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
        'fieldConfig' => [
            'template' => '{label}<div class="col-sm-6 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-3 control-label'],
            'options' => []
        ],
    ]); ?>

<?php
$tipopagocredito = ArrayHelper::map(TipoPagoCredito::find()->where(['=','estado',1])->orderBy('descripcion ASC')->all(), 'id_tipo_pago', 'descripcion');
?>
        <div class="panel panel-success">
            <div class="panel-heading">
                Información: Abonos
            </div>
            <div class="panel-body">
                <div class="panel-body">
                    <table class="table table-bordered table-striped table-hover">
                        <tr>
                             <th><?= Html::activeLabel($credito, 'Nro_Prestamo') ?>:</th>
                            <td><?= Html::encode($credito->id_credito) ?></td>
                             <th><?= Html::activeLabel($credito, 'Vlr_Cuota') ?>:</th>
                            <td><?= Html::encode('$'.number_format($credito->vlr_cuota,2)) ?></td>
                            <th><?= Html::activeLabel($credito, 'Nuevo_saldo') ?>:</th>
                            <td><?= Html::encode('$'.number_format($credito->saldo_credito,2)) ?></td>
                        </tr>   
                    </table>
                </div>
                <div class="row">
                     <?= $form->field($model,'vlr_abono')->textInput(['maxlength' => true]) ?>
                </div>
                
                <div class="row">
                     <?= $form->field($model, 'observacion')->textarea(['maxlength' => true]) ?>
                </div>
               
                <div class="panel-footer text-right">			
                    <a href="<?= Url::toRoute(['credito-operarios/view' , 'id' => $credito->id_credito]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
                </div>
            </div>
        </div>
<?php $form->end() ?>     
