<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Ordenproducciontipo;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'INGRESO DE PRODUCCION';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
    $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<div class="table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Registros  <span class="badge"> <?= count($modelo)?></span>
        </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:90%;'>                
                    <th scope="col" style='background-color:#B9D5CE;'>ID</th>
                    <th scope="col" style='background-color:#B9D5CE;'>OP_INTERNA</th>
                    <th scope="col" style='background-color:#B9D5CE;'>REFERENCIA</th>
                    <th scope="col" style='background-color:#B9D5CE;'>CLIENTE</th>
                    <th scope="col" style='background-color:#B9D5CE;'></th>

                </tr>
            </thead>
            <tbody>
                <?php 
                if($modelo){ 
                    foreach ($modelo as $val):?>
                        <tr style='font-size:90%;'>  
                            <td><?= $val->id_valor ?></td>
                            <td><?= $val->idordenproduccion ?></td>
                            <td><?= $val->ordenproduccion->codigoproducto ?></td>
                            <td><?= $val->ordenproduccion->cliente->nombrecorto ?></td>
                            <td style= 'width: 25px; height: 25px;'>
                                    <a href="<?= Url::toRoute(["valor-prenda-unidad/view_produccion", "id" => $val->id_valor, 'idordenproduccion' => $val->idordenproduccion, 'id_planta' =>$id_planta, 'tokenOperario' => $tokenOperario]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                            </td>


                    <?php endforeach; 
                } ?>
             </tbody>                               
        </table>    
    </div>
</div>
