<?php


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use kartik\depdrop\DepDrop;
use yii\widgets\DetailView;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Session;
$this->title = 'Editar hora';
$this->params['breadcrumbs'][] = ['label' => 'Cambiar hora', 'url' => ['hora_corte_masivo']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="valor-prenda-unidad-editar">

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['hora_corte_masivo'], ['class' => 'btn btn-primary btn-sm']) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Registro de la hora 
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Codigo') ?>:</th>
                    <td><?= Html::encode($model->id_valor) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_Orden') ?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Referencia') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion->codigoproducto) ?></td>
                </tr>    
            </table>
        </div>
    </div>  
    <?php
 $formulario = ActiveForm::begin([
    
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-3 control-label'],
                    'options' => []
                ],

]);
?>
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#listadocortes" aria-controls="listadocortes" role="tab" data-toggle="tab">Listado de cortes <span class="badge"><?= 1 ?></span></a></li>
       </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="pago">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:90%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha de proceso</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Hora de inicio</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Hora de corte</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Nueva hora de inicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Nueva hora de corte</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style='font-size:90%;'>
                                            <td><?= $listado->fecha_proceso?></td>
                                            <td><?= $listado->hora_inicio?></td>
                                            <td><?= $listado->hora_corte?></td>
                                            <td style="padding-center: 1;padding-right: 1;"><input type="time" name="hora_inicio[]" value="" size="12"></td>  
                                            <td style="padding-center: 1;padding-right: 1;"><input type="time" name="hora_corte[]" value="" size="12" ></td>  
                                            <input type="hidden" name="listado_hora[]" value="<?= $listado->id_corte ?>">
                                        </tr>    
                                    </tbody>
                            </table>
                            <div class="panel-footer text-right" >            
                               <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Actualizar", ['name' => 'actualizar_hora','class' => 'btn btn-primary btn-sm ']); ?>                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>     
<?php ActiveForm::end(); ?>    
</div>    