<?php
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

/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Resume de pago';
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
    "action" => Url::toRoute("valor-prenda-unidad/indexsoporte"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$operario= ArrayHelper::map(\app\models\Operarios::find()->orderBy('nombrecompleto asc')->all(), 'id_operario', 'nombrecompleto');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "idordenproduccion")->input("search") ?>
             <?= $formulario->field($form, 'id_operario')->widget(Select2::classname(), [
                'data' => $operario,
                'options' => ['prompt' => 'Seleccione el operario'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
               <?= $formulario->field($form, 'dia_pago')->widget(DatePicker::className(), ['name' => 'check_issue_date',
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
             <?= $formulario->field($form, 'operacion')->dropDownList(['' => 'TODOS', '1' => 'CONFECCION', '2' => 'OPERACIN', '3' => 'AJUSTE'],['prompt' => 'Seleccione el estado ...']) ?>
             <?= $formulario->field($form, 'exportado')->dropDownList(['' => 'TODOS', '0' => 'NO', '1' => 'SI'],['prompt' => 'Seleccione el estado ...']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("valor-prenda-unidad/indexsoporte") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>
<?php
    $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<div class="panel-footer text-right">
    <div class="panel-footer text-right">
      <!-- Inicio Nuevo Detalle proceso -->
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear pago',
            ['/valor-prenda-unidad/pagarserviciosoperarios'],
            [
                'title' => 'Crear servicios',
                'data-toggle'=>'modal',
                'data-target'=>'#modalpagarserviciosoperarios',
                'class' => 'btn btn-info btn-xs'
            ])    
       ?>
    </div> 
    <div class="modal remote fade" id="modalpagarserviciosoperarios">
        <div class="modal-dialog modal-lg">
            <div class="modal-content"></div>
        </div>
    </div>
</div>    
    
<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros: <span class="badge"> <?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>                
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>OP</th>
                <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                <th scope="col" style='background-color:#B9D5CE;'>Operación</th>
                  <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cant.</th>
                <th scope="col" style='background-color:#B9D5CE;'>Vr. Prenda</th>
                <th scope="col" style='background-color:#B9D5CE;'>T. pagado</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Porcentaje de cumplimiento">% Cump.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Registro exportado" >Exportado</span></th>
                <th scope="col" style='background-color:#B9D5CE;'>Observacion</th>
                <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                
              
            </tr>
            </thead>
            <tbody>
            <?php 
             
            foreach ($modelo as $val):?>
                <tr style='font-size:85%;'>  
                    <td><?= $val->consecutivo ?></td>
                    <td><?= $val->idordenproduccion ?></td>
                    <td><?= $val->operario->nombrecompleto ?></td>
                    <td><?= $val->operacionPrenda?></td>
                     <td><?= $val->dia_pago ?></td>
                    <td align="right"><?= ''.number_format($val->cantidad,0) ?></td>
                    <td align="right"><?= ''.number_format($val->vlr_prenda,0) ?></td>
                    <td align="right"><?= ''.number_format($val->vlr_pago,0) ?></td>
                      <td><?= $val->porcentaje_cumplimiento ?></td>
                    <td><?= $val->usuariosistema ?></td>
                    <td><?= $val->registroExportado?></td>
                    <td><?= $val->observacion?></td>
                    <td style="width: 35px;"><input type="checkbox" name="consecutivo[]" value="<?= $val->consecutivo ?>"></td>
            </tbody>            
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <?= Html::submitButton("<span class='glyphicon glyphicon-folder-close'></span> Exportado",['name' => 'cerrar_abrir', 'class' => 'btn btn-success btn-sm']);?>          
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
