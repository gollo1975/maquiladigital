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
 
$pago = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $fecha_inicio])
                         ->andWhere(['=','fecha_corte', $fecha_corte])->one();
$listado_operarios = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $fecha_inicio])
                         ->andWhere(['=','fecha_corte', $fecha_corte])->orderBy('operario ASC')->all();
$this->title = 'Resume de pago';
$this->params['breadcrumbs'][] = $this->title;
$form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
        ]);
?>
<?php if($pago->autorizado == 0){?>
    <p>
      <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['indexsoporte'], ['class' => 'btn btn-primary btn-sm']) ?>
      <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Generar nomina", ['name' => 'generarnomina', "class" => "btn btn-info btn-sm",]) ?>
      <?php
       echo Html::a('<span class="glyphicon glyphicon-save-file"></span> Actualizar saldo', ['actualizarsaldo', 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte], ['class' => 'btn btn-default btn-sm']);
       echo Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizarnomina', 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte], ['class' => 'btn btn-default btn-sm',
           'data' => ['confirm' => 'Esta seguro de Autorizar el proceso de pago..', 'method' => 'post']])?>   
    </p> 
 <?php }else{ ?>
    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['indexsoporte'], ['class' => 'btn btn-primary btn-sm']) ?>
    </p>
<?php }?>   

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros: <span class="badge"> <?= count($listado_operarios) ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>                
                <th scope="col" style='background-color:#B9D5CE;'>No Pago</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha corte</th>
                <th scope="col" style='background-color:#B9D5CE;'>Dias</th>
                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                <th scope="col" style='background-color:#B9D5CE;'>Devengado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Deducción</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total pagado</th>
                <th scope="col" style='background-color:#B9D5CE;'>Observacion</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
                <?php 
                $Devengado = 0; $Deduccion = 0; $Pagar = 0;
                     foreach ($listado_operarios as $val):
                         $Devengado += $val->devengado;
                         $Deduccion += $val->deduccion;
                         $Pagar += $val->Total_pagar;
                         ?>
                        <tr style='font-size:85%;'>  
                           <td><?= $val->id_pago ?></td> 
                           <td><?= $val->documento ?></td>
                           <td><?= $val->operario ?></td>
                           <td><?= $val->fecha_inicio ?></td>
                           <td><?= $val->fecha_corte?></td>
                             <td><?= $val->total_dias?></td>
                           <td><?= $val->usuario ?></td>
                           <td align="right"><?= ''.number_format($val->devengado,0) ?></td>
                           <td align="right"><?= ''.number_format($val->deduccion,0) ?></td>
                           <td align="right"><?= ''.number_format($val->Total_pagar,0) ?></td>
                           <td><?= $val->observacion?></td>
                           <td style=' width: 25px;'>
                              <a href="<?= Url::toRoute(["valor-prenda-unidad/vistadetallepago",'id_pago'=>$val->id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'autorizado' => $pago->autorizado]) ?>" ><span class="glyphicon glyphicon-eye-open "></span></a>
                           </td>
                           <td style="width: 25px;">				
                               <a href="<?= Url::toRoute(["imprimircolillaconfeccion",'id_pago'=>$val->id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]) ?>" ><span class="glyphicon glyphicon-print" title="Imprimir "></span></a>
                           </td>
                            <td style=' width: 25px;'>
                                <?php if ($val->autorizado == 0){ ?>
                                <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['eliminarpago', 'id' => $val->id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte], [
                                    'class' => '',
                                    'data' => [
                                        'confirm' => 'Esta seguro de eliminar el registro?',
                                        'method' => 'post',
                                    ],
                                ])
                                ?>
                                <?php }else{ ?>
                               
                                <?php } ?>
                            </td>
                           
                           <input type="hidden" name="pago_servicio_confeccion[]" value="<?= $val->id_operario ?>">
                           <input type="hidden" name="id_pago[]" value="<?= $val->id_pago ?>">
                       </tr>    
                <?php endforeach; ?>
            </body>    
            <tr>
               <td colspan="6"></td>
               <td align="right"><b>Totales</b></td>
               <td align="right" ><b><?= '$ '.number_format($Devengado,0); ?></b></td>
               <td align="right"><b><?= '$ '.number_format($Deduccion,0); ?></b></td>
               <td align="right"><b><?= '$ '.number_format($Pagar,0); ?></b></td>
               <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="10"></td>
        </tr>
        </table> 
    <br>
        <div class="panel-footer text-right" >            
                <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Exportar pagos', ['pagoservicioconfeccion', 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte], ['class' => 'btn btn-primary btn-sm'])?>              
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
