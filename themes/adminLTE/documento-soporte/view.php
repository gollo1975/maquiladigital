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
$conceptos = \yii\helpers\ArrayHelper::map(app\models\ConceptoDocumentoSoporte::find()->orderBy('concepto ASC')->all(), 'id_concepto','concepto');
$ConRetenciones = \yii\helpers\ArrayHelper::map(app\models\RetencionFuente::find()->all(), 'id_retencion','concepto');
?>
<div class="documento-soporte-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-xs']); ?>
	<?php if ($model->autorizado == 0){ ?>
            <?= Html::a('<span class="glyphicon glyphicon-check"></span> Autorizado', ['autorizado', 'id' => $model->id_documento_soporte], ['class' => 'btn btn-success btn-xs']) ;?>
        <?php }else {?>
            <?php if ($model->autorizado == 1 && $model->numero_soporte == 0){ 
                echo Html::a('<span class="glyphicon glyphicon-refresh"></span> Desautorizar', ['autorizado', 'id' => $model->id_documento_soporte], ['class' => 'btn btn-success btn-xs']);?>
                 <?= Html::a('<span class="glyphicon glyphicon-send"></span>  Generar consecutivo', ['generar_documento', 'id' => $model->id_documento_soporte],['class' => 'btn btn-default btn-xs' ,
                    'data' => ['confirm' => 'Esta seguro de Generar del consecutivo al documento soporte.', 'method' => 'post']]);?>
                <?= Html::a('<span class="glyphicon glyphicon-print"></span> Visualizar PDF', ['imprimir_documento', 'id' => $model->id_documento_soporte], ['class' => 'btn btn-default btn-xs']); 
            }else{  ?>  
                 <?= Html::a('<span class="glyphicon glyphicon-send"></span>  Enviar Documento a la Dian', ['enviar_documento_soporte_dian', 'id' => $model->id_documento_soporte],['class' => 'btn btn-success btn-xs',  'id' => 'my_button', 'onclick' => '$("#my_button").attr("disabled", "disabled")' ,
                 'data' => ['confirm' => 'Esta seguro de enviar el Documento Soporte  No  '. $model->numero_soporte. ' a la DIAN', 'method' => 'post']]);?>
                <?= Html::a('<span class="glyphicon glyphicon-print"></span> Visualizar PDF', ['imprimir_documento', 'id' => $model->id_documento_soporte], ['class' => 'btn btn-default btn-xs']); ?>
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
            <li role="presentation" class="active"><a href="#detalledocumento" aria-controls="detalledocumento" role="tab" data-toggle="tab">Detalle del documento <span class="badge"><?= count($detalles) ?></span></a></li>
            
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
                                            <?php if($model->autorizado == 0){?>
                                                 <td><?= Html::dropDownList('id_concepto[]', $detalle->id_concepto, $conceptos, ['class' => 'col-sm-8', 'prompt' => 'Seleccione el concepto', 'required' => true]) ?></td>
                                                 <td></td>
                                                 <td style="background:#ADB9D1; font-weight:bold;"><input type="text" name="cantidad[]" style = "text-align: right" size = '4' value="<?= $detalle->cantidad?>" required></td>
                                                 <td style="background:#ADB9D1; font-weight:bold;"><input type="text" name="valor_unitario[]" style = "text-align: right" size = '8' value="<?= $detalle->valor_unitario?>" required></td>
                                                 <td><?= Html::dropDownList('id_retencion[]', $detalle->id_retencion, $ConRetenciones, ['class' => 'col-sm-6', 'prompt' => 'Seleccione']) ?></td>
                                                 <td style="text-align: right"><?= ''. number_format($detalle->valor_retencion,0)?></td>
                                                 <td style="text-align: right"><?= ''. number_format($detalle->total_pagar,0)?></td>
                                                 <td style='width: 15px; height: 15px'>
                                                           <?= Html::a('', ['delete', 'id' => $model->id_documento_soporte, 'id_detalle' => $detalle->id_detalle], [
                                                             'class' => 'glyphicon glyphicon-trash',
                                                             'data' => [
                                                                 'confirm' => 'Esta seguro de eliminar el registro?',
                                                                 'method' => 'post',
                                                             ],
                                                           ]) ?>
                                                </td>
                                           <?php } else { ?>
                                                <td><?= $detalle->id_concepto ?></td>
                                                <td><?= $detalle->descripcion ?></td>
                                                <td><?= $detalle->cantidad ?></td>
                                                <td style="text-align: right"><?= ''. number_format($detalle->valor_unitario,0)?></td>
                                                <?php if($detalle->porcentaje_retencion > 0){?>
                                                    <td style="text-align: center"><?= $detalle->retencion->porcentaje ?></td>
                                                <?php }else{?>
                                                  <td style="text-align: center"><?= $detalle->porcentaje_retencion  ?></td>    
                                                <?php }?>  
                                                <td style="text-align: right"><?= ''. number_format($detalle->valor_retencion,0)?></td>
                                                <td style="text-align: right"><?= ''. number_format($detalle->total_pagar,0)?></td> 
                                                <td style='width: 15px; height: 15px'></td>
                                            <?php } ?>         
                                            <input type="hidden" name="listado[]" value="<?= $detalle->id_detalle ?>">
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="panel-footer text-right">
                            <?php if($model->autorizado == 0 && count($detalles) == 0){
                                    if($model->id_compra != ''){
                                         echo Html::a('<span class= "glyphicon glyphicon-plus"></span> Nueva linea',['documento-soporte/nueva_linea','id' => $model->id_documento_soporte ,'token' => 1],['class' =>'btn btn-info btn-sm']);
                                    } else {
                                        echo Html::a('<span class= "glyphicon glyphicon-plus"></span> Nueva linea',['documento-soporte/nueva_linea','id' => $model->id_documento_soporte, 'token' => 0],['class' =>'btn btn-info btn-sm']);
                                    }
                            }else{
                                if($model->autorizado == 0 && count($detalles) > 0){?>
                                         <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-warning btn-sm", 'name' => 'ActualizarLineas']) ?>		
                                <?php }else{?>

                                <?php }
                            }?>
                        </div>
                    </div>
                </div>
            </div>
            <!--TERMINA EL TABS-->
        </div>    
    </div>  
       <?php ActiveForm::end(); ?>
</div>
    