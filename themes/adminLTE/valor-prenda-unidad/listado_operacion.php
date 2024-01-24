<?php


//clases
use yii\bootstrap\Progress;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\Session;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\db\Expression;
use yii\db\Query;
    
/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$this->title = 'Tallas';
$this->params['breadcrumbs'][] = ['label' => 'Listado tallas ', 'url' => ['view_operacion_talla','id' => $id, 'token' => $token]];
$this->params['breadcrumbs'][] = $id;


?>
<div class="ordenproduccionproceso-view">
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-default btn"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view_operacion_talla','id' => $id, 'token' => $token],['class' => 'btn btn-primary btn-xs']) ?></button>
        
    </div>    
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle talla
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_orden')?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Referencia') ?></th>
                    <td><?= Html::encode($model->codigoproducto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Talla') ?>:</th>
                    <td><?= Html::encode($model->productodetalle->prendatipo->prenda. ' - '. $model->productodetalle->prendatipo->talla->talla) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cantidad') ?>:</th>
                    <td align="right"><?= Html::encode (''.number_format($model->cantidad),0) ?></td>
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
            <li role="presentation" class="active"><a href="#operaciones" aria-controls="operaciones" role="tab" data-toggle="tab">Operaciones <span class="badge"><?= count($operaciones) ?></span></a></li>
        </ul>
        <div class="tab-content">
           <div role="tabpanel" class="tab-pane active" id="operaciones">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                       <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Minunto</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operaciones</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cant. confeccionada</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $conminuto = 0;
                                    $consegundo = 0;
                                    foreach ($operaciones as $registro):?>
                                            <tr style="font-size: 85%;">
                                                <td><?= $registro->idproceso ?></td>
                                                <td><?= $registro->proceso->proceso ?></td>
                                                <td><?= ''.number_format($registro->minutos,2) ?></td>
                                                <td style="text-align: right"><?= ''.number_format($model->cantidad) ?></td>
                                                <?php 
                                                $suma = 0;
                                                $buscar = app\models\ValorPrendaUnidadDetalles::find()->where(['=','idordenproduccion', $id])
                                                                                                      ->andWhere(['=','iddetalleorden', $id_detalle])
                                                                                                      ->andwhere(['=','idproceso', $registro->idproceso])->all();
                                                foreach ($buscar as $total):
                                                      $suma += $total->cantidad;                                                  
                                                endforeach;?>
                                                <td style="text-align: right"><?= ''.number_format($suma) ?></td>  
                                                <td style= 'width: 15px; height: 10px;'>
                                                    <a href="<?= Url::toRoute(["valor-prenda-unidad/listado_operarios", "id" => $id, 'id_detalle' => $id_detalle, 'id_operacion' => $registro->idproceso, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-list-alt"></span></a>
                                                </td>
                                            </tr>
                                    <?php
                                      
                                    endforeach;
                                    ?>  
                                </tbody> 
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
        </div>  
    </div>   
    <?php ActiveForm::end(); ?>
</div>

