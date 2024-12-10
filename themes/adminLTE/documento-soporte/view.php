<?php
use app\models\FormatoContenido;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Contrato */

$this->title = 'Documento soporte';
$this->params['breadcrumbs'][] = ['label' => 'Dcumento soporte', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_documento_soporte;
?>
<div class="documento-soporte-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-xs']) ?>
	<?php if ($model->autorizado == 0){ ?>
            <?= Html::a('<span class="glyphicon glyphicon-check"></span> Autorizado', ['autorizado', 'id' => $model->id_documento_soporte], ['class' => 'btn btn-success btn-xs']) ?>
        <?php }else {?>
            <?php if ($model->autorizado == 1 && $model->numero_soporte == 0){ ?>
                 <?= Html::a('<span class="glyphicon glyphicon-uncheck"></span> Desautorizar', ['autorizado', 'id' => $model->id_documento_soporte], ['class' => 'btn btn-success btn-xs']);?>
                 <?= Html::a('<span class="glyphicon glyphicon-send"></span>  Generar documento', ['generar_documento', 'id' => $model->id_documento_soporte],['class' => 'btn btn-default btn-xs' ,
                    'data' => ['confirm' => 'Esta seguro de Generar del consecutivo al documento soporte.', 'method' => 'post']]);?>
                <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimir_documento', 'id' => $model->id_documento_soporte], ['class' => 'btn btn-default btn-xs']); 
             
                
            }else{  ?>  
        
            <?php }
        }    ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Registro del documento soporte
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_documento_soporte') ?></th>
                    <td><?= Html::encode($model->id_documento_soporte) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idproveedor') ?></th>
                    <td><?= Html::encode($model->proveedor->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_forma_pago') ?></th>
                    <td><?= Html::encode($model->formaPago->concepto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'documento_compra') ?></th>
                    <td><?= Html::encode($model->documento_compra) ?></td>
                </tr>
                 <tr style="font-size: 85%;">
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_soporte') ?></th>
                    <td><?= Html::encode($model->numero_soporte) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_elaboracion') ?></th>
                    <td><?= Html::encode($model->fecha_elaboracion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_hora_registro') ?></th>
                    <td><?= Html::encode($model->fecha_hora_registro) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'valor_pagar') ?></th>
                    <td style='text-align: right'><?= Html::encode(''. number_format($model->valor_pagar,0)) ?></td>
                    
                </tr>
            </table>
        </div>
    </div> 
    <?php $form = ActiveForm::begin([
          'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
          'fieldConfig' => [
              'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
              'labelOptions' => ['class' => 'col-sm-3 control-label'],
              'options' => []
          ],
    ]);?>
          
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#detalledocumento" aria-controls="detalledocumento" role="tab" data-toggle="tab">Detalle del documento <span class="badge"><?= 1 ?></span></a></li>
            
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="detalledocumento">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size: 85%'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Concepto</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Vr. Unitario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>% Retencion</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Vr Retencion</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Total linea</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($detalles as $key => $detalle) {?>
                                        <tr style='font-size: 85%'>
                                            <td><?= $detalle->id_concepto?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--TERMINA EL TABS-->
        </div>    
    </div>  
       <?php ActiveForm::end(); ?>
</div>
    