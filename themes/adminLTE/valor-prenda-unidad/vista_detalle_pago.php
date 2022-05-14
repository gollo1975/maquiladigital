<?php


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


/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$this->title = 'Vista pago';
$this->params['breadcrumbs'][] = ['label' => 'Vista del pago', 'url' => ['pageserviceoperario','fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]];
$this->params['breadcrumbs'][] = $model->id_pago;
?>

<div class="valor-prenda-unidad-vista">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['pageserviceoperario', 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte], ['class' => 'btn btn-primary btn-sm']) ?>              
        
    <br>
    <br>    
    <div class="panel panel-success">
        <div class="panel-heading">
            Registro operario
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, "Nro_pago") ?>:</th>
                    <td><?= Html::encode($model->id_pago) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Documento') ?>:</th>
                    <td><?= Html::encode($model->documento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Operario') ?>:</th>
                    <td><?= Html::encode($model->operario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Devengado') ?>:</th>
                    <td style="text-align: right;"><?= Html::encode(''.number_format($model->devengado,0)) ?></td>
                </tr>
                 <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, "Fecha_inicio") ?>:</th>
                    <td><?= Html::encode($model->fecha_inicio) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_corte') ?>:</th>
                    <td><?= Html::encode($model->fecha_corte) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Total_dias') ?>:</th>
                    <td><?= Html::encode($model->total_dias) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Deduccion') ?>:</th>
                    <td style="text-align: right;"><?= Html::encode(''.number_format($model->deduccion,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, "Usuario") ?>:</th>
                    <td><?= Html::encode($model->usuario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cerrado') ?>:</th>
                    <td><?= Html::encode($model->fecha_corte) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Observación') ?>:</th>
                    <td><?= Html::encode($model->observacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Total_Pagar') ?>:</th>
                    <td style="text-align: right;"><?= Html::encode(''.number_format($model->Total_pagar,0)) ?></td>
                </tr>
                
            </table>
        </div>
    </div>
    
    <div class="table-responsive">
        <div class="panel panel-success ">
            <div class="panel-heading">
                Detalles
            </div>
            <div class="panel-body">
              <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr style="font-size: 85%;">
                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Concepto</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Devengado</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Deducción</th>
                        <th scope="col" style='background-color:#B9D5CE;'></th>
                         <th scope="col" style='background-color:#B9D5CE;'></th>
                           
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($detalle_pago as $val): ?>
                    <tr style="font-size: 85%;">
                        <td><?= $val->codigo_salario ?></td>
                        <td><?= $val->codigoSalario->nombre_concepto  ?></td>
                        <td><?= '$ '.number_format($val->devengado,0) ?></td>
                        <td><?= '$ '.number_format($val->deduccion,0) ?></td>
                        <?php if($autorizado == 0){?>
                            <td style=' width: 25px;'>
                               <a href="<?= Url::toRoute(["valor-prenda-unidad/editarvistadetallepago", 'id_detalle' => $val->id_detalle, 'id_pago'=>$val->id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'autorizado' => $autorizado]) ?>" ><span class="glyphicon glyphicon-pencil "></span></a>
                            </td>
                            <td style= 'width: 25px;'>
                                <?= Html::a('', ['eliminardetallepago', 'id_detalle' => $val->id_detalle, 'id_pago' => $val->id_pago,'fecha_inicio'=>$fecha_inicio, 'fecha_corte' => $fecha_corte, 'autorizado' => $autorizado], [
                                    'class' => 'glyphicon glyphicon-trash',
                                    'data' => [
                                        'confirm' => 'Esta seguro de eliminar el registro?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            </td>
                        <?php }else{?>
                             <th></th>
                             <th ></th>
                        <?php }?>    
                    </tr>
                    </tbody>
                    <?php endforeach; ?>
                </table>
                <?php if($autorizado == 0){?>
                    <div class="panel-footer text-right"> 
                       <?= Html::a('<span class="glyphicon glyphicon-save"></span> Importar', ['valor-prenda-unidad/importarconceptosalarios', 'id_pago' => $id_pago, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio, 'autorizado' => $autorizado], ['class' => 'btn btn-success btn-sm']); ?>      
                    </div>  
                <?php }?>
            </div>
            
            
        </div>
    </div>
    
</div>
