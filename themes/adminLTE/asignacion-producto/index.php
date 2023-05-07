<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Proveedor;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Asignación productos';
$this->params['breadcrumbs'][] = $this->title;

?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtro");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("asignacion-producto/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$proveedor = ArrayHelper::map(Proveedor::find()->where(['=','genera_moda', 1])->orderBy ('nombrecorto ASC')->all(), 'idproveedor', 'nombrecorto');
$Tipo = ArrayHelper::map(\app\models\Ordenproducciontipo::find()->orderBy ('idtipo ASC')->all(), 'idtipo', 'tipo');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "documento")->input("search") ?>
             <?= $formulario->field($form, 'proveedor')->widget(Select2::classname(), [
                'data' => $proveedor,
                'options' => ['prompt' => 'Seleccione el cliente...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'fecha_asignacion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
             <?= $formulario->field($form, 'fecha_corte')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?= $formulario->field($form, 'tipoOrden')->widget(Select2::classname(), [
                'data' => $Tipo,
                'options' => ['prompt' => 'Seleccione el proceso...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'autorizado')->dropDownList(['0' => 'NO', '1' => 'SI'],['prompt' => 'Seleccione una opcion ...']) ?>
            <?= $formulario->field($form, "orden_produccion")->input("search") ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("asignacion-producto/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                 <th scope="col" style='background-color:#B9D5CE;'>No Orden</th>
                <th scope="col" style='background-color:#B9D5CE;'>Nit/Cedula</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proveedor</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proceso</th>
                <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. proceso</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total orden</th>
                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                <th scope="col" style='background-color:#B9D5CE;'>Ult. Usuario</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Autorizado" >Aut.</span></th>
                <th scope="col" style='background-color:#B9D5CE; text-align: center;' colspan="3">Opciones</th>
              
              
            </tr>
            </thead>
            <tbody>
            <?php 
             
            foreach ($model as $val):?>
                <tr style='font-size:85%;'>                
                <td><?= $val->id_asignacion?></td>
                <td><?= $val->orden_produccion?></td>
                <td><?= $val->documento ?></td>
                <td><?= ($val->razon_social)?></td>
                <td><?= $val->tipo->tipo ?></td>
                <td><?= '$'.number_format($val->unidades,0)?></td>
                <td><?= $val->fecha_asignacion?></td>
                <td><?= '$'.number_format($val->total_orden,0)?></td>
                <td><?= $val->usuario?></td>
                 <td><?= $val->usuario_editado?></td>
                <td><?= $val->estadoautorizado?></td>
                <?php if($val->autorizado == 0){?> 
                     <td style= 'width: 25px; height: 10px;'>
                    <?php echo Html::a('<span class="glyphicon glyphicon-user"></span>',
                         ['/asignacion-producto/buscarproducto','id' => $val->id_asignacion, 'token' => $token],
                         [
                             'title' => 'Buscar producto parar asignacion',
                             'data-toggle'=>'modal',
                             'data-target'=>'#modalbuscarproducto'.$val->id_asignacion,
                         ])
                        ?>
                    </td> 
                    <div class="modal remote fade" id="modalbuscarproducto<?= $val->id_asignacion?>">
                         <div class="modal-dialog modal-lg">
                             <div class="modal-content"></div>
                         </div>
                    </div>
                    <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["asignacion-producto/view", "id" => $val->id_asignacion, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                    </td>
                    <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["asignacion-producto/update", "id" => $val->id_asignacion ]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                    </td>
                <?php }else{ ?>
                    <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["asignacion-producto/view", "id" => $val->id_asignacion, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                    </td>
                    <td style= 'width: 25px; height: 25px;'></td>
                     <td style= 'width: 25px; height: 25px;'></td>
                        
                <?php }?>    
             
            </tbody>            
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <a align="right" href="<?= Url::toRoute("asignacion-producto/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>