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

$this->title = 'Detalle balanceo';
$this->params['breadcrumbs'][] = ['label' => 'View_balanceo', 'url' => ['view_balanceo','id' => $model->idordenproduccion]];
$this->params['breadcrumbs'][] = $model->idordenproduccion;
//codigo que permite buscar el si la OP tiene modulo de balanceo
$varModular = 0;
$buscarOrden = app\models\Balanceo::find()->where(['=','idordenproduccion', $model->idordenproduccion])->andWhere(['=','id_proceso_confeccion', 2])->one();
if($buscarOrden){
    $varModular = 1;
}
?>
<div class="ordenproduccionproceso-view">
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-default btn"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['produccionbalanceo'],['class' => 'btn btn-primary btn-xs']) ?></button>
        <?php if($model->cerrar_orden == 0){?>
            <div class="btn-group btn-sm" role="group">
                <button type="button" class="btn btn-info  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Modulos
                  <span class="caret"></span>
                </button>

                  <ul class="dropdown-menu">
                        <li><?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear ', ['/balanceo/create', 'idordenproduccion' => $model->idordenproduccion], ['target' => '_blank']) ?></li>
                  </ul>
            </div>
        <?php }?>
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
            <li role="presentation" class="active"><a href="#operaciones" aria-controls="operaciones" role="tab" data-toggle="tab">Operaciones <span class="badge"><?= count($operaciones) ?></span></a></li>
            <li role="presentation"><a href="#modulo" aria-controls="modulo" role="tab" data-toggle="tab">Modulos <span class="badge"><?= count($modulos) ?></span></a></li>
            <li role="presentation"><a href="#tallas" aria-controls="tallas" role="tab" data-toggle="tab">Tallas <span class="badge"><?= count($modeldetalles) ?></span></a></li>
        </ul>
        <div class="tab-content">
           <div role="tabpanel" class="tab-pane active" id="operaciones">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Segundos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Minutos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Orden</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Proceso</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Pieza</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha creación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Maquina</th>
                                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($operaciones as $val):
                                        $flujo = app\models\FlujoOperaciones::find()->where(['=','idproceso', $val->idproceso])->andWhere(['=','idordenproduccion', $model->idordenproduccion])->all();
                                        if(!$flujo){
                                            $table = new app\models\FlujoOperaciones();
                                            $table->idproceso = $val->idproceso;
                                            $table->idordenproduccion = $model->idordenproduccion;
                                            $table->id_tipo = $val->id_tipo;
                                            $table->segundos = $val->duracion;
                                            $table->minutos = ''.number_format($val->duracion/60,2);
                                            $table->usuariosistema = Yii::$app->user->identity->username;
                                            $table->insert();
                                        }
                                        
                                    endforeach; ?>
                                    <?php
                                    $conminuto = 0;
                                    $consegundo = 0;
                                    $prenda = app\models\FlujoOperaciones::find()->where(['=', 'idordenproduccion', $model->idordenproduccion])->orderBy('pieza ASC, operacion DESC, orden_aleatorio ASC')->all();
                                    foreach ($prenda as $registro):?>
                                            <tr style="font-size: 85%;">
                                                 <td><?= $registro->id ?></td>
                                                <td><?= $registro->idproceso ?></td>
                                                <td><?= $registro->proceso->proceso ?></td>
                                                <td><?= ''.number_format($registro->segundos,0) ?></td>
                                                <td><?= ''.number_format($registro->minutos ,2) ?></td>
                                                <td><?= $registro->orden_aleatorio ?></td>
                                                <?php if($registro->operacion == 0){?>
                                                   <td style='background-color:#B9D5CE;'><?= 'BALANCEO' ?></td>
                                                <?php }else{?>
                                                   <td style='background-color:#A5D3E6;'><?= 'PREPARACION' ?></td>
                                                <?php }?>   
                                                <?php if($registro->pieza == 0){?>
                                                   <td style='background-color:#ACF1D8;'><?= 'PIEZA 1' ?></td>
                                                <?php }else{?>
                                                   <td style='background-color:#E3CDFC;'><?= 'PIEZA 2' ?></td>
                                                <?php }?>     
                                                 <td><?= $registro->fecha_creacion ?></td>
                                                <?php 
                                                if($registro->id_tipo == ''){?>
                                                       <td><?= 'Not found' ?></td>
                                                 <?php }else{?>
                                                        <td><?= $registro->tipomaquina->descripcion ?></td>
                                                <?php }?> 
                                                <td style="width: 30px;"><input type="checkbox" name="id[]" value="<?= $registro->id ?>"></td>        
                                            </tr>
                                    <?php
                                       $consegundo += $registro->segundos;
                                       $conminuto += $registro->minutos;
                                    endforeach;
                                    ?>  
                                </tbody> 
                                <td colspan="3"></td><td style="font-size: 85%;"><b>Tot. Seg.:</b> <?= $consegundo ?> <td style="font-size: 85%;"><b>Sam_Balanceo:</b> <?= $model->sam_balanceo ?></td><td style="font-size: 85%; color: "><b>Sam_Preparacion:</b> <?= $model->sam_preparacion ?><td style="font-size: 85%; color: "></td><td colspan="3"></td>
                            </table>
                        </div>    
                    </div>
                </div>
                <?php $orden = Ordenproduccion::findOne($model->idordenproduccion);
                if($orden->faltante <> 0){ ?>
                    <div class="panel-footer text-right">
                        <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Excel', ['exceloperaciones_iniciales', 'id' => $model->idordenproduccion], ['class' => 'btn btn-primary btn-sm'])?>
                        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['orden-produccion/editarflujooperaciones', 'idordenproduccion' => $model->idordenproduccion],[ 'class' => 'btn btn-success btn-sm']) ?>                                                             
                        <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar", ["class" => "btn btn-danger btn-sm", 'name' => 'eliminarflujo']) ?>
                   </div>
                <?php }else{?>
                <div class="panel-footer text-right">
                    <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Excel', ['exceloperaciones_iniciales', 'id'=>$model->idordenproduccion], ['class' => 'btn btn-primary btn-sm'])?>
                </div>    
                <?php }?>
            </div>
           <!-- TERMINA EL TABS-->
           
           <div role="tabpanel" class="tab-pane" id="modulo">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Op</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Nro modulo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Eficiencia</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>F. terminación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Proceso</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                            
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($modulos) > 0){
                                        foreach ($modulos as $val): ?>
                                        <tr style="font-size: 85%; ">
                                            <td><?= $val->id_balanceo ?></td>
                                            <td><?= $val->idordenproduccion ?></td>
                                             <td><?= $val->cliente->nombrecorto ?></td>
                                            <td><?= $val->cantidad_empleados ?></td>
                                            <td><?= $val->modulo ?></td>
                                            <td align="right"><?= $val->total_eficiencia ?></td>
                                            <td><?= $val->fecha_inicio ?></td>
                                            <td><?= $val->fecha_terminacion ?></td>
                                            <?php if($val->id_proceso_confeccion == 1){?>
                                                       <td style='background-color:#B9D5CE;'><?= $val->procesoconfeccion->descripcion_proceso?></td>
                                                    <?php }else{?>
                                                       <td style='background-color:#A5D3E6;'><?= $val->procesoconfeccion->descripcion_proceso?></td>
                                                    <?php }?>   
                                            <td><?= $val->observacion ?></td>
                                            <?php if($val->procesoconfeccion->estado_proceso == 1){
                                                   $modulo = $val->id_balanceo;
                                                    if($val->estado_modulo == 0){?>
                                                        <td style="width: 50px; height: 30px;">
                                                            <div class="panel-footer text-center">
                                                              <!-- Inicio Nuevo Detalle proceso -->
                                                                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Prendas',
                                                                    ['/orden-produccion/subirprendaterminada','id_balanceo' => $val->id_balanceo, 'idordenproduccion' => $model->idordenproduccion, 'id_proceso_confeccion' => $val->id_proceso_confeccion],
                                                                    [
                                                                        'title' => 'Prendas terminadas',
                                                                        'data-toggle'=>'modal',
                                                                        'data-target'=>'#modalsubirprendaterminada'.$val->id_balanceo,
                                                                        'class' => 'btn btn-success btn-xs'
                                                                    ])    
                                                               ?>
                                                            </div> 
                                                            <div class="modal remote fade" id="modalsubirprendaterminada<?= $val->id_balanceo ?>">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content"></div>
                                                                </div>
                                                            </div>
                                                         </td>
                                                    <?php }else{ ?>
                                                        <td style="width: 50px; height: 30px;"></td>
                                                    <?php }
                                            }else{
                                                $modulo = $val->id_balanceo;
                                                ?>
                                                        <td style="width: 50px; height: 30px;"></td>
                                            <?php } ?>                                    
                                        </tr>
                                    </tbody>
                                    <?php endforeach;
                                     $modulo = $modulo;
                                    }else{
                                         $modulo = 0;
                                    }
                               
                                ?>
                            </table>
                        </div>    
                    </div>
                </div>    
            </div>
           <!--TERMINA EL TABS DE MODULO-->
           
            <div role="tabpanel" class="tab-pane" id="tallas">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Producto / Talla</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Confeccionadas</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Faltante</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Total segundos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Total minutos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Minutos confección</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                         <th scope="col" style='background-color:#B9D5CE;'></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                      $t_segundos = 0; $t_minutos = 0; $t_minutos_conf = 0;
                                      $total_c = 0; $total_confeccion = 0;
                                     
                                    foreach ($modeldetalles as $val):
                                        $total_c = 0;
                                        $cantidad = 0;
                                        ?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $val->iddetalleorden ?></td>
                                            <td><?= $val->productodetalle->prendatipo->prenda.' / '.$val->productodetalle->prendatipo->talla->talla ?></td>
                                            <td align="center"><?= $val->cantidad ?></td>
                                            <?php
                                            $cantidad = app\models\CantidadPrendaTerminadas::find()->where(['=','iddetalleorden', $val->iddetalleorden])->all();
                                            if(count($cantidad) > 0){
                                                foreach ($cantidad as $variable):
                                                    $total_c += $variable->cantidad_terminada;
                                                endforeach;
                                                ?>   
                                                 <td align="right" style="background: #F6CECE;"><?= $total_c ?></td>    
                                            <?php }else {?>
                                                  <td align="right"><?= 0 ?></td>
                                             <?php } 
                                               $cantidad = $val->cantidad - $total_c;
                                            if($cantidad == $val->cantidad){?> 
                                                <td align="center"><?= $val->cantidad - $total_c ?></td>    
                                            <?php }else{?>
                                                <td align="center" style="background: #A8E4C9;"><?= $val->cantidad - $total_c ?></td>
                                            <?php }?>    
                                             <td align="right"><?= ''.number_format(($model->duracion * 60)* ($val->cantidad),0) ?></td>
                                             <td align="right"><?= ''.number_format($model->duracion  * $val->cantidad,0) ?></td>
                                            <td align="right" style="background: #F5BCA9;"><?= ''.number_format($model->segundosficha / 60 * $val->cantidad,0) ?></td>
                                            <?php
                                            if ($varModular == 0){?>
                                                <td style= 'width: 25px; height: 25px;'>
                                                        <a href="<?= Url::toRoute(["orden-produccion/vistatallas", "iddetalleorden" => $val->iddetalleorden]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                                </td>
                                                <td style= 'width: 25px; height: 25px;'>
                                                </td>
                                            <?php }else{
                                                    if ($varModular == 1){?>
                                                        <td style= 'width: 25px; height: 25px;'>
                                                               <a href="<?= Url::toRoute(["orden-produccion/vistatallasbalanceopreparacion", "iddetalleorden" => $val->iddetalleorden,'modulo' => $buscarOrden->id_balanceo,]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                                       </td>
                                                        <td style= 'width: 25px; height: 25px;'>
                                                                <a href="<?= Url::toRoute(["orden-produccion/recoger_preparacion", "iddetalleorden" => $val->iddetalleorden,'modulo' => $buscarOrden->id_balanceo, 'id'=> $model->idordenproduccion]) ?>" ><span class="glyphicon glyphicon-plus"></span></a>
                                                        </td>
                                                    <?php }else{?>
                                                        <td style= 'width: 25px; height: 25px;'>
                                                           <td></td> 
                                                        </td>
                                                   <?php }
                                                   
                                            } ?>  
                                                        
                                        </tr>
                                        <?php
                                            $total_confeccion +=$val->faltante;
                                            $t_minutos += ($model->duracion * $val->cantidad);
                                            $t_segundos = $t_minutos * 60;
                                            $t_minutos_conf += ($model->segundosficha/60 * $val->cantidad);
                                    endforeach; ?>
                                <td colspan="3"><td style="font-size: 85%; width: 100px; text-align: right;"><b>Total:</b> <?= ''.number_format($total_confeccion)?> </td><td style="font-size: 85%; width: 100px; text-align: right;"><b>Faltan:</b><?= ''. number_format($model->cantidad - $total_confeccion,0) ?></td><td style="font-size: 85%; width: 160px; text-align: right;"><b>T. Segundos:</b> <?= ''.number_format($t_segundos,0) ?> </td><td style="font-size: 85%; width: 160px; text-align: right;"><b>T. Minutos:</b> <?= ''.number_format($t_minutos,0) ?></td> <td style="font-size: 85%; width: 170px; text-align: right; background: #4B6C67; color: #FFFFFF;"><b>T. Minutos conf.:</b> <?= ''.number_format($t_minutos_conf,0) ?></td> <td colspan="2">
                                </tbody>     
                            </table>
                        </div>    
                    </div>
                </div>    
            </div>
           
           <!--TERMINA TABS-->
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
