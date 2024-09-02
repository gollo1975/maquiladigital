<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\models\Cliente;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pagos';
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
    "action" => Url::toRoute("pago-fletes/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$Conproveedor = ArrayHelper::map(\app\models\Proveedor::find()->orderBy('nombrecorto ASC')->all(), 'idproveedor', 'nombrecorto');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
            <?= $formulario->field($form, "numero")->input("search") ?>
              <?= $formulario->field($form, 'proveedor')->widget(Select2::classname(), [
                'data' => $Conproveedor,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            
            <?= $formulario->field($form, 'desde')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
               <?= $formulario->field($form, 'hasta')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("pago-fletes/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <th scope="col" style='background-color:#B9D5CE;'>Proveedor</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha pago</th>
                <th scope="col" style='background-color:#B9D5CE;'>fecha hora regisro</th>
                <th scope="col" style='background-color:#B9D5CE;'>Numero pago</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Valor pagado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Aut.</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cerrado</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
              
            </tr>
            </thead>
            <tbody>
            <?php 
            foreach ($model as $val):?>
                <tr style='font-size:85%;'>  
                    <td><?= $val->id_pago?></td>
                    <td><?= $val->proveedor->nombrecorto ?></td>
                    <td><?= $val->fecha_pago ?></td>
                    <td><?= $val->fecha_registro ?></td>
                    <td><?= $val->numero_pago?></td>
                    <td align="right"><?= ''.number_format($val->total_pagado,0)?></td>
                    <td><?= $val->registroAutorizado?></td>
                    <td><?= $val->procesoCerrado?></td>
                
                    <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["pago-fletes/view", "id" => $val->id_pago ]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                    </td>
                    <td style= 'width: 25px; height: 25px;'>
                        <?php if($val->autorizado == 0){?>
                            <a href="<?= Url::toRoute(["pago-fletes/update", "id" => $val->id_pago ]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                        <?php }?>    
                    </td>
                </tr>
                    
            <?php endforeach; ?>
             </tbody>           
        </table>    
    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <a align="right" href="<?= Url::toRoute("pago-fletes/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

