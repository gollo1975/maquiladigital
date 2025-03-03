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
/* @var $model app\models\Empleado */

$this->title = 'Insumos de (' .$model->tipo->tipo. ')';
$this->params['breadcrumbs'][] = ['label' => 'Insumos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_entrega;
?>
<div class="orden-produccion-insumos-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
        
        <?php if($model->autorizado == 0 && $model->numero_orden == 0){?>
             <?= Html::a('<span class="glyphicon glyphicon-check"></span> Autorizar', ['autorizado', 'id' => $model->id_entrega], ['class' => 'btn btn-default btn-sm']); ?>
        <?php }else{ 
            if($model->autorizado == 1 && $model->numero_orden == 0){  ?>
                <?= Html::a('<span class="glyphicon glyphicon-refresh"></span> Desautorizar', ['autorizado', 'id' => $model->id_entrega], ['class' => 'btn btn-default btn-sm']); ?>
                <?= Html::a('<span class="glyphicon glyphicon-send"></span>  Generar orden', ['generar_consecutivo', 'id' => $model->id_entrega],['class' => 'btn btn-info btn-xs',
                        'data' => ['confirm' => 'Esta seguro de GENERAR el consecutivo a la orden de '.$model->tipo->tipo.'. Despues de generar este proceso no se puede reversar la Orden.', 'method' => 'post']]);?>
            <?php }else{ ?>
                <?= Html::a('<span class="glyphicon glyphicon-print"></span> Visualizar PDF', ['reporte_orden_insumo', 'id' => $model->id_entrega], ['class' => 'btn btn-default btn-sm']); ?>
                <?= Html::a('<span class="glyphicon glyphicon-export"></span> Exportar a excel', ['exportar_excel', 'id' => $model->id_entrega], ['class' => 'btn btn-success btn-sm']); ?>            
                <?= Html::a('<span class="glyphicon glyphicon-send"></span>  Descargar insumos', ['enviar_insumos_modulo', 'id' => $model->id_entrega],['class' => 'btn btn-info btn-xs',
                        'data' => ['confirm' => 'Esta seguro de ENVIAR los insumos al modulo de inventario.', 'method' => 'post']]);?>
            <?php } 
        } ?> 
         
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Informacion de la orden.
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_entrega') ?>:</th>
                    <td><?= Html::encode($model->id_entrega) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idordenproduccion') ?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'orden_produccion_cliente') ?>:</th>
                    <td><?= Html::encode($model->orden_produccion_cliente) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idtipo') ?>:</th>
                    <td><?= Html::encode($model->tipo->tipo) ?></td>
                   
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_producto') ?>:</th>
                    <td>R<?= Html::encode($model->codigo_producto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_creada') ?>:</th>
                    <td><?= Html::encode($model->fecha_creada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_hora_generada') ?>:</th>
                    <td><?= Html::encode($model->fecha_hora_generada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'user_name') ?>:</th>
                    <td><?= Html::encode($model->user_name) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_orden') ?>:</th>
                    <td style="text-align:right"><?= Html::encode($model->numero_orden) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_costo') ?>:</th>
                    <td style="text-align:right"><?= Html::encode(''.number_format($model->total_costo,0)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'autorizado') ?>:</th>
                    <td ><?= Html::encode($model->estadoAutorizado) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion->cliente->nombrecorto) ?></td>
                 
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
    ]); ?>
    <!--INICIO LOS TABS-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tallas" aria-controls="tallas" role="tab" data-toggle="tab">Tallas: <span class="badge"><?= count($tallas) ?></span></a></li>
            <li role="presentation" ><a href="#listadoinsumos" aria-controls="listadoinsumos" role="tab" data-toggle="tab">Listado de insumos: <span class="badge"><?= count($listado) ?></span></a></li>

        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="tallas">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Talla</th>   
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'></th> 
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     foreach ($tallas as $val):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $val->productodetalle->prendatipo->prenda ?></td>
                                            <td><?= $val->listadoTalla?></td>
                                            <td><?= $val->cantidad ?></td>
                                            <?php if($model->autorizado == 0){?>
                                                <td style= 'width: 25px;' >
                                                    <?= Html::a('<span class="glyphicon glyphicon-list"></span> ',
                                                            ['orden-produccion-insumos/ver_insumos', 'id' => $model->id_entrega, 'tipo_orden' => $model->idtipo,'tallas' => $val->iddetalleorden,'referencia' => $model->codigo_producto],
                                                            [
                                                                'class' => '',   
                                                                'title' => 'Ver el listados de insumos',
                                                                'data-toggle'=>'modal',
                                                                'data-target'=>'#modalverinsumos'.$model->id_entrega,
                                                            ])    
                                                    ?>
                                                    <div class="modal remote fade" id="modalverinsumos<?= $model->id_entrega?>">
                                                        <div class="modal-dialog modal-lg" style ="width: 650px;">
                                                            <div class="modal-content"></div>
                                                        </div>
                                                    </div>
                                               </td>    
                                            <?php }else{?>
                                               <td style= 'width: 25px;' ></td>
                                            <?php }?>   
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                        </div>
                    </div>   
                </div>
               
            </div>
            <!--INICIO EL OTRO TABS -->
              <div role="tabpanel" class="tab-pane " id="listadoinsumos">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Código</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Nombre del insumo</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Falta</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>U. faltante</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>T. unidades</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Tallas</th>   
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th> 
                                         <th scope="col" style='background-color:#B9D5CE;'>Unidades / Metros</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad / Metros</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'></th> 
                                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalB = 0; $totalT = 0; $totalHilo = 0;
                                     foreach ($listado as $val):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $val->insumos->codigo_insumo ?></td>
                                            <td style='background-color:<?= $val->insumos->grupo->color ?>'><?= $val->insumos->descripcion ?></td>
                                            <?php if($val->faltan_insumos == 0){?>
                                                <td><?= $val->faltanInsumos ?></td>
                                                <td><?= $val->cantidad_faltante ?></td>
                                            <?php }else{?>
                                                <td style='background-color:#faf2cc;'><?= $val->faltanInsumos ?></td>
                                                <td><?= $val->cantidad_faltante ?></td>
                                            <?php }?>    
                                            <td style="text-align:right"><?= $val->detalle->total_unidades ?></td>
                                            <td><?= $val->ordenDetalle->productodetalle->prendatipo->talla->talla ?></td>
                                            <td style="text-align:right"><?= $val->cantidad ?></td>
                                            <td style="text-align:right"><?= ''.number_format($val->unidades,2)?></td>
                                            <?php if($val->metros > 0){?>
                                                <td style="text-align:right"><?= $val->metros ?>- <?= $val->insumos->tipomedida->abreviatura?></td>
                                            <?php }else{?>
                                                <td style="text-align:right"></td>
                                            <?php }   
                                            if($val->detalle->maneja_unidad == 1){
                                               if($val->insumos->grupo->abreviatura == 'I'){
                                                    if($val->detalle->total_unidades > 0){
                                                        $totalB += $val->metros;
                                                    } 
                                               }
                                               if($val->insumos->grupo->abreviatura == 'MP'){
                                                   $totalT += $val->metros; 
                                               }    
                                            } 
                                             if($val->detalle->maneja_unidad == 2){
                                                $totalHilo += $val->metros;
                                            }
                                                
                                            if($model->autorizado == 0){?>
                                                <?php if($val->detalle->maneja_unidad == 1 || $val->detalle->maneja_unidad == 2){?>
                                                    <td style= 'width: 25px;' >
                                                        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ',
                                                                ['orden-produccion-insumos/editar_linea_insumo', 'id' => $model->id_entrega, 'id_detalle' => $val->id],
                                                                [
                                                                    'class' => '',   
                                                                    'title' => 'Editar linea de insumos',
                                                                    'data-toggle'=>'modal',
                                                                    'data-target'=>'#modaeditarlineainsumos'.$model->id_entrega,
                                                                ])    
                                                        ?>
                                                        <div class="modal remote fade" id="modaeditarlineainsumos<?= $model->id_entrega?>">
                                                            <div class="modal-dialog modal-lg" style ="width: 600px;">
                                                                <div class="modal-content"></div>
                                                            </div>
                                                        </div>
                                                   </td> 
                                                <?php }else{?>
                                                   <td style= 'width: 25px;' ></td>
                                                <?php }?>   
                                            <?php }else{?> 
                                                   <td style= 'width: 25px;' ></td>
                                            <?php }?>          
                                           <td style= 'width: 25px;'><input type="checkbox" id="seleccion" name="seleccion[]" value="<?= $val->id ?>"></td>
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                            <tr>
                                <td><span class="badge">Total botones: </span> <?php echo ''. number_format($totalB,0);?> <span class="badge">Total metros de tela: </span> <?php echo $totalT;?> <span class="badge">Total conos de hilo: </span> <?php echo $totalHilo;?></td>
                            </tr>    
                        </div>
                         <?php
                         if($model->autorizado == 0){
                            if(count($listado)> 0){?>
                                <div class="panel-footer text-right">
                                   <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar", ["class" => "btn btn-danger",'name' => 'eliminar_todo']) ?>
                                </div>
                            <?php }
                        }?>
                    </div>   
                </div>
               
            </div>
            <!--INICIO EL OTRO TABS -->
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
