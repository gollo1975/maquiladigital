 <?php

//modelos
use app\models\Ordenproducciondetalle;
use app\models\Ordenproduccion;
use app\models\Cliente;
use app\models\Color;
use app\models\Remision;
use app\models\Producto;
use app\models\Productodetalle;
//clase
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

$this->title = 'Trazabilidad OP';
$this->params['breadcrumbs'][] = ['label' => 'Ordenes de Producción', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idordenproduccion;
$view = 'orden-produccion';
?>
<div class="ordenproduccion-view_trazabilidad">
    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['trazabilidad_ordenes'], ['class' => 'btn btn-primary btn-sm']) ?>
    </p>        
          
    <div class="panel panel-success">
        <div class="panel-heading">
            Orden de Producción
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, "idordenproduccion") ?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idcliente') ?>:</th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigoproducto') ?></th>
                    <td style="background-color: <?= $model->tipo->color?>"><?= Html::encode($model->codigoproducto) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Unidades') ?>:</th>
                     <td style="text-align: right"><?= Html::encode(''.number_format($model->cantidad,0)) ?></td>
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
    <!-- comienza los tabs -->
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#detalle_orden" aria-controls="detalle_orden" role="tab" data-toggle="tab">Tallas de la op <span class="badge"><?= count($detalle_orden) ?></span></a></li>
            <li role="presentation"><a href="#listado_operaciones" aria-controls="listado_operaciones" role="tab" data-toggle="tab">Operaciones <span class="badge"><?= 1 ?></span></a></li>
            <li role="presentation"><a href="#listado_modulos" aria-controls="listado_modulos" role="tab" data-toggle="tab">Modulos <span class="badge"><?= 1 ?></span></a></li>
        </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="detalle_orden">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Planta</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Descripcion y talla</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Unidades lote</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Confeccionadas</th>
                                            <th scope="col" style='background-color:#B9D5CE;'>Faltan x confeccionar</th>
                                            <th scope="col" style='background-color:#B9D5CE;'></th>
                                        </tr>
                                    </thead>    
                                    <body>
                                        <?php foreach ($detalle_orden as $val): ?>
                                            <tr style="font-size: 85%;">
                                                <td><?= $val->iddetalleorden ?></td>
                                                <td style="background-color: <?= $val->plantaProduccion->nombre_color?> "><?= $val->plantaProduccion->nombre_planta ?></td>
                                                <td><?= $val->productodetalle->prendatipo->prenda.' / '.$val->productodetalle->prendatipo->talla->talla   ?></td>
                                                <td style="text-align: right"><?= $val->codigoproducto ?></td>
                                                <td style="text-align: right; background-color: #83c5be; color: black"><?= ''. number_format($val->cantidad,0) ?></td>
                                                <td style="text-align: right; background-color: #d8e2dc; color: black"><?= ''. number_format($val->cantidad_operada,0) ?></td>
                                                <td style="text-align: right; background-color: #fcd5ce; color: black"><?= ''. number_format($val->cantidad - $val->cantidad_operada,0) ?></td>
                                                <td style="width: 20px; height: 20px;">
                                                        <!-- Inicio Nuevo Detalle proceso -->
                                                          <?= Html::a('<span class="glyphicon glyphicon-user"></span> ',
                                                              ['/orden-produccion/mostrar_operarios_talla', 'id' => $model->idordenproduccion, 'id_detalle_talla' => $val->iddetalleorden],
                                                              [
                                                                  'title' => 'Permite mostrar todos los operarios que estan en esta talla',
                                                                  'data-toggle'=>'modal',
                                                                  'data-target'=>'#modalmostraroperariotalla'.$model->idordenproduccion,
                                                                  'class' => '',
                                                                  'data-backdrop' => 'static',

                                                              ])    
                                                         ?>
                                                </td> 
                                                 <div class="modal remote fade" id="modalmostraroperariotalla<?= $model->idordenproduccion ?>">
                                                          <div class="modal-dialog modal-lg" style ="width: 1000px;">
                                                              <div class="modal-content"></div>
                                                          </div>
                                                      </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </body>          
                                </table>    
                            </div>  
                        </div>
                    </div>    
                </div>
              
                <!-- TERMINA TABS DE DETALLE -->
              
            </div>  
    </div>
  <?php ActiveForm::end(); ?>  
</div>


   