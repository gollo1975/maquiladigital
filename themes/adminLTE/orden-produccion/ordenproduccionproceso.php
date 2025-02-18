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

$this->title = 'Ficha de Operaciones';
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
    "action" => Url::toRoute("orden-produccion/proceso"),
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
            <?= $formulario->field($form, "orden")->input("search") ?>
            <?= $formulario->field($form, "codigoproducto")->input("search") ?>
        </div>
        
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("orden-produccion/proceso") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Registros: <span class="badge"><?= $pagination->totalCount ?></span>
        </div>
        <table class="table table-bordered table-hover">
            <thead>
            <tr style="font-size: 85%;">
                <th scope="col" style='background-color:#B9D5CE;'>Op Int.</th>
                <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                <th scope="col" style='background-color:#B9D5CE;'>Op Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. llegada</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. procesada</th>
                <th sscope="col" style='background-color:#B9D5CE;'>Tipo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Progreso</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
            <tr style="font-size: 85%;">
                <td><?= $val->idordenproduccion ?></td>
                <td><?= $val->codigoproducto ?></td>
                <td><?= $val->ordenproduccion ?></td>
                  <td><?= $val->cantidad ?></td>
                <td><?= $val->cliente->nombrecorto ?></td>
                <td><?= date("Y-m-d", strtotime("$val->fechallegada")) ?></td>
                <td><?= date("Y-m-d", strtotime("$val->fechaprocesada")) ?></td>
                <td  style='background-color:<?= $val->tipo->color?>; color: black;'><?= $val->tipo->tipo ?></td>
                <td><?= 'Proceso '.'<b>'.round($val->porcentaje_proceso,1).' % - </b>Cantidad '.'<b>'.round($val->porcentaje_cantidad,1).' %' ?></td>
                <td style="width: 20px; height: 20px">
                    <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span> ', ['view_detalle', 'id' => $val->idordenproduccion] ) ?>
                </td>
                <td style="width: 20px; height: 20px">
                    <?= Html::a('<span class="glyphicon glyphicon-list"></span> ',
                                        ['orden-produccion/ver_informacion', 'id' => $val->idordenproduccion],
                                        [
                                            'class' => '',   
                                            'title' => 'Ver mas informacion de la OP',
                                            'data-toggle'=>'modal',
                                            'data-target'=>'#modalverinformacion'.$val->idordenproduccion,
                                        ])    
                                   ?>
                    <div class="modal remote fade" id="modalverinformacion<?= $val->idordenproduccion?>">
                        <div class="modal-dialog modal-lg" style ="width: 650px;">
                            <div class="modal-content"></div>
                        </div>
                    </div>
                </td>    
            </tr>
            </tbody>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>
