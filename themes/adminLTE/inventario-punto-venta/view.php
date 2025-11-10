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

$this->title = '(' .$model->nombre_producto. ')';
$this->params['breadcrumbs'][] = ['label' => 'Inventario bodega', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_inventario;
?>

<div class="inventario-punto-venta-view">

    <!--<?= Html::encode($this->title) ?>-->
    <p>
        <?php if($token == 0){
            echo  Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']);
        }else{ 
            echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['search_consulta_inventario'], ['class' => 'btn btn-primary btn-sm']);
        }
        if($model->aplica_talla_color == 1){
            echo Html::a('<span class="glyphicon glyphicon-eye-close"></span> Cerrar combinacion', ['cerrar_combinaciones', 'id' => $model->id_inventario, 'token'=> $token, 'codigo' =>$codigo],['class' => 'btn btn-info btn-sm',
                               'data' => ['confirm' => 'Esta seguro de cerrar la combinacion de tallas y colores. Tener presente que se actualiza el inventario de BODEGA.', 'method' => 'post']]);
            if($codigo <> 0 && $model->inventario_aprobado == 0){
                 echo Html::a('<span class="glyphicon glyphicon-import"></span> Descargar inventario de bodega', ['descargar_inventario_bodega', 'id' => $model->id_inventario, 'token'=> $token, 'codigo' =>$codigo],['class' => 'btn btn-success btn-sm',
                               'data' => ['confirm' => 'Esta seguro de enviar el proceso a BODEGA para descargar en inventario que se traslado al punto de venta.', 'method' => 'post']]);
            }
        }?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            INVENTARIO DE BODEGA
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?></th>
                    <td><?= Html::encode($model->id_inventario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Codigo') ?></th>
                    <td><?= Html::encode($model->codigo_producto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nombre_producto') ?></th>
                    <td><?= Html::encode($model->nombre_producto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'iva_incluido') ?></th>
                    <td><?= Html::encode($model->ivaIncluido) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'costo_unitario') ?></th>
                    <td style="text-align: right;"><?= Html::encode(''.number_format($model->costo_unitario,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                   
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_proceso') ?></th>
                    <td><?= Html::encode($model->fecha_proceso) ?></td>
                     
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_creacion') ?></th>
                    <td><?= Html::encode($model->fecha_creacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_punto') ?></th>
                    <td><?= Html::encode($model->punto->nombre_punto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'user_name') ?></th>
                    <td><?= Html::encode($model->user_name) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_marca') ?></th>
                    <td><?= Html::encode($model->marca->marca ?? 'NOT FOUND') ?></td>
                </tr>
                <tr style="font-size: 85%;">
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_barra') ?></th>
                    <td><?= Html::encode($model->codigo_barra) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'stock_unidades') ?></th>
                    <td style="text-align: right;"><?= Html::encode(''.number_format($model->stock_unidades,0)) ?></td>
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'stock_inventario') ?></th>
                    <td style="text-align: right; background-color:#F5EEF8;"><?= Html::encode(''.number_format($model->stock_inventario,0)) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'inventario_inicial') ?></th>
                    <td><?= Html::encode($model->inventarioInicial) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_categoria') ?></th>
                    <td><?= Html::encode($model->categoria->categoria ?? 'NOT FOUND') ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'inventario_inicial') ?></th>
                    <td><?= Html::encode($model->inventarioInicial) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'aplica_inventario') ?></th>
                    <td><?= Html::encode($model->aplicaInventario) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idproveedor') ?></th>
                    <td><?= Html::encode($model->proveedor->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'porcentaje_iva') ?></th>
                    <td><?= Html::encode($model->porcentaje_iva) ?></td>
                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'venta_publico') ?></th>
                    <td><?= Html::encode($model->ventaPublico) ?></td>
                   
                    
                </tr>
                <tr style="font-size: 85%;" >
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'descripcion_producto') ?></th>
                    <td colspan="10"><?= Html::encode($model->descripcion_producto)?></td>
                </tr>
                
            </table>
            <!-- TERMINA TABLE-->
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
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#talla_color" aria-controls="talla_color" role="tab" data-toggle="tab">Tallas y colores <span class="badge"><?= count($talla_color) ?></span></a></li>
             <li role="presentation"><a href="#trasladopunto" aria-controls="trasladopunto" role="tab" data-toggle="tab">Traslado de bodega a punto <span class="badge"><?= count($traslado) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="talla_color">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>                      
                                        <th scope="col" style='background-color:#B9D5CE;'>Talla</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Color</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>User_name</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha registro</th> 
                                         <th scope="col" style='background-color:#B9D5CE;'>Cerrado</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Stock</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad </th> 
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                         <th scope="col" style='background-color:#B9D5CE;'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($talla_color as $val):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $val->id_detalle?></td>
                                            <td style="text-align: right"><?= $val->talla->talla?></td>
                                            <?php if($val->id == null){?>
                                            <td>
                                                
                                                    <!-- Inicio Nuevo Detalle proceso -->
                                                      <?= Html::a('</span> Seleccione el color',
                                                          ['/inventario-punto-venta/seleccionar_color','id' => $model->id_inventario, 'codigo' => $codigo, 'token' => $token, 'id_detalle' => $val->id_detalle],
                                                          [
                                                              'title' => 'Generar los costos y gastos',
                                                              'data-toggle'=>'modal',
                                                              'data-target'=>'#modalgenerarcostogastos',
                                                              'class' => 'btn btn-info btn-xs'
                                                          ])    
                                                          ?>
                                                   <div class="modal remote fade" id="modalgenerarcostogastos" data-backdrop="static">
                                                           <div class="modal-dialog modal-lg-centered">
                                                               <div class="modal-content"></div>
                                                           </div>
                                                   </div>
                                                    
                                            </td>
                                            <?php }else{?>
                                               <td><?= $val->color->color?></td>
                                            <?php } ?>
                                            <td><?= $val->user_name?></td>
                                            <td><?= $val->fecha_registro?></td>
                                            <td><?= $val->cerradoDetalle?></td>
                                            <td style="text-align: right"><?= $val->stock_punto?></td>
                                            <td style="padding-right: 1; padding-right: 1; text-align: right"><input type="text" name="cantidad[]" style="text-align: right" value="<?= $val->cantidad ?>" size="5" > </td> 
                                            <input type="hidden" name="entrada_cantidad[]" value="<?= $val->id_detalle ?>">
                                            <?php if($val->cerrado == 0){?>
                                                
                                                <td style= 'width: 25px; height: 25px;'>    
                                                     <?= Html::a('<span class="glyphicon glyphicon-plus"></span>', ['inventario-punto-venta/crear_linea_color', 'id' => $model->id_inventario, 'id_detalle' => $val->id_detalle, 'token' => $token, 'codigo' => $codigo],['class' => 'btn btn-success btn-xs' ,
                                                        'data' => ['confirm' => 'Esta seguro de crear una nueva linea para un nuevo color?', 'method' => 'post']]);?>
                                                </td>
                                                <td style= 'width: 25px; height: 25px;'>
                                                    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['eliminar_linea', 'id' => $model->id_inventario, 'id_detalle' => $val->id_detalle, 'token' => $token, 'codigo' => $codigo], [
                                                               'class' => '',
                                                               'data' => [
                                                                   'confirm' => 'Esta seguro de eliminar el registro?',
                                                                   'method' => 'post',
                                                               ],
                                                           ])
                                                    ?>
                                                </td>    
                                            <?php }else{ ?>        
                                                <td style= 'width: 25px; height: 25px;'></td>
                                                <td style= 'width: 25px; height: 25px;'></td>
                                           <?php }?>
                                            
                                        </tr>
                                    <?php endforeach;?>
                                  
                                </tbody>      
                            </table>
                            <div class="panel-footer text-right">
                                <?php
                                if($model->aplica_talla_color == 1){
                                    if($model->idordenproduccion == null){
                                        if(count($talla_color) <> count($talla_color_cerrado)){?>
                                           <?= Html::a('<span class="glyphicon glyphicon-search"></span> Crear combinacion', ['inventario-punto-venta/generar_combinacion_talla_color', 'id' => $model->id_inventario, 'token' => $token, 'codigo' => $codigo],[ 'class' => 'btn btn-primary btn-sm']);?>
                                           <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-warning btn-sm", 'name' => 'actualizarlineas']);?>
                                       <?php }else{?>
                                            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear combinacion', ['inventario-punto-venta/generar_combinacion_talla_color', 'id' => $model->id_inventario, 'token' => $token, 'codigo' => $codigo],[ 'class' => 'btn btn-primary btn-sm']) ?>
                                       <?php }
                                    }else{
                                         if(count($talla_color) <> count($talla_color_cerrado)){
                                            echo Html::a('<span class="glyphicon glyphicon-save"></span> Descargar tallas', ['inventario-punto-venta/descargar_tallas_op', 'id' => $model->id_inventario, 'token' => $token, 'codigo' => $codigo, 'idordeproduccion' => $model->idordenproduccion],[ 'class' => 'btn btn-success btn-sm']);?>
                                            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-warning btn-sm", 'name' => 'actualizarlineas']);
                                        }else{
                                           if(count($talla_color_cerrado)> 0){?>
                                               <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear combinacion', ['inventario-punto-venta/generar_combinacion_talla_color', 'id' => $model->id_inventario, 'token' => $token, 'codigo' => $codigo],[ 'class' => 'btn btn-primary btn-sm']);
                                           } else{
                                               echo Html::a('<span class="glyphicon glyphicon-save"></span> Descargar tallas', ['inventario-punto-venta/descargar_tallas_op', 'id' => $model->id_inventario, 'token' => $token, 'codigo' => $codigo, 'idordeproduccion' => $model->idordenproduccion],[ 'class' => 'btn btn-success btn-sm']);
                                           } 
                                            
                                        }    
                                    }   
                                }?>
                            </div>
                        </div>
                       
                    </div>   
                </div>
            </div>
            <!-- TERMINA TABAS-->
            <div role="tabpanel" class="tab-pane" id="trasladopunto">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:90%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>                      
                                        <th scope="col" style='background-color:#B9D5CE;'>Bodega principal</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Punto de  venta destino</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th> 
                                         <th scope="col" style='background-color:#B9D5CE;'>Fecha y hora</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>User name</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($traslado as $val):?>
                                        <tr style='font-size:90%;'>
                                            <td><?= $val->id_traslado?></td>
                                            <td><?= $val->puntoSaliente->nombre_punto?></td>
                                            <td><?= $val->puntoEntrante->nombre_punto?></td>
                                             <td style="text-align: right"><?= $val->unidades?></td>
                                            <td><?= $val->fecha_proceso?></td>
                                            <td><?= $val->fecha_hora_registro?></td>
                                             <td><?= $val->user_name?></td>
                                        </tr>
                                    <?php endforeach;?>
                                  
                                </tbody>      
                            </table>
                        </div>
                    </div>   
                </div>
            </div>
            <!-- TERMINA TABS->
        </div>
    </div>    
  <?php $form->end() ?> 
</div>
  

