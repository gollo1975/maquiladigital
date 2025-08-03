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
use yii\grid\GridView;
//Modelos...
use app\models\Empleado;
use app\models\Contrato;
use app\models\GrupoPago;
use app\models\ConfiguracionLicencia;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LicenciaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Licencias';
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
    "action" => Url::toRoute("licencia/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
$grupopago = ArrayHelper::map(GrupoPago::find()->all(), 'id_grupo_pago', 'grupo_pago');
$empleado = ArrayHelper::map(Empleado::find()->all(), 'id_empleado','nombrecorto');
$configuracionlicencia = ArrayHelper::map(ConfiguracionLicencia::find()->all(), 'codigo_licencia', 'concepto');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
              <?= $formulario->field($form, "identificacion")->input("search") ?>
             <?= $formulario->field($form, 'id_empleado')->widget(Select2::classname(), [
                'data' => $empleado,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            
            <?= $formulario->field($form, 'fecha_desde')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']
                ])
            ?>
        
            <?= $formulario->field($form, 'fecha_hasta')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']
                ])
            ?>
             <?= $formulario->field($form, 'id_grupo_pago')->widget(Select2::classname(), [
                'data' => $grupopago,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
      
            <?= $formulario->field($form, 'codigo_licencia')->widget(Select2::classname(), [
                'data' => $configuracionlicencia,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("licencia/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
            <tr>      
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>Tipo_Licencia</th>
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
            <tr style="font-size: 85%;">                
                <td><?= $val->id_licencia_pk?></td>
                <td><?= $val->codigoLicencia->concepto?></td>
                <td><?= $val->identificacion ?></td>
                <td><?= $val->empleado->nombrecorto ?></td>
                <td><?= $val->grupoPago->grupo_pago ?></td>
                <td><?= $val->fecha_desde ?></td>
                <td><?= $val->fecha_hasta ?></td>
                <td><?= $val->dias_licencia ?></td>
                <?php 
                    if($contrato_activo->contrato_activo == 0){?>
                <td colspan="4" align='center'><p style="color:red;">Closed</p></td>
                      
                    <?php }else{?>
                        <td style="width: 25px;">
                            <a href="<?= Url::toRoute(["licencia/view", "id" => $val->id_licencia_pk]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                         </td>
                         <td style="width: 25px;">
                             <a href="<?= Url::toRoute(["licencia/update", "id" => $val->id_licencia_pk]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                        </td>
                        <td style="width: 25px;">
                            <?= Html::a('', ['eliminar', 'id' => $val->id_licencia_pk], [
                                'class' => 'glyphicon glyphicon-trash',
                                'data' => [
                                    'confirm' => 'Esta seguro de eliminar el registro?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </td>
                    <?php } ?>     
            </tr>            
            <?php endforeach; ?>
          </tbody>      
        </table> 
        <div class="panel-footer text-right" >            
           <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
            <a align="right" href="<?= Url::toRoute("licencia/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
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