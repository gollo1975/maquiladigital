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
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
//Modelos...
use app\models\Empleado;
use app\models\Contrato;
use app\models\GrupoPago;
use app\models\ConfiguracionIncapacidad;
use app\models\DiagnosticoIncapacidad;


$this->title = 'Listado  de Incapacidades';
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
    "action" => Url::toRoute("incapacidad/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$grupopago = ArrayHelper::map(GrupoPago::find()->orderBy ('grupo_pago ASC')->all(), 'id_grupo_pago', 'grupo_pago');
$empleado = ArrayHelper::map(Empleado::find()-> orderBy ('nombrecorto ASC')->all(), 'id_empleado','nombrecorto');
$diagnostico = ArrayHelper::map(DiagnosticoIncapacidad::find()->all(), 'id_codigo', 'codigo_diagnostico');
$configuracionincapacidad = ArrayHelper::map(ConfiguracionIncapacidad::find()->all(), 'codigo_incapacidad', 'nombre');
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
             <?= $formulario->field($form, "numero_incapacidad")->input("search") ?>
             <?= $formulario->field($form, 'id_grupo_pago')->widget(Select2::classname(), [
                'data' => $grupopago,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
      
            <?= $formulario->field($form, 'codigo_incapacidad')->widget(Select2::classname(), [
                'data' => $configuracionincapacidad,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        
            <?= $formulario->field($form, 'fecha_final')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("incapacidad/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
          Registros:<span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr >         
                 <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>Nro_Incapacidad</th>
                <th scope="col" style='background-color:#B9D5CE;'>Tipo_Incapacidad</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Grupo_pago</th>
                <th scope="col" style='background-color:#B9D5CE;'>Desde</th>                
                <th scope="col" style='background-color:#B9D5CE;'>Hasta</th>
                <th scope="col" style='background-color:#B9D5CE;'>Dias</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th score="col" style='background-color:#B9D5CE;'></th>                              
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val):
                 $contrato_activo = Contrato::find()->where(['=', 'id_contrato', $val->id_contrato])->one();
                ?>
            <tr style ='font-size: 85%;'>                
                <td><?= $val->id_incapacidad ?></td>
                <td><?= $val->numero_incapacidad ?></td>
                <td><?= $val->codigoIncapacidad->nombre ?></td>
                <td><?= $val->identificacion ?></td>
                <td><?= $val->empleado->nombrecorto ?></td>
                <td><?= $val->grupoPago->grupo_pago ?></td>
                <td><?= $val->fecha_inicio ?></td>
                <td><?= $val->fecha_final ?></td>
                <td><?= $val->dias_incapacidad ?></td>
                 <?php 
                    if($contrato_activo->contrato_activo == 0){?>
                      <td colspan="4"><p style="color:red;">Closed</p></td>
                    <?php }else{?>
                      <td style= 'width: 25px;'>
                        <a href="<?= Url::toRoute(["incapacidad/view", "id" => $val->id_incapacidad]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                     </td>
                     <td style= 'width: 25px;'>
                         <a href="<?= Url::toRoute(["incapacidad/update", "id" => $val->id_incapacidad]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                    </td>
                    <td style= 'width: 25px;'>
                        <?= Html::a('', ['eliminar', 'id' => $val->id_incapacidad], [
                            'class' => 'glyphicon glyphicon-trash',
                            'data' => [
                                'confirm' => 'Esta seguro de eliminar el registro?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </td>
                    <?php }?>   
            </tr>            
            <?php endforeach; ?>
            </tbody>    
        </table> 
        <div class="panel-footer text-right" >            
           <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
            <a align="right" href="<?= Url::toRoute("incapacidad/nuevo") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
        <?php $form->end() ?>
        </div>
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