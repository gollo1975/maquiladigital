<?php


use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Notacredito;
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
use app\models\Notacreditodetalle;
use app\models\Cliente;
use app\models\Producto;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;


/* @var $this yii\web\View */
/* @var $model app\models\Notacredito */

$this->title = 'Detalle Nota de Crédito';
$this->params['breadcrumbs'][] = ['label' => 'Notas Créditos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idnotacredito;
$view = 'notacredito';
?>
<div class="notacredito-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->idnotacredito], ['class' => 'btn btn-primary']) ?>
        <?php if ($model->autorizado == 0) { ?>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->idnotacredito], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->idnotacredito], ['class' => 'btn btn-default']) ?>
        <?php }
            else {
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->idnotacredito], ['class' => 'btn btn-default']);
                echo Html::a('<span class="glyphicon glyphicon-check"></span> Generar', ['notacredito', 'id' => $model->idnotacredito], ['class' => 'btn btn-default']);                
                if ($model->numero > 0){
                    echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimir', 'id' => $model->idnotacredito], ['class' => 'btn btn-default']);            
                                                                             
                }
            }
        ?>
    </p>

    <div class="panel panel-success">
        <div class="panel-heading">
            Nota crédito
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th><?= Html::activeLabel($model, 'idnotacredito') ?>:</th>
                    <td><?= Html::encode($model->idnotacredito) ?></td>
                    <th><?= Html::activeLabel($model, 'Cliente') ?>:</th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th><?= Html::activeLabel($model, 'Subtotal') ?>:</th>
                    <td align="right"><?= Html::encode('$ '.number_format($model->valor,0)) ?></td>
                </tr>
                <tr>
                    <th><?= Html::activeLabel($model, 'numero') ?>:</th>
                    <td><?= Html::encode($model->numero) ?></td>
                    <th><?= Html::activeLabel($model, 'fecha') ?>:</th>
                    <td><?= Html::encode($model->fecha) ?></td>
                    <th><?= Html::activeLabel($model, 'Iva') ?>: +</th>
                    <td align="right"><?= Html::encode('$ '.number_format($model->iva,0)) ?></td>
                </tr>
                <tr>
                    <th><?= Html::activeLabel($model, 'autorizado') ?>:</th>
                    <td><?= Html::encode($model->autorizado) ?></td>
                    <th><?= Html::activeLabel($model, 'usuariosistema') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                    <th><?= Html::activeLabel($model, 'ReteIva') ?>: -</th>
                    <td align="right"><?= Html::encode('$ '.number_format($model->reteiva,0)) ?></td>
                </tr>
                <tr>
                    <th><?= Html::activeLabel($model, 'idconceptonota') ?>:</th>
                    <td><?= Html::encode($model->conceptonota->concepto) ?></td>
                    <th></th>
                    <td></td>
                    <th><?= Html::activeLabel($model, 'ReteFuente') ?>: -</th>
                    <td align="right"><?= Html::encode('$ '.number_format($model->retefuente,0)) ?></td>
                </tr>
                <tr>
                    <th><?= Html::activeLabel($model, 'fechapago') ?>:</th>
                    <td><?= Html::encode($model->fechapago) ?></td>
                    <th></th>
                    <td></td>
                    <th><?= Html::activeLabel($model, 'Total') ?>:</th>
                    <td align="right"><?= Html::encode('$ '.number_format($model->total,0)) ?></td>
                </tr>
                <tr>
                    <th><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan="5"><?= Html::encode($model->observacion) ?></td>
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
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Id</th>                        
                        <th scope="col">Nro Factura</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Valor</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($modeldetalles as $val): ?>
                    <tr>
                        <td><?= $val->iddetallenota ?></td>                        
                        <td><?= $val->nrofactura ?></td>
                        <td><?= $val->factura->cliente->nombrecorto ?></td>
                        <td><?= '$ '.number_format($val->valor,0) ?></td>
                        <?php if ($model->autorizado == 0) { ?>
                            <td>
                                <a href="#" data-toggle="modal" data-target="#iddetallenota2<?= $val->iddetallenota ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                                <!-- Editar modal detalle -->
                                <div class="modal fade" role="dialog" aria-hidden="true" id="iddetallenota2<?= $val->iddetallenota ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                <h4 class="modal-title">Editar detalle</h4>
                                            </div>
                                            <?= Html::beginForm(Url::toRoute("notacredito/editardetalle"), "POST") ?>
                                            <div class="modal-body">
                                                <div class="panel panel-success">
                                                    <div class="panel-heading">
                                                        <h4>Información Nota Crédito Detalle</h4>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="col-lg-4">
                                                            <label>Valor:</label>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <input type="text" name="valor" value="<?=  $val->valor ?>" class="form-control" required>
                                                        </div>
                                                        <input type="hidden" name="iddetallenota" value="<?= $val->iddetallenota ?>">
                                                        <input type="hidden" name="idnotacredito" value="<?= $val->idnotacredito ?>">
                                                        <input type="hidden" name="total" value="<?= $val->valor ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Guardar</button>
                                            </div>
                                            <?= Html::endForm() ?>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                                <!-- Eliminar modal detalle -->
                                <a href="#" data-toggle="modal" data-target="#iddetallenota<?= $val->iddetallenota ?>"><span class="glyphicon glyphicon-trash"></span></a>
                                <div class="modal fade" role="dialog" aria-hidden="true" id="iddetallenota<?= $val->iddetallenota ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                <h4 class="modal-title">Eliminar Detalle</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Realmente deseas eliminar el registro con código <?= $val->iddetallenota ?>?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <?= Html::beginForm(Url::toRoute("notacredito/eliminardetalle"), "POST") ?>
                                                <input type="hidden" name="iddetallenota" value="<?= $val->iddetallenota ?>">
                                                <input type="hidden" name="idnotacredito" value="<?= $model->idnotacredito ?>">
                                                <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Eliminar</button>
                                                <?= Html::endForm() ?>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            </td>
                        <?php } ?>
                    </tr>
                    </tbody>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php if ($model->autorizado == 0) { ?>
                <div class="panel-footer text-right">
                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nuevo', ['notacredito/nuevodetalles', 'idnotacredito' => $model->idnotacredito,'idcliente' => $model->idcliente], ['class' => 'btn btn-success']) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['notacredito/editardetalles', 'idnotacredito' => $model->idnotacredito],[ 'class' => 'btn btn-success']) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['notacredito/eliminardetalles', 'idnotacredito' => $model->idnotacredito], ['class' => 'btn btn-danger']) ?>
                </div>
            <?php } ?>
        </div>
    </div>


</div>