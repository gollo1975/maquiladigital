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
use app\models\GrupoPago;
use app\models\ConceptoSalarios;
use app\models\Contrato;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LicenciaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Adicional por fecha';
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
    "action" => Url::toRoute(["pago-adicional-fecha/view", 'id'=>$id, 'fecha_corte' => $fecha_corte]),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
$grupopago = ArrayHelper::map(GrupoPago::find()->orderBy('grupo_pago ASC')->all(), 'id_grupo_pago', 'grupo_pago');
$empleado = ArrayHelper::map(Empleado::find()->orderBy('nombrecorto ASC')->all(), 'id_empleado','nombrecorto');
$conceptosalario = ArrayHelper::map(ConceptoSalarios::find()->where(['tipo_adicion'=> 1])->orWhere(['tipo_adicion'=> 2])->orWhere(['=','intereses', 1])->all(), 'codigo_salario', 'nombre_concepto');
?>
<div class="panel-footer text-left"> 
    <a href="<?= Url::toRoute(["pago-adicional-fecha/index"]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>  
    <?php if($fechacorte->estado_proceso == 1){?>   
        <?= Html::a('<span class="glyphicon glyphicon-import"></span> Importar intereses', ['pago-adicional-fecha/importinteres', 'id' => $id, 'fecha_corte' => $fecha_corte], ['class' => 'btn btn-info btn-sm'])?>
           <div class="btn-group" role="group">
            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Importar pago producción
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
               <li><?= Html::a('<span class="glyphicon glyphicon-import"></span> Importar', ['pago-adicional-fecha/importarpagoproduccion', 'id' => $id, 'fecha_corte' => $fecha_corte],['class' => 'btn btn-default btn-xs']) ?></li>
               <li><?= Html::a('<span class="glyphicon glyphicon-ok"></span> Aplicar', ['pago-adicional-fecha/aplicarpagoproduccion', 'id' => $id, 'fecha_corte' => $fecha_corte],['class' => 'btn btn-info btn-xs']) ?></li>
            </ul>
       </div> 
       <div class="btn-group" role="group">
            <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Importar primas
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
               <li><?= Html::a('<span class="glyphicon "></span> Importar primas', ['pago-adicional-fecha/importarpagoprimas', 'id' => $id, 'fecha_corte' => $fecha_corte],['class' => 'btn btn-default btn-xs']) ?></li>
               <li><?= Html::a('<span class="glyphicon "></span> Aplicar pago', ['pago-adicional-fecha/aplicarpagoprimas', 'id' => $id, 'fecha_corte' => $fecha_corte],['class' => 'btn btn-info btn-xs']) ?></li>
            </ul>
       </div> 
    
  <?php }?>  
</div>
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
         
             <?= $formulario->field($form, 'id_grupo_pago')->widget(Select2::classname(), [
                'data' => $grupopago,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
      
            <?= $formulario->field($form, 'codigo_salario')->widget(Select2::classname(), [
                'data' => $conceptosalario,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);?>
              <?= $formulario->field($form, 'tipo_adicion')->dropDownList(["1" => 'SUMA', "2" => 'RESTA'],['prompt' => 'Seleccione una opcion ...']) ?>
            
        </div> 
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute(["pago-adicional-fecha/view" , "id" => $id, 'fecha_corte' => $fecha_corte])?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <tr style="font-size: 85%;">      
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha_Corte</th>
                <th scope="col" style='background-color:#B9D5CE;'>Concepto</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Grupo_pago</th>
                <th scope="col" style='background-color:#B9D5CE;'>Vlr_pago</th>
                <th scope="col" style='background-color:#B9D5CE;'><SPAN title="Aplica a dia Laborado">Adl.</SPAN></th>
                <th scope="col" style='background-color:#B9D5CE;'><SPAN title="Activo">Act.</SPAN></th>
                <th scope="col" style='background-color:#B9D5CE;'><SPAN title="Activo periodo">Ap</SPAN></th>
                <th scope="col" style='background-color:#B9D5CE;'><SPAN title="Debito / Credito">D/C</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><SPAN title="Numero de contrato">Nro_Cont</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th score="col" style='background-color:#B9D5CE;'></th>                              
                 <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val):
                    $contrato_activo = Contrato::find()->where(['=', 'id_contrato', $val->id_contrato])->one();
                ?>
                <tr style="font-size: 85%;">                
                <td><?= $val->id_pago_permanente?></td>
                 <td><?= $val->fecha_corte?></td>
                <td><?= $val->codigoSalario->nombre_concepto ?></td>
                <td><?= $val->empleado->identificacion?></td>
                <td><?= $val->empleado->nombrecorto?></td>
                <td><?= $val->grupoPago->grupo_pago?></td>
                <td><?= '$'.number_format($val->vlr_adicion,0)?></td>
                <td><?= $val->aplicardialaborado?></td>
                <td><?= $val->estadoregistro?></td>
                <td><?= $val->estadoPeriodo?></td>
                <td><?= $val->debitoCredito?></td>
                <td><?= $val->contrato->id_contrato?></td>
                 <?php
                    if($contrato_activo->contrato_activo == 0){?>
                        <td colspan="3" align="center"><p style="color:red;">Closed</p></td>
                    <?php }else{
                        if($estado_proceso == 1){
                            ?>   

                            <td style="width: 25px;">
                                <a href="<?= Url::toRoute(["pago-adicional-fecha/vista", 'estado_proceso'=>$estado_proceso, "id" => $id , "id_pago_permanente" => $val->id_pago_permanente, "tipoadicion"=>$val->tipo_adicion, 'fecha_corte' => $val->fecha_corte]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                             </td>
                             <td style="width: 25px;">
                                 <a href="<?= Url::toRoute(["pago-adicional-fecha/updatevista", "id"=>$id, "id_pago_permanente" => $val->id_pago_permanente, "tipoadicion"=>$val->tipo_adicion, 'fecha_corte' => $val->fecha_corte]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                            </td>
                            <td style="width: 25px;">
                                <?= Html::a('', ['eliminaradicional', 'id_pago_permanente' => $val->id_pago_permanente, 'fecha_corte' => $val->fecha_corte], [
                                    'class' => 'glyphicon glyphicon-trash',
                                    'data' => [
                                        'confirm' => 'Esta seguro de eliminar el registro?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            </td>
                        <?php }else{ ?>
                            <td style="width: 25px;">
                                <a href="<?= Url::toRoute(["pago-adicional-fecha/vista", 'estado_proceso'=>$estado_proceso, "id" => $id , "id_pago_permanente" => $val->id_pago_permanente, "tipoadicion"=>$val->tipo_adicion, 'fecha_corte' => $val->fecha_corte]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                             </td>   
                            <td></td>    
                             <td></td>
                        <?php }    
                      } ?>     
                  <td><input type="checkbox" name="id_pago_permanente[]" value="<?= $val->id_pago_permanente ?>"></td>
            </tr>            
            </tbody>            
            <?php endforeach; ?>
        </table> 
      <?php if($estado_proceso == 0){?>
        <div class="panel-footer text-right" >    
           
            <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
            <a align="right" href="<?= Url::toRoute(["pago-adicional-fecha/createadicion", 'id'=>$id, 'fecha_corte' => $fecha_corte])?>" class="btn btn-success disabled btn-sm"><span class='glyphicon glyphicon-plus'></span></a>
            <a align="right" href="<?= Url::toRoute(["pago-adicional-fecha/createdescuento", 'id'=> $id, 'fecha_corte' => $fecha_corte])?>" class="btn btn-info disabled  btn-sm"><span class='glyphicon glyphicon-minus-sign'></span></a>
              <div class="btn-group">
                    <button type="button" class="btn btn-warning dropdown-toggle disabled btn-sm"
                            data-toggle="dropdown">
                        <span class="glyphicon glyphicon-check"></span>Activar 
                    </button>
                    <ul class="dropdown-menu" role="menu">
                      <li><?= Html::submitButton("<span class='glyphicon glyphicon-check'></span> Registro",['name' => 'activar_periodo_registro', 'class' => 'btn btn-warningbtn-sm']);?>  </li>
                      <li><?= Html::submitButton("<span class='glyphicon glyphicon-check'></span> Periodo", ['name' => 'activar_periodo', "class" => "btn btn-info btn-sm"]) ?>  </li>
                    </ul>
             </div> 
                 <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle disabled btn-sm"
                            data-toggle="dropdown">
                        <span class="glyphicon glyphicon-unchecked"></span>Desactivar 
                    </button>
                    <ul class="dropdown-menu" role="menu">
                      <li><?= Html::submitButton("<span class='glyphicon glyphicon-unchecked'></span> Registro", ["class" => "btn btn-warning btn-sm", 'name' => 'desactivar_periodo_registro']) ?>  </li>
                      <li><?= Html::submitButton("<span class='glyphicon glyphicon-unchecked'></span> Periodo.", ["class" => "btn btn-info btn-sm", 'name' => 'desactivar_periodo']) ?>  </li>
                    </ul>
                  </div>     
        </div>
      <?php }else{?>
       <div class="panel-footer text-right" >  
            <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
            <a align="right" href="<?= Url::toRoute(["pago-adicional-fecha/createadicion", 'id'=>$id, 'fecha_corte' => $fecha_corte])?>" class="btn btn-success btn-sm "><span class='glyphicon glyphicon-plus'></span></a>
            <a align="right" href="<?= Url::toRoute(["pago-adicional-fecha/createdescuento", 'id'=> $id, 'fecha_corte' => $fecha_corte])?>" class="btn btn-info btn-sm"><span class='glyphicon glyphicon-minus-sign'></span></a>
              <div class="btn-group">
                    <button type="button" class="btn btn-warning dropdown-toggle btn-sm"
                            data-toggle="dropdown">
                        <span class="glyphicon glyphicon-check"></span>Activar 
                    </button>
                    <ul class="dropdown-menu" role="menu">
                      <li><?= Html::submitButton("<span class='glyphicon glyphicon-check'></span> Registro",['name' => 'activar_periodo_registro', 'class' => 'btn btn-warning btn-sm']);?>  </li>
                      <li><?= Html::submitButton("<span class='glyphicon glyphicon-check'></span> Periodo", ['name' => 'activar_periodo', "class" => "btn btn-info btn-sm"]) ?>  </li>
                    </ul>
             </div> 
                 <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle btn-sm"
                            data-toggle="dropdown">
                        <span class="glyphicon glyphicon-unchecked"></span>Desactivar 
                    </button>
                    <ul class="dropdown-menu" role="menu">
                      <li><?= Html::submitButton("<span class='glyphicon glyphicon-unchecked'></span> Registro", ["class" => "btn btn-warning btn-sm", 'name' => 'desactivar_periodo_registro']) ?>  </li>
                      <li><?= Html::submitButton("<span class='glyphicon glyphicon-unchecked'></span> Periodo.", ["class" => "btn btn-info btn-sm", 'name' => 'desactivar_periodo']) ?>  </li>
                    </ul>
                  </div>     
        </div>
      <?php }?>
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