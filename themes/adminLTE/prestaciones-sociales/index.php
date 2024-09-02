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
use app\models\GrupoPago;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Prestaciones sociales';
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
    "action" => Url::toRoute("prestaciones-sociales/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$grupo = ArrayHelper::map(GrupoPago::find()->all(), 'id_grupo_pago', 'grupo_pago');
$empleado = ArrayHelper::map(Empleado::find()->orderBy('nombrecorto ASC')->all(), 'id_empleado', 'nombrecorto');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "documento")->input("search") ?>
             <?= $formulario->field($form, 'id_empleado')->widget(Select2::classname(), [
                'data' => $empleado,
                'options' => ['prompt' => 'Seleccione el empleado...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            
            <?= $formulario->field($form, 'id_grupo_pago')->widget(Select2::classname(), [
                'data' => $grupo,
                'options' => ['prompt' => 'Seleccione el grupo pago ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("prestaciones-sociales/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <th scope="col" style='background-color:#B9D5CE;'>Nro pago</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Grupo_pago</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. inicio</th>                
                <th scope="col" style='background-color:#B9D5CE;'>F. final</th>
                <th scope="col" style='background-color:#B9D5CE;'>Devengado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Deducción</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total pagar</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Estado generado" >Eg</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Estado aplicado" >Ea</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Estado cerrado" >Ec</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <td align="center" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></td>                              
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
                <tr style='font-size:85%;'>                
                <td><?= $val->nro_pago ?></td>
                <td><?= $val->documento?></td>
                <td><?= mb_strtoupper($val->empleado->nombrecorto)?></td>
                 <td><?= $val->grupoPago->grupo_pago ?></td>
                <td><?= $val->fecha_inicio_contrato ?></td>
                <td><?= $val->fecha_termino_contrato ?></td>
                <td align="right"><?= ''.number_format($val->total_devengado,0) ?></td>
                <td align="right"><?= ''.number_format($val->total_deduccion,0) ?></td>
                <td align="right"><?= ''.number_format($val->total_pagar,0) ?></td>
                <td><?= $val->estadogenerado?></td>
                <td><?= $val->estadoaplicado?></td>
                <td><?= $val->estadocerrado?></td>
               
                <td style= 'width: 35px;'>
                        <a href="<?= Url::toRoute(["prestaciones-sociales/view", "id" => $val->id_prestacion, "pagina" =>$pagina]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                </td>
                <td style= 'width: 35px;'>
                        <?= Html::a('', ['eliminar', 'id' => $val->id_prestacion], [
                            'class' => 'glyphicon glyphicon-trash',
                            'data' => [
                                'confirm' => 'Esta seguro de eliminar el registro?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </td>
                <td align="center" style= 'width: 35px;'><input type="checkbox" name="id_prestacion[]" value="<?= $val->id_prestacion ?>"></td>
            </tr>            
            </tbody>            
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >            
                
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar", ['name' => 'elminar','class' => 'btn btn-danger btn-sm ']); ?>
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
