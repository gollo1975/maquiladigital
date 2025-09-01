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
use app\models\Horario;
/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$Fecha =  $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;
$operario = \app\models\Operarios::findOne($tokenOperario);
$this->title = strtoupper($tallas->listadoTallaIndividual) . ' - Referencia:' . strtoupper($model->ordenproduccion->codigoproducto);
$this->params['breadcrumbs'][] = $this->title;
$horario = Horario::findOne(1);
$desayunoRegistrado = \app\models\ValorPrendaUnidadDetalles::find()
    ->where([
        'id_operario' => $tokenOperario,
        'dia_pago' => date('Y-m-d')
    ])
    ->andWhere(['is not', 'hora_inicio_desayuno', new \yii\db\Expression('null')])
    ->count();

// Verificar si ya se ha registrado un inicio de almuerzo para el día
$almuerzoRegistrado = \app\models\ValorPrendaUnidadDetalles::find()
    ->where([
        'id_operario' => $tokenOperario,
        'dia_pago' => date('Y-m-d')
    ])
    ->andWhere(['is not', 'hora_inicio_almuerzo', new \yii\db\Expression('null')])
    ->count();

//busca cuantos eventos veces a ido al baño
$tiempo_desuso = \app\models\ValorPrendaUnidadDetalles::find()
    ->where([
        'id_operario' => $tokenOperario,
        'dia_pago' => date('Y-m-d')
    ])
    ->andWhere(['is not', 'tiempo_desuso', new \yii\db\Expression('null')])
    ->count();
?>
<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view_produccion','id' => $model->id_valor, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tokenOperario' =>$tokenOperario], ['class' => 'btn btn-primary btn-sm'])?>

    <?php if ($desayunoRegistrado == 0) { ?>
        <?= Html::a('<span class="glyphicon glyphicon-film"></span> Desayuno', ['cargar_tiempo_desayuno','id' => $model->id_valor, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tokenOperario' =>$tokenOperario,'id_detalle' => $id_detalle], ['class' => 'btn btn-success btn-sm']); ?>
    <?php } elseif ($desayunoRegistrado > 0 && $almuerzoRegistrado == 0) { ?>
        <?= Html::a('<span class="glyphicon glyphicon-text-background"></span> Almuerzo', ['cargar_tiempo_almuerzo','id' => $model->id_valor, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tokenOperario' =>$tokenOperario,'id_detalle' => $id_detalle], ['class' => 'btn btn-info btn-sm']); ?>
    <?php } else {
         // Opcional: Si ambos ya fueron registrados, no se muestra ningún botón.
    } 
    if($horario->aplica_tiempo_desuso == 1 && $tiempo_desuso < $horario->total_eventos_dia){?>
        <?= Html::a('<span class="glyphicon glyphicon-time"></span> Tiempo autorizado', ['validar_tiempo_desuso','id' => $model->id_valor, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tokenOperario' =>$tokenOperario,'id_detalle' => $id_detalle], ['class' => 'btn btn-warning btn-sm']); 
    }?>
    
</p>

<?php $form = ActiveForm::begin([
            "method" => "post",                            
       ]);
?>
<!--INICIA LOS TABS-->
<div class="table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            <?php if(count($detalle_balanceo) > 0){?>
                <?= $Fecha?>
            <?php }?>    
        </div>                       
         <table class="table table-responsive-lg">
            <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Descripcion</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Sam</th>
                    <th scope="col" style='background-color:#B9D5CE;'>T. oper.</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Faltan</th>
                    <th scope="col" style='background-color:#B9D5CE;'></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $valor_contrato = 0; 
                $total_unidades = 0;
                $valor_vinculado = 0;
                if ($detalle_balanceo){
                    $empresa = app\models\Matriculaempresa::findOne(1);
                    foreach ($detalle_balanceo as $val):
                        $flujo = app\models\Ordenproducciondetalleproceso::find()->where(['=','idproceso', $val->id_proceso])->andWhere(['=','iddetalleorden', $id_detalle])->one();
                        $total_unidades = $flujo->total_unidades_operacion - $flujo->unidades_confeccionadas;
                        if($total_unidades != 0){ ?>
                            <tr style="font-size: 85%;">
                                <td><?= $val->id_proceso ?></td>
                                <td><?= $val->proceso->proceso ?></td>
                                <td><?= $val->minutos ?></td> 
                                <td style="text-align: center"><?= $flujo->total_unidades_operacion ?></td> 
                                <td style="text-align: center"><?= $total_unidades?></td>
                                <td style= 'width: 25px; height: 25px;'>
                                    <?= Html::a('<span class="glyphicon glyphicon-send"></span> Enviar', ['valor-prenda-unidad/enviar_operacion_individual', 'id' => $model->id_valor,'tokenOperario' => $tokenOperario ,'id_detalle' => $id_detalle, 'id_planta' => $id_planta,'id_operacion' => $val->id_proceso,'idordenproduccion' => $idordenproduccion],['class' => 'btn btn-success btn-xs']); ?>  
                                </td>
                            </tr>  
                        <?php }    
                    endforeach;
                }  ?>
            </tbody>  
        </table>
    </div>
   <div class="panel panel-success ">
        <div class="panel-heading">
            <?php
            if(count($vector_eficiencia) > 0){
                $total = 0; $con = 0;
                foreach ($vector_eficiencia as $val) {
                    $con += 1;
                    $total += $val->porcentaje_cumplimiento;
                }?>
                    <div style="font-size: 200%; text-align: center; display: flex; justify-content: center; gap: 50px;">
                            <div>Operaciones: <?= round($con)?></div>
                            <div>Eficiencia: <?= round($total / $con)?>%</div>
                        </div>
            <?php }?>    
        </div> 
   </div>    
</div>
   
 
         
    <?php ActiveForm::end(); ?>
