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
use app\models\TipoProducto;



$this->title = 'Costo de productos';
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
    "action" => Url::toRoute("costo-producto/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$tipo_producto = ArrayHelper::map(TipoProducto::find()->orderBy ('concepto ASC')->all(), 'id_tipo_producto', 'concepto');

?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
              <?= $formulario->field($form, "codigo_producto")->input("search") ?>
             <?= $formulario->field($form, 'id_tipo_producto')->widget(Select2::classname(), [
                'data' => $tipo_producto,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, "descripcion")->input("search") ?>
            <?= $formulario->field($form, 'fecha_creacion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
             <?= $formulario->field($form, 'asignado')->dropDownList(['0' => 'NO', '1' => 'SI'],['prompt' => 'Seleccione una opcion ...']) ?>
        
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("costo-producto/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
          Registros <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size: 90%;'>         
                
                <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                <th scope="col" style='background-color:#B9D5CE;'>Descripción</th>
                <th scope="col" style='background-color:#B9D5CE;'>Tipo_producto</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Unidades</th> 
                <th scope="col" style='background-color:#B9D5CE;'>Costo muestra</th>
                <th scope="col" style='background-color:#B9D5CE;'>Costo real</th>
                <th scope="col" style='background-color:#B9D5CE;'>Subtotal</th>
                <th scope="col" style='background-color:#B9D5CE;'>Gran total </th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha creación</th> 
                <th scope="col" style='background-color:#B9D5CE;'><span title="Costo Autorizado">Aut.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Aplica Iva">A. Iva</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Asignado a proveedor">Asig.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th score="col" style='background-color:#B9D5CE;'></th>                              
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
            <tr style ='font-size: 85%;'>                
              
                <td><?= $val->codigo_producto?></td>
                <td><?= $val->descripcion?></td>
                <td><?= $val->tipoProducto->concepto?></td>
                <td style="text-align: right"><?= ''.number_format($val->cantidad,0)?></td>
                <td style="text-align: right"><?= '$'.number_format($val->costo_sin_iva,0)?></td>
                <td style="text-align: right"><?= '$'.number_format($val->costo_con_iva,0)?></td>
                <td style="text-align: right"><?= '$'.number_format($val->subtotal_producto,0)?></td>
                <td style="text-align: right"><?= '$'.number_format($val->total_producto,0)?></td>
                <td><?= $val->fecha_creacion?></td>
                <td><?= $val->Autorizadocosto?></td>
                <td><?= $val->aplicaiva?></td>
                <?php if($val->asignado == 0){?>
                    <td><?= $val->asignarProducto?></td>
                <?php }else{?>
                    <td style= 'width: 25px; height: 10px;'>
                        <?php echo Html::a('<span class="glyphicon glyphicon-user "></span>',            
                            ['/costo-producto/buscarasignacion','id' => $val->id_producto],
                            [
                                'title' => 'Buscar asignacion',
                                'data-toggle'=>'modal',
                                'data-target'=>'#modalbuscarasignacion'.$val->id_producto,
                                'classs' >= 'btn btn-info btn-xs '
                            ]
                        );
                       ?>
                    </td> 
                    <div class="modal remote fade" id="modalbuscarasignacion<?= $val->id_producto ?>">
                        <div class="modal-dialog modal-dialog">
                            <div class="modal-content"></div>
                        </div>
                    </div>
                    
                <?php }?>    
               
                <td style= 'width: 25px; height: 10px;'>
                  <a href="<?= Url::toRoute(["costo-producto/view", "id" => $val->id_producto]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                 </td>
                <td style= 'width: 25px; height: 10px;'>
                   <a href="<?= Url::toRoute(["costo-producto/update", "id" => $val->id_producto]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                </td>
                    <td style= 'width: 25px;'>
                        <?= Html::a('', ['eliminar', 'id' => $val->id_producto], [
                            'class' => 'glyphicon glyphicon-trash',
                            'data' => [
                                'confirm' => 'Esta seguro de eliminar el registro?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </td>
            </tr>            
            <?php endforeach; ?>
            </tbody>    
        </table> 
        <div class="panel-footer text-right" >            
           <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
            <a align="right" href="<?= Url::toRoute("costo-producto/nuevoproducto") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
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