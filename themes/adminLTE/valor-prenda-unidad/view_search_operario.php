<?php
//clases
use yii\bootstrap\Progress;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\Session;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;
use kartik\select2\Select2;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$Fecha =  $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;

$this->title = 'Ingreso de pagos ('. $model->planta->nombre_planta. ')-(Referencia:' . $codigo.' )';
$this->params['breadcrumbs'][] = $this->title;
$operarios = ArrayHelper::map(\app\models\Operarios::find()->where(['=','estado', 1])->andWhere(['=','id_planta', $model->id_planta])->orderBy('nombrecompleto ASC')->all(), 'id_operario', 'nombrecompleto');

?>
<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['search_tallas_ordenes','id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion, 'id_planta' =>$model->id_planta, 'tokenPlanta' =>$tokenPlanta, 'tipo_pago' => $tipo_pago], ['class' => 'btn btn-primary btn-sm'])?>
</p> 
<?php $formulario = ActiveForm::begin([
    "method" => "get",  
    "action" => Url::toRoute(["valor-prenda-unidad/view_search_operaciones", 'id' => $model->id_valor, 'idordenproduccion' => $model->idordenproduccion, 'id_planta' => $model->id_planta, 'id_detalle' => $id_detalle, 'codigo' => $codigo, 'tokenPlanta' =>$tokenPlanta, 'tipo_pago' => $tipo_pago]),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
?>

<div class="panel panel-success panel-filters">
        <div class="panel-heading">
            <td style="text-align: right">  <?= $Fecha ?></td>
        </div>

        <div class="panel-body" id="entrada_pago" >
            <div class="row">
                <?= $formulario->field($form, 'operario')->widget(Select2::classname(), [
                       'data' => $operarios,
                       'options' => ['prompt' => 'Seleccione...'],
                       'pluginOptions' => [
                           'allowClear' => true
                       ],
                ]); ?>
                <?= $formulario->field($form, 'fecha_entrada')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
                ?>
                 <?= $formulario->field($form, 'aplica_sabado')->dropDownList(['0' => 'NO', '1' => 'SI']) ?>
                <?= $formulario->field($form, 'alimentacion')->dropDownList(['0' => 'NO', '1' => 'SI']) ?>
                <?= $formulario->field($form, 'modulo')->widget(Select2::classname(), [
                       'data' => $nombre_modulo,
                       'options' => ['prompt' => 'Seleccione...'],
                       'pluginOptions' => [
                           'allowClear' => true
                       ],
                ]); ?>
                <?= $formulario->field($form, 'id_detalle')->widget(Select2::classname(), [
                       'data' => $listado_tallas,
                       'options' => ['prompt' => 'Seleccione...'],
                       'pluginOptions' => [
                           'allowClear' => true
                       ],
                ]); ?>

           </div>
        </div>    
            <div class="panel-footer text-right">
                   <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>               
            </div>

</div>
<?php $formulario->end() ?>
<?php
$form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<!--INICIA LOS TABS-->
<div>
    <ul class="nav nav-tabs" role="tablist">
        <?php if ($detalle_balanceo){?>
        <li role="presentation" class="active"><a href="#listadoperaciones" aria-controls="listadoperaciones" role="tab" data-toggle="tab">Listado de operaciones <span class="badge"><?= count($detalle_balanceo) ?></span></a></li>
        <li role="presentation" ><a href="#historial" aria-controls="historial" role="tab" data-toggle="tab">Historial de pago <span class="badge"><?= count($conOperaciones) ?></span></a></li>
        <?php }else{?>
            <li role="presentation" class="active"><a href="#listadoperaciones" aria-controls="listadoperaciones" role="tab" data-toggle="tab">Listado de operaciones <span class="badge"></span></a></li>
        <?php }?>    
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="listadoperaciones">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">                     
                        <table class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Operaciones</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>T. minutos</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Valor vinculado</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Valor contrato</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>H. inicio</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>H. corte</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $valor_contrato = 0;
                                $valor_vinculado = 0;
                                if ($detalle_balanceo){
                                    foreach ($detalle_balanceo as $val):
                                        $valor_contrato = round($val->minutos * $empresa->vlr_minuto_contrato); 
                                        $valor_vinculado = round($val->minutos * $empresa->vlr_minuto_vinculado);
                                        ?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $val->id_proceso ?></td>
                                            <td><?= $val->proceso->proceso ?></td>
                                            <td><?= $val->minutos ?></td> 
                                            <td style="text-align: right"><?= $valor_vinculado?></td> 
                                            <td style="text-align: right"><?= $valor_contrato?></td> 
                                            <td style="padding-left: 1;padding-right: 1;"><input type="time" name="hora_inicio[]" style="text-align: right"  value="<?= $conCorteProceso->hora_inicio ?>" size="8" ></td> 
                                            <td style="text-align: left"><?= $conCorteProceso->hora_corte?></td>     
                                            <td style="padding-right: 1;padding-right: 1; text-align: right"><input type="text" name="cantidad[]" style="text-align: right"   size="6" ></td>
                                             <input type="hidden" name="operaciones[]" value="<?= $val->id_detalle ?>">
                                        </tr>     
                                   <?php endforeach;
                                }  ?>
                            </tbody>  
                        </table>
                    </div>
                    <?php if($detalle_balanceo){?>
                        <div class="panel-footer text-right">
                            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar datos", ["class" => "btn btn-success btn-sm", 'name' => 'envia_dato_confeccion']) ?>
                        </div>
                    <?php }?>
                </div>
            </div> 
        </div><!--TERMINA TABS DE INGRESOS-->  
        <div role="tabpanel" class="tab-pane" id="historial">
            <div class="table-responsive">
                <div class="panel panel-success">
                    <div class="panel-body">                     
                        <table class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Operaciones</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Tallas</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Cant.</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Vr prenda</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Vr pagado</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>H. inicio</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>H. corte</th>
                                    <th scope="col" style='background-color:#B9D5CE;'>Linea</th>
                                    <th scope="col" style='background-color:#B9D5CE;'></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($conOperaciones){
                                    foreach ($conOperaciones as $val): ?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $val->idproceso ?></td>
                                            <?php if($val->idproceso <> ''){?>
                                                <td><?= $val->operaciones->proceso ?></td>
                                            <?php }else{ ?>
                                                <td><?= 'NO FOUND'?></td>
                                            <?php } ?>    
                                            <?php if($val->iddetalleorden <> ''){?>
                                                <td><?= $val->detalleOrdenProduccion->listadoTalla ?></td>
                                            <?php }else{ ?>
                                                <td><?= 'NO FOUND'?></td>
                                            <?php } ?>  
                                           <td style="text-align: right"><?= $val->cantidad?></td>
                                           <td style="text-align: right"><?= ''. number_format($val->vlr_prenda,0)?></td> 
                                            <td style="text-align: right"><?= ''. number_format($val->vlr_pago,0)?></td> 
                                            <td ><?= $val->hora_inicio?></td>
                                            <td ><?= $val->hora_corte?></td>
                                            <td ><?= $val->hora_descontar?></td>
                                            <td style= 'width: 15px; height: 10px;'>
                                                <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['eliminar_operacion_cargada', 'id' => $val->id_valor, 'detalle' => $val->consecutivo, 'idordenproduccion' => $val->idordenproduccion,'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago,'codigo' => $codigo, 'tokenPlanta' => $tokenPlanta, 'id_datalle_talla' => $val->iddetalleorden], [
                                                    'class' => '',
                                                    'data' => [
                                                        'confirm' => 'Esta seguro de eliminar el registro que ingreso?',
                                                        'method' => 'post',
                                                    ],
                                                ])
                                                ?>
                                            </td>
                                        </tr>     
                                   <?php endforeach;
                                }  ?>
                            </tbody>  
                        </table>
                    </div>
                </div>
            </div> 
        </div><!--TERMINA TABS DE INGRESOS-->  
    </div>
</div>    
         
    <?php ActiveForm::end(); ?>
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