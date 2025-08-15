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
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$this->title = 'Eficiencia individual ('. $model->planta->nombre_planta. ')-(Referencia: '.$orden->codigoproducto.')';
$this->params['breadcrumbs'][] = $this->title;

?>
<?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
    <?php if ($model->autorizado == 0) { ?>
        <?= Html::a('<span class="glyphicon glyphicon-ok"></span> autorizado', ['valor-prenda-unidad/autorizado', 'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $model->tipo_proceso_pago, 'tokenPlanta' => $tokenPlanta], ['class' => 'btn btn-success btn-sm']);?>
        <div class="btn-group btn-sm" role="group">
            <button type="button" class="btn btn-warning  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               Crear hora y fechas
               <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                    <li> <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Hora corte individual',
                        ['/valor-prenda-unidad/crear_hora_corte','id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion,'id_planta' => $id_planta, 'tipo_pago' => $model->tipo_proceso_pago, 'tokenPlanta' => $tokenPlanta],
                          ['title' => 'Crear hora de corte para ingresar las operaciones',
                           'data-toggle'=>'modal',
                           'data-target'=>'#modalcrearhoracorte',
                           'class' => ''
                          ]) ?>
                     </li>
                    <li> <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Hora corte APP',
                        ['/valor-prenda-unidad/crear_hora_corte_app','id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion,'id_planta' => $id_planta, 'tipo_pago' => $model->tipo_proceso_pago, 'tokenPlanta' => $tokenPlanta],
                          ['title' => 'Crear hora de corte para ingresar las operaciones',
                           'data-toggle'=>'modal',
                           'data-target'=>'#modalcrearhoracorteapp',
                           'class' => ''
                          ]) ?>
                     </li>
                      
            </ul>
        </div>
        <div class="btn-group btn-sm" role="group">
            <button type="button" class="btn btn-info  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               Editar horas y fechas
               <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li> <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar hora corte individual',
                        ['/valor-prenda-unidad/editar_hora_corte','id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion,'id_planta' => $id_planta, 'tipo_pago' => $model->tipo_proceso_pago, 'tokenPlanta' => $tokenPlanta],
                          ['title' => 'Permite  editar el ultimo de corte de ingreso.',
                           'data-toggle'=>'modal',
                           'data-target'=>'#modaleditarhoracorte',
                           'class' => ''
                          ])    
                    ?>
                 </li>
                <li> <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar hora corte APP',
                        ['/valor-prenda-unidad/editar_hora_corte_app','id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion,'id_planta' => $id_planta, 'tipo_pago' => $model->tipo_proceso_pago, 'tokenPlanta' => $tokenPlanta],
                          ['title' => 'Permite  editar el ultimo de corte de ingreso.',
                           'data-toggle'=>'modal',
                           'data-target'=>'#modaleditarhoracorteapp',
                           'class' => ''
                          ])    
                    ?>
                 </li>
                      
            </ul>
       
        </div>  
    <?php } else { 
             if ($model->cerrar_pago == 0) { ?>
               <?= Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion,'id_planta' => $id_planta, 'tipo_pago' => $model->tipo_proceso_pago, 'tokenPlanta' => $tokenPlanta], ['class' => 'btn btn-default btn-sm']);?>
                <?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar pago', ['cerrarpago', 'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $model->tipo_proceso_pago, 'tokenPlanta' => $tokenPlanta],['class' => 'btn btn-warning btn-xs',
                'data' => ['confirm' => 'Esta seguro de cerrar el proceso de pago Nro : '. $model->id_valor. '', 'method' => 'post']]);?>
                <?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar pago-Orden', ['cerrarpagoorden', 'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $model->tipo_proceso_pago, 'tokenPlanta' => $tokenPlanta],['class' => 'btn btn-info btn-xs',
                'data' => ['confirm' => 'Esta seguro de cerrar el proceso de pago Nro : '. $model->id_valor. ' y la orden de producción Nro: '.$model->idordenproduccion.'', 'method' => 'post']]);
             }    
    }?>

    <div class="modal remote fade" id="modalcrearhoracorte" data-backdrop="static">
         <div class="modal-dialog modal-lg" style ="width: 450px;">    
             <div class="modal-content"></div>
         </div>
    </div>
  <div class="modal remote fade" id="modalcrearhoracorteapp" data-backdrop="static">
         <div class="modal-dialog modal-lg" style ="width: 450px;">    
             <div class="modal-content"></div>
         </div>
    </div>
    <div class="modal remote fade" id="modaleditarhoracorte" data-backdrop="static">
         <div class="modal-dialog modal-lg" style ="width: 450px;">    
             <div class="modal-content"></div>
         </div>
    </div>
    <div class="modal remote fade" id="modaleditarhoracorteapp" data-backdrop="static">
         <div class="modal-dialog modal-lg" style ="width: 450px;">    
             <div class="modal-content"></div>
         </div>
    </div>
       
<div class="table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Tallas <span class="badge"> <?= count($conTallas)?></span>
        </div>                       
        <table class="table table-responsive-lg">
            <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Descripcion</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Cant.</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Operaciones</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Confeccion</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Costo</th>
                    <th scope="col" style='background-color:#B9D5CE;'></th>
                    <th scope="col" style='background-color:#B9D5CE;'></th>
                    <th scope="col" style='background-color:#B9D5CE;'></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($conTallas as $val):?>
                        <tr style="font-size: 85%;">
                            <td><?= $val->codigoproducto ?></td>
                            <td><?= $val->productodetalle->prendatipo->prenda ?></td>
                            <td><?= $val->productodetalle->prendatipo->talla->talla ?></td>
                            <td style="text-align: right"><?= ''.number_format($val->cantidad,0) ?></td> 
                            <td style="text-align: right"><?= ''.number_format($val->cantidad_operaciones,0) ?></td>
                            <td style="text-align: right"><?= ''.number_format($val->cantidad_confeccionada,0) ?></td>
                            <td style="text-align: right"><?= ''.number_format($val->costo_confeccion,0) ?></td>
                            <?php if($model->autorizado == 0){?>
                                <td style= 'width: 15px; height: 10px;'>
                                    <a href="<?= Url::toRoute(["valor-prenda-unidad/view_search_operaciones", "id" => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion, 'id_planta' =>$model->id_planta, 'codigo' => $val->codigoproducto, 'tokenPlanta' =>$tokenPlanta, 'tipo_pago' => $tipo_pago]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                </td>
                                <td style= 'width: 15px; height: 10px;'>
                                    <a href="<?= Url::toRoute(["valor-prenda-unidad/cantidad_talla_confeccion", "id" => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion, 'id_planta' =>$model->id_planta,'id_detalle' => $val->iddetalleorden, 'tokenPlanta' =>$tokenPlanta, 'tipo_pago' => $tipo_pago]) ?>" ><span class="glyphicon glyphicon-list"></span></a>
                                </td>
                                <td style= 'width: 15px; height: 10px;'>
                                    <a href="<?= Url::toRoute(["valor-prenda-unidad/view", "id" => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion, 'id_planta' =>$model->id_planta, 'tipo_pago' => $model->tipo_proceso_pago]) ?>" ><span class="glyphicon glyphicon-send"></span></a>
                                </td>   
                            <?php }else{?>
                                <td style= 'width: 15px; height: 10px;'></td>
                                <td style= 'width: 15px; height: 10px;'>
                                    <a href="<?= Url::toRoute(["valor-prenda-unidad/cantidad_talla_confeccion", "id" => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion, 'id_planta' =>$model->id_planta,'id_detalle' => $val->iddetalleorden, 'tokenPlanta' =>$tokenPlanta, 'tipo_pago' => $tipo_pago]) ?>" ><span class="glyphicon glyphicon-list"></span></a>
                                </td>
                                <td style= 'width: 15px; height: 10px;'></td>
                            <?php }?>    
                        </tr>     
                 <?php endforeach;?>
            </tbody>  
        </table>
           
    </div>
</div>    

