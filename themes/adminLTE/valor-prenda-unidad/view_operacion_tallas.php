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

$this->title = 'Consulta de tallas';
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?php if($token == 1){
        echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['search_operacion_talla'], ['class' => 'btn btn-primary btn-sm']);
    }else{
       echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']); 
    }?>
</p>
<div class="table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Tallas <span class="badge"> <?= count($detalle_orden)?></span>
        </div>     
             <div class="panel-body">
                    <table class="table table-bordered table-hover">
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

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                                foreach ($detalle_orden as $val):
                                    $total += $val->costo_confeccion;
                                    ?>
                                    <tr style="font-size: 85%;">
                                        <td><?= $val->codigoproducto ?></td>
                                        <td><?= $val->productodetalle->prendatipo->prenda ?></td>
                                        <td><?= $val->productodetalle->prendatipo->talla->talla ?></td>
                                        <td style="text-align: right"><?= ''.number_format($val->cantidad,0) ?></td> 
                                        <td style="text-align: right"><?= ''.number_format($val->cantidad_operaciones,0) ?></td>
                                        <td style="text-align: right"><?= ''.number_format($val->cantidad_confeccionada,0) ?></td>
                                        <td style="text-align: right"><?= ''.number_format($val->costo_confeccion,0) ?></td>
                                        <td style= 'width: 15px; height: 10px;'>
                                        <a href="<?= Url::toRoute(["valor-prenda-unidad/listado_operaciones", "id" => $id, 'id_detalle' => $val->iddetalleorden, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                        </td>

                                    </tr>     
                             <?php endforeach;?>
                        </tbody>  
                        <tr style="font-size: 90%;">
                        <td colspan="5"></td>
                        <td style="text-align: right;  background-color:#F0F3EF;"><b>TOTAL SERVICIO:</b></td>
                        <td align="right" style="background-color:#F0F3EF" ><b><?= '$ ' . number_format($total, 0); ?></b></td>
                        <td></td>
                        </tr>
                    </table>
                </div>
       </div>       
</div>    

