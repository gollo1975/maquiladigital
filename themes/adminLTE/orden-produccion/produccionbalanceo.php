<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use kartik\date\DatePicker;
use kartik\select2\Select2;

$this->title = 'Balanceo';
$this->params['breadcrumbs'][] = $this->title;

?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtroproceso");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("orden-produccion/produccionbalanceo"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-4 form-group">{input}</div>',
        'labelOptions' => ['class' => 'col-sm-2 control-label'],
        'options' => [ 'tag' => false,]
    ],

]);
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>

    <div class="panel-body" id="filtroproceso" style="display:none">
        <div class="row">
            <?= $formulario->field($form, 'idcliente')->widget(Select2::classname(), [
                'data' => $clientes,
                'options' => ['prompt' => 'Seleccione un cliente...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'idtipo')->widget(Select2::classname(), [
                'data' => $ordenproducciontipos,
                'options' => ['prompt' => 'Seleccione un tipo...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="row" >
            <?= $formulario->field($form, "ordenproduccion")->input("search") ?>
            <?= $formulario->field($form, "codigoproducto")->input("search") ?>
        </div>
        
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("orden-produccion/produccionbalanceo") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>
<?php $formulario->end() ?>
<!--- COMIENZA EL TABS-->
<div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
       
        <li role="presentation" class="active"><a href="#ordenes" aria-controls="ordenes" role="tab" data-toggle="tab">Ordenes: <span class="badge"><?= $pagination->totalCount ?></span></a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="ordenes">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th scope="col" style='background-color:#B9D5CE;'>Op_Int.</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Op-Cliente</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>F. Llegada</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>F. Procesada</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>F. Entrega</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Servicio</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Unid.</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Estado</th>
                                    <th scope="col" style='background-color:#B9D5CE;'></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($model as $val): ?>
                                <tr style="font-size: 85%;">
                                    <td><?= $val->idordenproduccion ?></td>
                                    <td><?= $val->ordenproduccion ?></td>
                                      <td><?= $val->codigoproducto ?></td>
                                    <td><?= $val->cliente->nombrecorto ?></td>
                                    <td><?= date("Y-m-d", strtotime("$val->fechallegada")) ?></td>
                                    <td><?= date("Y-m-d", strtotime("$val->fechaprocesada")) ?></td>
                                    <td><?= date("Y-m-d", strtotime("$val->fechaentrega")) ?></td>
                                    <?php if($val->idtipo == 1){?>
                                        <td style='background-color:#A1D2D8;'><?= $val->tipo->tipo ?></td>
                                    <?php }else{?>
                                        <td style='background-color:#F5DB90;'><?= $val->tipo->tipo ?></td>
                                    <?php }?>    
                                    <td align="right"><?= ''.number_format($val->cantidad,0) ?></td>
                                    <?php if($val->faltante == $val->cantidad){?>
                                    <td style="font-size: 85%;background: #3B9785; color: #FFFFFF;"><?php echo 'PRODUCCION'?></td>
                                    <?php }else{
                                            if($val->faltante < $val->cantidad && $val->cerrar_orden == 0){?>
                                                <td style="font-size: 85%;background: #316941; color: #FFFFFF;"><?php echo 'EN CONFECCION'?></td>
                                            <?php }else{?>
                                                    <td style="font-size: 85%;background: #D5F2E7; color: #0A3664;"><?php echo 'TERMINADO'?></td>
                                            <?php }
                                    }?>   
                                    <td style="width: 25px;">
                                        <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span> ', ['view_balanceo', 'id' => $val->idordenproduccion] ) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>    
        </div>
        <!-- TERMINA EL TABS-->
    </div>    
</div>    

<?= LinkPager::widget(['pagination' => $pagination]) ?>
