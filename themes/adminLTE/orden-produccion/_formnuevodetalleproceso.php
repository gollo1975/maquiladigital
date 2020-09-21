<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Ordenproduccion;
use app\models\Ordenproducciondetalle;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;

$this->title = 'Nuevo Detalle Proceso';
$this->params['breadcrumbs'][] = ['label' => 'Ficha de operaciones detalle', 'url' => ['view_detalle', 'id' => $id ]];
$this->params['breadcrumbs'][] = $iddetalleorden;

?>
    <?php $model = Ordenproduccion::findOne($id); ?>
    <?php $modeldetalle = Ordenproducciondetalle::findOne($iddetalleorden); ?>
    
    <div class="modal-body">
        <p>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view_detalle', 'id' => $id ], ['class' => 'btn btn-primary btn-sm']) ?>
        </p>
        <div class="panel panel-success">
            <div class="panel-heading">
                Operaciones
            </div>
            <div class="panel-body">
                <table class="table table-responsive">
                    <tr style="font-size: 85%;">
                        <th><?= Html::activeLabel($model, 'idordenproduccion') ?></th>
                        <td><?= Html::encode($model->idordenproduccion) ?></td>
                        <th><?= Html::activeLabel($model, 'tipo') ?></th>
                        <td><?= Html::encode($model->tipo->tipo) ?></td>
                        <th><?= Html::activeLabel($model, 'ordenproduccion') ?></th>
                        <td><?= Html::encode($model->ordenproduccion) ?></td>
                    </tr>
                    <tr style="font-size: 85%;">
                        <th><?= Html::activeLabel($model, 'Producto') ?></th>
                        <td><?= Html::encode($modeldetalle->productodetalle->prendatipo->prenda.' / '.$modeldetalle->productodetalle->prendatipo->talla->talla) ?></td>
                        <th><?= Html::activeLabel($model, 'codigo') ?></th>
                        <td><?= Html::encode($modeldetalle->codigoproducto) ?></td>
                        <th><?= Html::activeLabel($model, 'cantidad') ?></th>
                        <td><?= Html::encode($modeldetalle->cantidad) ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <?php $formulario = ActiveForm::begin([
            "method" => "get",
            "action" => Url::toRoute(["orden-produccion/nuevo_detalle_proceso", 'id' => $id, 'iddetalleorden' => $iddetalleorden]),
            "enableClientValidation" => true,
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                            'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                            'labelOptions' => ['class' => 'col-sm-2 control-label'],
                            'options' => []
                        ],

        ]);
        ?>

        <div class="panel panel-success panel-filters">
            <div class="panel-heading">
                Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
            </div>

            <div class="panel-body" id="filtrocliente">
                <div class="row" >
                    <?= $formulario->field($formul, "id")->input("search") ?>
                    <?= $formulario->field($formul, "proceso")->input("search") ?>
                </div>
                <div class="panel-footer text-right">
                    <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
                    <a align="right" href="<?= Url::toRoute(["orden-produccion/nuevo_detalle_proceso", 'id' => $id, 'iddetalleorden' => $iddetalleorden]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
                </div>
            </div>
        </div>

        <?php $formulario->end() ?>
        
        
        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
        ]); ?>
        <div class="table table-responsive">
            <div class="panel panel-success ">
                <div class="panel-heading">
                    Procesos: <span class="badge"><?= $pagination->totalCount ?></span>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Proceso</th>
                            <th scope="col">Duración</th>
                            <th scope="col">Ponderación</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($procesos as $val): ?>
                        <tr style="font-size: 85%;">
                            <td><?= $val->idproceso ?></td>
                            <td><?= $val->proceso ?></td>
                            <td><input type="text" name="duracion[]" value="0" required></td>
                            <td><input type="text" name="ponderacion[]" value="<?= $model->ponderacion ?>" required></td>
                            <td><input type="hidden" name="proceso[]" value="<?= $val->proceso ?>"></td>
                            <td><input type="hidden" name="idproceso[]" value="<?= $val->idproceso ?>"></td>
                        </tr>
                        </tbody>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div class="panel-footer text-right">
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar y Nuevo", ["class" => "btn btn-success btn-sm", 'name' => 'guardarynuevo']) ?>
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm", 'name' => 'guardar']) ?>
                </div>

            </div>
            <?= LinkPager::widget(['pagination' => $pagination]) ?>
        </div>
        
    </div>
<?php ActiveForm::end(); ?>