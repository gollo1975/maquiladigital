<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\CostoFijo;
use app\models\CostoFijoDetalle;
use app\models\Arl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Session;
use yii\db\ActiveQuery;



use yii\widgets\LinkPager;




/* @var $this yii\web\View */
/* @var $model app\models\CostoFijo */
/* @var $form yii\widgets\ActiveForm */
$plantas_de_produccion = ArrayHelper::map(\app\models\PlantaEmpresa::find()->all(), 'id_planta', 'nombre_planta');

?>

<?php
$this->title = 'Costo Fijo';

$this->params['breadcrumbs'][] = $this->title;
?>


<div class="panel panel-success">
    <div class="panel-heading">
        Costo Fijo
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
            <tr>
                <th><?= Html::activeLabel($costofijo, 'Total') ?>:</th>
                <td><?= Html::encode('$ ' . number_format($costofijo->valor)) ?></td>                               
            </tr>                       
        </table>
    </div>
</div>

<div class="panel panel-success ">
    <div class="panel-heading">
        Detalle Costo Fijo
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr style="font-size: 90%">
                    <th scope="col">Item</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Valor</th>  
                    <th scope="col">Sucursal</th>  
                    <th scope="col">Aplica</th>  
                    <th scope="col"></th>                    
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach ($costofijodetalle as $val): 
                    $planta = \app\models\PlantaEmpresa::findOne($val->id_planta);
                    ?>
                   <tr style="font-size: 90%">                                         
                        <td><?= $i ?></td>
                        <td><?= $val->descripcion ?></td>
                        <td><?= '$ ' . number_format($val->valor) ?></td>      
                        <?php if($val->id_planta != null){?>
                        <td style="background-color: <?= $planta->nombre_color;?>;"><?= $val->plantaEmpresa->nombre_planta  ?></td>
                        <?php }else{ ?>
                             <td><?= 'NOT FOUND'  ?></td>
                        <?php }?>  
                         <td><?= $val->aplicaConcepto ?></td>     
                        <td>
                        <a href="#" data-toggle="modal" data-target="#iddetalle<?= $val->id_detalle_costo_fijo ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                                <!-- Editar modal detalle -->
                                <div class="modal fade" role="dialog" aria-hidden="true" id="iddetalle<?= $val->id_detalle_costo_fijo ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                <h4 class="modal-title">Editar Costo Fijo</h4>
                                            </div>
                                            <?= Html::beginForm(Url::toRoute("costo-fijo/editardetalle"), "POST") ?>
                                            <div class="modal-body">
                                                <div class="panel panel-success">
                                                    <div class="panel-heading">
                                                        <h4>Información </h4>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-lg-2">
                                                                <label>Descripción:</label>
                                                            </div>
                                                            <div class="col-lg-8">
                                                                <input type="text" name="descripcion" class="form-control" value="<?= $val->descripcion ?>" required>
                                                            </div>
                                                        </div>
                                                       <div class="row">
                                                            <div class="col-lg-2">
                                                                <label for="planta-id">Planta:</label>
                                                            </div>
                                                            <div class="col-lg-8">
                                                                <select name="id_planta" id="planta-id" class="form-control">
                                                                    <option value="">Selecciona una planta...</option>
                                                                    <?php foreach ($plantas_de_produccion as $id => $nombre): ?>
                                                                        <option value="<?= $id; ?>" <?= ($val->id_planta == $id) ? 'selected' : ''; ?>>
                                                                            <?= $nombre; ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-2">
                                                                <label>Valor:</label>
                                                            </div>
                                                            <div class="col-lg-8">
                                                                <input type="text" name="valor" class="form-control" value="<?= $val->valor ?>" onkeypress="return esInteger(event)" required>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-2">
                                                                <label for="aplica-concepto">Aplica:</label>
                                                            </div>
                                                            <div class="col-lg-8">
                                                                <input type="checkbox" name="aplica_concepto" id="aplica-concepto" value="1" <?= ($val->aplica_concepto == 1) ? 'checked' : ''; ?>>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="id_costo_fijo" value="<?= $costofijo->id_costo_fijo ?>">
                                                        <input type="hidden" name="id_detalle_costo_fijo" value="<?= $val->id_detalle_costo_fijo ?>">
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
                        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['eliminar', 'id' => $costofijo->id_costo_fijo, 'iddetalle' => $val->id_detalle_costo_fijo], [
                                'class' => '',
                                'data' => [
                                    'confirm' => 'Esta seguro de eliminar el registro?',
                                    'method' => 'post',
                                ],
                            ])
                            ?>
                        </td>
                    </tr>
                </tbody>
                <?php $i++; ?>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="panel-footer text-right">        
        <a href="#" data-toggle="modal" data-target="#<?= $costofijo->id_costo_fijo ?>" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Nuevo</a>
        <!-- Nuevo modal detalle -->
        <div class="modal fade" role="dialog" aria-hidden="true" id="<?= $costofijo->id_costo_fijo ?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" align="left">Nuevo Costo Fijo</h4>
                    </div>
                    <?= Html::beginForm(Url::toRoute("costo-fijo/nuevodetalle"), "POST") ?>
                        <div class="modal-body">
                            <div class="panel panel-success">
                                <div class="panel-heading" align="left">
                                    <h4>Información del registro </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <label>Descripción:</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <input type="text" name="descripcion" class="form-control" value="" autofocus required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <label for="planta-produccion">Planta:</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <select name="id_planta" id="planta-produccion" class="form-control">
                                                <option value="">Selecciona una planta...</option>
                                                <?php foreach ($plantas_de_produccion as $id => $nombre): ?>
                                                    <option value="<?= $id; ?>"><?= $nombre; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <label>Valor:</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <input type="text" name="valor" class="form-control" value="0" onkeypress="return esInteger(event)" required>
                                        </div>
                                    </div>
                                   <div class="row">
                                        <div class="col-lg-2">
                                            <label for="aplica-concepto">Aplica:</label>
                                        </div>
                                        <div class="col-lg-8 text-left">
                                            <input type="checkbox" name="aplica_concepto" id="aplica-concepto">
                                        </div>
                                    </div>
                                    <input type="hidden" name="id_costo_fijo" value="<?= $costofijo->id_costo_fijo ?>">                                
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
        
    </div>
</div>

<script type="text/javascript">
    function esInteger(e) {
        var charCode
        charCode = e.keyCode
        status = charCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false
        }
        return true
    }
</script>