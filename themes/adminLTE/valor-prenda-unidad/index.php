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

$this->title = 'Valor de prenda';
$this->params['breadcrumbs'][] = $this->title;
?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtro");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("valor-prenda-unidad/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$tipo = ArrayHelper::map(Ordenproducciontipo::find()->orderBy('idtipo ASC')->all(), 'idtipo', 'tipo');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "idordenproduccion")->input("search") ?>
             <?= $formulario->field($form, 'idtipo')->widget(Select2::classname(), [
                'data' => $tipo,
                'options' => ['prompt' => 'Seleccione el servicio'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'estado_valor')->dropDownList(['' => 'TODOS', '1' => 'CERRADO', '0' => 'ABIERTO'],['prompt' => 'Seleccione el estado ...']) ?>
             <?= $formulario->field($form, 'autorizado')->dropDownList(['' => 'TODOS', '1' => 'SI', '0' => 'NO'],['prompt' => 'Seleccione el estado ...']) ?>
             <?= $formulario->field($form, 'cerrar_pago')->dropDownList(['' => 'TODOS', '1' => 'SI', '0' => 'NO'],['prompt' => 'Seleccione el estado ...']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>
<?php
    $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros: <span class="badge"> <?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>                
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>OP_In</th>
                  <th scope="col" style='background-color:#B9D5CE;'>Cod_Prod.</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>Servicio</th>
                  <th scope="col" style='background-color:#B9D5CE;'>Cant.</th>
                <th scope="col" style='background-color:#B9D5CE;'>Vr. Vinculado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Vr. Contrato</th>
                <th scope="col" style='background-color:#B9D5CE;'>T. Pagar</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. Proceso</th>
                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Autorizado" >Aut.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Cerrado" >Cer.</span></th>
                 <th scope="col" style='background-color:#B9D5CE;'><span title="Activo" >Act.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
              
            </tr>
            </thead>
            <tbody>
            <?php 
             
            foreach ($modelo as $val):?>
                <tr style='font-size:85%;'>  
                <?php if($val->cerrar_pago == 0){?>   
                    <td><?= $val->id_valor ?></td>
                    <td><?= $val->idordenproduccion ?></td>
                      <td><?= $val->ordenproduccion->codigoproducto ?></td>
                    <td><?= $val->ordenproduccion->cliente->nombrecorto ?></td>
                    <td><?= $val->tipo->tipo?></td>
                    <td align="right"><?= ''.number_format($val->cantidad,0) ?></td>
                    <td align="right"><?= ''.number_format($val->vlr_vinculado,0) ?></td>
                    <td align="right"><?= ''.number_format($val->vlr_contrato,0) ?></td>
                    <td align="right"><?= ''.number_format($val->total_pagar,0) ?></td>
                     <td><?= $val->fecha_proceso ?></td>
                    <td><?= $val->usuariosistema ?></td>
                    <td><?= $val->autorizadoPago?></td>
                    <td><?= $val->cerradoPago?></td>
                     <td><?= $val->estadovalor?></td>
                <?php }else {?>
                    <td style='background-color:#DDE6E4;'><?= $val->id_valor ?></td>
                    <td style='background-color:#DDE6E4;'><?= $val->idordenproduccion ?></td>
                    <td style='background-color:#DDE6E4;'><?= $val->ordenproduccion->codigoproducto ?></td>
                    <td style='background-color:#DDE6E4;'><?= $val->ordenproduccion->cliente->nombrecorto ?></td>
                    <td style='background-color:#DDE6E4;'><?= $val->tipo->tipo?></td>
                    <td align="right" style='background-color:#DDE6E4;'><?= ''.number_format($val->cantidad,0) ?></td>
                    <td align="right" style='background-color:#DDE6E4;'><?= ''.number_format($val->vlr_vinculado,0) ?></td>
                    <td align="right" style='background-color:#DDE6E4;'><?= ''.number_format($val->vlr_contrato,0) ?></td>
                    <td align="right" style='background-color:#DDE6E4;'><?= ''.number_format($val->total_pagar,0) ?></td>
                     <td style='background-color:#DDE6E4;'><?= $val->fecha_proceso ?></td>
                    <td style='background-color:#DDE6E4;'><?= $val->usuariosistema ?></td>
                    <td style='background-color:#DDE6E4;'><?= $val->autorizadoPago?></td>
                    <td style='background-color:#DDE6E4;'><?= $val->cerradoPago?></td>
                     <td style='background-color:#DDE6E4;'><?= $val->estadovalor?></td>
                <?php }?>      
                <td style= 'width: 25px; height: 25px;'>
                        <a href="<?= Url::toRoute(["valor-prenda-unidad/view", "id" => $val->id_valor, 'idordenproduccion' => $val->idordenproduccion]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                </td>
                <td style= 'width: 25px; height: 25px;'>
                        <a href="<?= Url::toRoute(["valor-prenda-unidad/update", "id" => $val->id_valor]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
             
            </tbody>            
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>
