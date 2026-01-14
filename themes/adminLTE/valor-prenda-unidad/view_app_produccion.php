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

$this->title = 'LISTADO DE TALLAS (Referencia: ('.$model->ordenproduccion->codigoproducto.')';
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['ingreso_eficiencia_empleado'], ['class' => 'btn btn-primary btn-sm']) ?>
    
</p>    
<div class="table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Tallas <span class="badge"> <?= count($detalle_orden)?></span>
        </div>                       
         <table class="table table-responsive-lg">
            <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Descripcion</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Faltan</th>
                    <th scope="col" style='background-color:#B9D5CE;'></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($detalle_orden as $val):
                        $total_Faltante = $val->cantidad_operaciones - $val->cantidad_confeccionada;
                        ?>
                        <tr style="font-size: 90%;">
                            <td><?= $val->productodetalle->prendatipo->prenda ?></td>
                            <td><?= $val->productodetalle->prendatipo->talla->talla ?></td>
                            <td style="text-align: center"><?= ''.number_format($val->cantidad,0) ?></td> 
                            <?php if($total_Faltante > 0){?>
                                <td style="text-align: center"><?= ''.number_format($total_Faltante,0) ?></td>
                            <?php }else{?>
                                <td style="text-align: center; background-color: #B9D5CE"><?= ''.number_format($total_Faltante,0) ?></td>
                            <?php }?>    
                            <?php if($total_Faltante > 0){?>
                                <td style= 'width: 15px; height: 10px;'>
                                    <a href="<?= Url::toRoute(["valor-prenda-unidad/entrada_operacion_talla", "id" => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta,  'tokenOperario' =>$tokenOperario, 'id_detalle' => $val->iddetalleorden]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                </td>
                            <?php }else{?>
                                <td style= 'width: 15px; height: 10px;'></td>
                            <?php }?>    
                        </tr>     
                 <?php endforeach;?>
            </tbody>  
        </table>
           
    </div>
</div>    

