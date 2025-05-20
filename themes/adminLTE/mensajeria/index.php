<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\date\DatePicker;


$this->title = 'Mensajeria';
$this->params['breadcrumbs'][] = $this->title;


?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtroproveedor");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista proveedor</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("mensajeria/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
$Conproveedor = ArrayHelper::map(app\models\Proveedor::find()->orderBy('nombrecorto ASC')->all(), 'idproveedor', 'nombrecorto');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtroproveedor" style="display:block">
        <div class="row" >
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
           <?= $formulario->field($form, 'proveedor')->widget(Select2::classname(), [
                    'data' => $Conproveedor,
                    'options' => ['placeholder' => 'Seleccione el proveedor'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
                    ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("mensajeria/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>
<?php $form = ActiveForm::begin([
                            "method" => "post",                            
                        ]);
                ?>    
<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros: <?= $pagination->totalCount ?>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
           <tr style="font-size: 85%;">    
                <th scope="col" style='background-color:#B9D5CE;'>Nit/Cedula</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proveedor</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha registro</th>
                <th scope="col" style='background-color:#B9D5CE;'>Nombre de la ruta</th>
                <th scope="col" style='background-color:#B9D5CE;'>Valor pago</th>
                <th scope="col" style='background-color:#B9D5CE;'>User_name</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cerrado</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>                               
                <th scope="col" style='background-color:#B9D5CE;'></th>  
                <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
                <tr style="font-size: 85%;">                   
                    <td><?= $val->proveedor->cedulanit ?></td>
                    <td><?= $val->proveedor->nombrecorto ?></td>
                    <td><?= $val->fecha_proceso ?></td>
                     <td><?= $val->fecha_registro ?></td>
                    <td><?= $val->precio->concepto ?></td>
                    <td style="text-align: right"><?= ''. number_format($val->valor_precio,0) ?></td>
                    <td><?= $val->user_name ?></td>
                    <td><?= $val->cerradoProceso ?></td>
                    
                    <td style= 'width: 25px; height: 20px;'>
                        <a href="<?= Url::toRoute(["mensajeria/view", "id" => $val->id_codigo]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                    </td>
                    <?php if($val->cerrado == 0){?>
                        <td style= 'width: 25px; height: 20px;'>
                            <a href="<?= Url::toRoute(["mensajeria/update", "id" => $val->id_codigo])?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                        </td>
                    <?php }else { ?>
                        <td style= 'width: 25px; height: 20px;'></td>
                    <?php } ?>    
                    <td style='width: 20px; height: 20px;'><input type="checkbox" name="listado_mensajeria[]" value="<?= $val->id_codigo ?>"></td> 
                </tr>
            </tbody>
            <?php endforeach; ?>
        </table>
        <div class="panel-footer text-right" >
            <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-eye-close'></span> Cerrar pago", ['class' => 'btn btn-info btn-sm', 'name' => 'cerrar_pago_mensajeria']); ?>                
            <a align="right" href="<?= Url::toRoute("mensajeria/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>
 <?php $form->end() ?>
<script type="text/javascript">
	function marcar(source) 
	{
		checkboxes=document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
		for(i=0;i<checkboxes.length;i++) //recoremos todos los controles
		{
			if(checkboxes[i].type == "checkbox") //solo si es un checkbox entramos
			{
				checkboxes[i].checked=source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
			}
		}
	}
</script>