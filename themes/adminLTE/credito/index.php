<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Empleado;
use app\models\TipoPagoCredito;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Créditos';
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
    "action" => Url::toRoute("credito/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$empleado = ArrayHelper::map(Empleado::find()->orderBy('nombrecorto ASC')->all(), 'id_empleado', 'nombrecorto');
$tipopagocredito = ArrayHelper::map(TipoPagoCredito::find()->orderBy('descripcion ASC')->all(), 'id_tipo_pago', 'descripcion');
$codigocredito = ArrayHelper::map(\app\models\ConfiguracionCredito::find()->orderBy('nombre_credito ASC')->all(),'codigo_credito' , 'nombre_credito');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, 'id_empleado')->widget(Select2::classname(), [
                'data' => $empleado,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'id_tipo_pago')->widget(Select2::classname(), [
                'data' => $tipopagocredito,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'codigo_credito')->widget(Select2::classname(), [
                'data' => $codigocredito,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
          <?= $formulario->field($form, 'saldo')->dropDownList(['1' => 'SI'],['prompt' => 'Seleccione una opcion ...']) ?>
        </div>
        
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("credito/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
        Registros: <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
            <tr>                
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>Tipo crédito</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Vlr_Credito</th>                
                <th scope="col" style='background-color:#B9D5CE;'>Cuota</th> 
                <th scope="col" style='background-color:#B9D5CE;'>Saldo</th> 
                <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th> 
                <th scope="col" style='background-color:#B9D5CE;'><span title="Numero de cuotas">N_C</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Cuota actual">C_A</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Estado crédito activo">E_C_A</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Registro activo periodo">R_A_P</span></th>
                <th colspan="3" style='background-color:#B9D5CE;'><p style="color:blue;" align="center">Opciones</p></th>
                <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
            <tr style= 'font-size:85%;'>                
                <td><?= $val->id_credito?></td>
                 <td><?= $val->codigoCredito->nombre_credito?></td>
                 <td><?= $val->empleado->identificacion?></td>
                 <td><?= $val->empleado->nombrecorto?></td>
                 <td><?= '$'.number_format($val->vlr_credito,0)?></td>
                  <td><?= '$'.number_format($val->vlr_cuota,0)?></td>
                 <td><?= '$'.number_format($val->saldo_credito,0)?></td>
                  <td><?= $val->fecha_inicio?></td>
                 <td><?= $val->numero_cuotas?></td>
                 <td><?= $val->numero_cuota_actual?></td>
                 <td><?= $val->Estadocredito?></td>
                 <td><?= $val->estadoperiodo?></td>
               
                <?php if($val->saldo_credito <= 0){?>   
                 <td style='width: 25px;'>
                        <a href="<?= Url::toRoute(["credito/view", "id" => $val->id_credito]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                   
                    </td>
                    <td>
                    </td>   
                    <td>
                    </td>
                    <td>
                    </td>  
                <?php }else{ ?>   
                   <td style='width: 25px;'>
                        <a href="<?= Url::toRoute(["credito/view", "id" => $val->id_credito]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                    </td>
                    <td style='width: 25px;'>
                        <a href="<?= Url::toRoute(["credito/update", "id" => $val->id_credito]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                    </td>
                    <td style='width: 25px;'>
                      <?= Html::a('', ['eliminar', 'id' => $val->id_credito], [
                        'class' => 'glyphicon glyphicon-trash',
                        'data' => [
                            'confirm' => 'Esta seguro de eliminar el registro?',
                            'method' => 'post',
                        ],
                      ]) ?>
                    </td>
                    <td><input type="checkbox" name="id_credito[]" value="<?= $val->id_credito ?>"></td>
                <?php }?>   
                
            </tr>            
            </tbody>            
            <?php endforeach; ?>
        </table> 
        <div class="panel-footer text-right" >     
             <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
                <a align="right" href="<?= Url::toRoute("credito/nuevo") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
            <div class="btn-group">
                  <button type="button" class="btn btn-warning dropdown-toggle btn-sm"
                          data-toggle="dropdown">
                      <span class="glyphicon glyphicon-check"></span>Activar 
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li><?= Html::submitButton("<span class='glyphicon glyphicon-check'></span> Credito",['name' => 'activar_periodo_registro', 'class' => 'btn btn-warning btn-sm']);?>  </li>
                    <li><?= Html::submitButton("<span class='glyphicon glyphicon-check'></span> Registro", ['name' => 'activar_periodo', "class" => "btn btn-info btn-sm"]) ?>  </li>
                  </ul>
            </div> 
            <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle btn-sm"
                            data-toggle="dropdown">
                        <span class="glyphicon glyphicon-unchecked"></span>Desactivar 
                    </button>
                    <ul class="dropdown-menu" role="menu">
                      <li><?= Html::submitButton("<span class='glyphicon glyphicon-unchecked'></span> Credito", ["class" => "btn btn-warning btn-sm", 'name' => 'desactivar_periodo_registro']) ?>  </li>
                      <li><?= Html::submitButton("<span class='glyphicon glyphicon-unchecked'></span> Registro", ["class" => "btn btn-info btn-sm", 'name' => 'desactivar_periodo']) ?>  </li>
                    </ul>
            </div>   
        </div>        
        <?php $form->end() ?>
       
     </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

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