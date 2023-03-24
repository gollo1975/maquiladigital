<?php

use app\models\Ordenproducciondetalle;
use app\models\Ordenproducciondetalleproceso;
use app\models\Ordenproduccion;
use app\models\Cliente;
use app\models\CantidadPrendaTerminadas;
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

$this->title = 'Reprocesos';
$this->params['breadcrumbs'][] = ['label' => 'Reproceso', 'url' => ['indexreprocesoproduccion']];
$this->params['breadcrumbs'][] = $model->idordenproduccion;
$detalle_orden = Ordenproducciondetalle::find()->where(['=','idordenproduccion', $model->idordenproduccion])->orderBy('idproductodetalle ASC')->all();
?>
<div class="ordenproduccionproceso-view">
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-default btn"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['indexreprocesoproduccion'],['class' => 'btn btn-primary btn-xs']) ?></button>
    </div>    
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle del registro
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_orden')?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?></th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Orden_Cliente') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Unidades') ?>:</th>
                    <td align="right"><?= Html::encode (''.number_format($model->cantidad),0) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechallegada') ?>:</th>
                    <td><?= Html::encode($model->fechallegada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_Inicio') ?>:</th>
                    <td><?= Html::encode($model->fechaprocesada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechaentrega') ?>:</th>
                    <td><?= Html::encode($model->fechaentrega) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Sam_standar') ?>:</th>
                    <td><?= Html::encode($model->duracion.'  minutos') ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Sam_operativo') ?>:</th>
                     <td><?= Html::encode($model->sam_operativo) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Sam_balanceo') ?>:</th>
                    <td><?= Html::encode($model->sam_balanceo.'  minutos') ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Sam_preparacion') ?>:</th>
                    <td><?= Html::encode($model->sam_preparacion.'  minutos') ?></td>
                </tr>
                 <tr style="font-size: 85%;">
                  <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Codigo_producto') ?>:</th>
                    <td><?= Html::encode($model->codigoproducto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Servicio') ?>:</th>
                    <td colspan="5"><?= Html::encode($model->tipo->tipo) ?></td>
                </tr>
                <tr>
                    <button class="btn btn-primary btn-sm" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        Observaciones
                      </button>
                      <div class="collapse" id="collapseExample">
                          <div class="well" style="font-size: 85%;">
                              <?= $model->observacion ?> 
                        </div>
                     </div>
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
            <li role="presentation" class="active"><a href="#modulo" aria-controls="modulo" role="tab" data-toggle="tab">Modulos: <span class="badge"><?= count($modulos) ?></span></a></li>
        </ul>
        <div class="tab-content">
           <div role="tabpanel" class="tab-pane active" id="modulo">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Op</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo proceso</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cant.</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Nro modulo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha terminación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Modulo</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Reproceso</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                            
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modulos as $val): ?>
                                    <tr style="font-size: 85%; ">
                                        <td><?= $val->id_balanceo ?></td>
                                        <td><?= $val->idordenproduccion ?></td>
                                        <?php if ($val->id_proceso_confeccion == 1){?>
                                            <td style='background-color:#A1D2D8;'><?= $val->procesoconfeccion->descripcion_proceso ?></td>
                                         <?php }else {?>
                                            <td style='background-color:#F1E4F4;'><?= $val->procesoconfeccion->descripcion_proceso ?></td>
                                         <?php }?>    
                                         <td><?= $val->cliente->nombrecorto ?></td>
                                        <td><?= $val->cantidad_empleados ?></td>
                                        <td><?= $val->modulo ?></td>
                                        <td><?= $val->fecha_inicio ?></td>
                                        <td><?= $val->fecha_terminacion ?></td>
                                        <?php if($val->estado_modulo == 0){?>
                                            <td style='background-color:#CBAAE3;'><?= $val->estadomodulo ?></td>
                                        <?php }else{ ?>    
                                            <td style='background-color:#FFC300;'><?= $val->estadomodulo ?></td>
                                        <?php }?>    
                                        <td><?= $val->verreproceso ?></td>
                                        <td><?= $val->observacion ?></td>
                                          <td style="width: 25px;">
                                                <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/orden-produccion/detalle_reproceso_prenda', 'id_balanceo' => $val->id_balanceo,'id' => $model->idordenproduccion], ['target' => '_blank']) ?>
                                            </td>
                                       
                                    </tr>
                                </tbody>
                                <?php endforeach; ?>
                            </table>
                        </div>    
                    </div>
                </div>    
            </div>
           <!--TERMINA EL TABS DE MODULO-->
           
        </div>  
    </div>   
    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
	function marcar(source) 
	{
		checkboxes=document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
		for(i=0;i<checkboxes.length;i++) //recoremos todos los controles
		{
			if(checkboxes[i].type == "checkbox") //solo si es un checkbox entramos
			{
				checkboxes[i].checked=source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
			}
		}
	}
</script>
