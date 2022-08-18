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
use app\models\ValorPrendaUnidadDetalles;
use app\models\Matriculaempresa;

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
             <?= $formulario->field($form, 'operacion')->dropDownList(['' => 'TODOS', '1' => 'CONFECCION', '2' => 'OPERACION', '3' => 'AJUSTE'],['prompt' => 'Seleccione el estado ...']) ?>
             <?= $formulario->field($form, 'exportado')->dropDownList(['' => 'TODOS', '0' => 'NO', '1' => 'SI'],['prompt' => 'Seleccione el estado ...']) ?>
          
        </div>
          <div class="row checkbox checkbox-success" align ="center">
                <?= $formulario->field($form, 'validar_eficiencia')->checkbox(['label' => 'Buscar eficiencia', '1' =>'small', 'class'=>'bs_switch','style'=>'margin-bottom:10px;', 'id'=>'validar_eficiencia']) ?>
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
    <?php if($validar_eficiencia == 1){?>
        <ul class="nav nav-tabs" role="tablist">
           <li role="presentation" class="active"><a href="#listado" aria-controls="listado" role="tab" data-toggle="tab">Listado <span class="badge"><?= $pagination->totalCount ?></span></a></li>
           <li role="presentation" ><a href="#eficiencia" aria-controls="eficiencia" role="tab" data-toggle="tab">Eficiencia <span class="badge"></span></a></li>
        </ul>
    <?php }else{ ?>
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#listado" aria-controls="listado" role="tab" data-toggle="tab">Listado <span class="badge"><?= $pagination->totalCount ?></span></a></li>
        </ul>
    <?php }?>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="listado">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
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
                            </thead>
                            <body>
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
                                <?php endforeach; ?>
                            </body>    
                        </table>
                        <div class="panel-footer text-right" >            
                                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                                <?= Html::submitButton("<span class='glyphicon glyphicon-folder-close'></span> Exportado",['name' => 'cerrar_abrir', 'class' => 'btn btn-success btn-sm']);?>          
                        </div>
                    </div>
                </div>    
            </div>    
        </div>
        <!-- FIN TABS-->
         <div role="tabpanel" class="tab-pane" id="eficiencia">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr style ='font-size:85%;'>                
                                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Fecha operación</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Cumplimiento</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Nota</th>
                                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                            </thead>
                            <body>
                                 <?php
                                    $cumplimiento = 0;
                                    $auxiliar = '';
                                    $empresa = Matriculaempresa::findOne(1);
                                    if($id_operario > 0){
                                         $modelo2 = ValorPrendaUnidadDetalles::find()->where(['>=','dia_pago', $dia_pago])
                                                  ->andWhere(['<=','dia_pago', $fecha_corte])
                                                  ->andWhere(['=','id_operario', $id_operario])->orderBy('dia_pago DESC')->all();
                                    }else{
                                        $modelo2 = ValorPrendaUnidadDetalles::find()->where(['>=','dia_pago', $dia_pago])
                                                  ->andWhere(['<=','dia_pago', $fecha_corte])->orderBy('id_operario DESC')->all();
                                    } 
                                    
                                    foreach ($modelo2 as $eficiencia): 
                                     //   echo $eficiencia->dia_pago,'<br>';
                                      // echo $eficiencia->id_operario,'<br>';
                                    
                                            $cumplimiento = 0;
                                            $detalle = ValorPrendaUnidadDetalles::find()->where(['=','dia_pago', $eficiencia->dia_pago])
                                                                                     ->andWhere(['=','id_operario', $eficiencia->id_operario])->orderBy('dia_pago')->all();
                                            $con = count($detalle);
                                            if($con <= 1){
                                                foreach ($detalle as $detalles):
                                                     $auxiliar = '';
                                                    ?>
                                                    <tr style="font-size: 85%;">
                                                        <td ><?= $detalles->operario->documento ?></td>
                                                       <td ><?= $detalles->operario->nombrecompleto ?></td>
                                                       <td ><?= $detalles->dia_pago?></td>
                                                       <?php if($detalles->porcentaje_cumplimiento > $empresa->porcentaje_empresa){?>
                                                            <td style='background-color:#F9F4CB;' ><?= $detalles->porcentaje_cumplimiento ?>%</td>
                                                            <td><?= 'GANA BONIFICACION' ?></td>
                                                       <?php }else{?> 
                                                            <td style='background-color:#B6EFF5;' ><?= $detalles->porcentaje_cumplimiento ?>%</td>
                                                            <td><?= 'NO GANA BONIFICACION' ?></td>
                                                       <?php }?>     
                                                       <td ><?= $detalles->usuariosistema ?></td>
                                                    </tr>
                                              <?php endforeach; 
                                            }else{
                                                foreach ($detalle as $contar):
                                                   $cumplimiento += $contar->porcentaje_cumplimiento;
                                                endforeach;
                                                if($id_operario > 0){
                                                    if($eficiencia->dia_pago != $auxiliar){
                                                       $auxiliar = $eficiencia->dia_pago;
                                                        ?>
                                                        <tr style="font-size: 85%;">
                                                          <td ><?= $contar->operario->documento ?></td>
                                                          <td ><?= $contar->operario->nombrecompleto ?></td>
                                                          <td ><?= $contar->dia_pago?></td>
                                                          <?php if($cumplimiento > $empresa->porcentaje_empresa){?>
                                                                <td style='background-color:#F9F4CB;' ><?= $cumplimiento ?>%</td>
                                                                <td><?= 'GANA BONIFICACION' ?></td>
                                                           <?php }else{?> 
                                                                <td style='background-color:#B6EFF5;' ><?= $cumplimiento ?>%</td>
                                                                <td><?= 'NO GANA BONIFICACION' ?></td>
                                                           <?php }?>     
                                                          <td ><?= $contar->usuariosistema ?></td>
                                                        </tr>
                                                    <?php }else{
                                                         $auxiliar = $eficiencia->dia_pago;
                                                    }  
                                               }else{
                                                    if($eficiencia->id_operario != $auxiliar){
                                                       $auxiliar = $eficiencia->id_operario;
                                                        ?>
                                                        <tr style="font-size: 85%;">
                                                          <td ><?= $contar->operario->documento ?></td>
                                                          <td ><?= $contar->operario->nombrecompleto ?></td>
                                                          <td ><?= $contar->dia_pago?></td>
                                                          <?php if($cumplimiento > $empresa->porcentaje_empresa){?>
                                                                <td style='background-color:#F9F4CB;' ><?= $cumplimiento ?>%</td>
                                                                <td><?= 'GANA BONIFICACION' ?></td>
                                                           <?php }else{?> 
                                                                <td style='background-color:#B6EFF5;' ><?= $cumplimiento ?>%</td>
                                                                <td><?= 'NO GANA BONIFICACION' ?></td>
                                                           <?php }?>     
                                                          <td ><?= $contar->usuariosistema ?></td>
                                                        </tr>
                                                    <?php }else{
                                                         $auxiliar = $eficiencia->id_operario;
                                                    }
                                               }    
                                            }   
                                    endforeach; 
                                   ?>
                                                    
                            </body>    
                        </table>
                         <?php $form->end() ?>
                    </div>
                </div>    
            </div>    
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
