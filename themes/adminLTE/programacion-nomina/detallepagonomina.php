<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\Session;
use yii\db\ActiveQuery;

//modelos
use app\models\Empleado;
use app\models\GrupoPago;
use app\models\ProgramacionNominaDetalle;
use app\models\ConceptoSalarios;
use app\models\PeriodoPagoNomina;

$this->title = 'Comprobante de pago';
$this->params['breadcrumbs'][] = ['label' => 'Comprobante de pago', 'url' => ['comprobantepagonomina']];
$this->params['breadcrumbs'][] = $model->id_programacion;
?>

<div class="programacion-nomina-detallepagonomina">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['comprobantepagonomina'], ['class' => 'btn btn-primary btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimircolilla', 'id' => $model->id_programacion], ['class' => 'btn btn-default btn-sm']) ?>
    </p>
    <?php
         $empleado = Empleado::find()->where(['=','id_empleado', $model->id_empleado])->one();
         $detalle_nomina = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $model->id_programacion])->orderBy('vlr_deduccion ASC')->all();
         $grupo_pago = GrupoPago::find()->where(['=','id_grupo_pago', $model->id_grupo_pago])->one();
         $periodo_pago = PeriodoPagoNomina::find()->where(['=','id_periodo_pago_nomina', $model->id_periodo_pago_nomina])->one();
         $tipo_pago = app\models\TipoNomina::find()->where(['=','id_tipo_nomina', $model->id_tipo_nomina])->one();
         $periodo = app\models\PeriodoPago::find()->where(['=','id_periodo_pago', $periodo_pago->id_periodo_pago])->one();
         $banco = app\models\BancoEmpleado::find()->where(['=','id_banco_empleado', $empleado->id_banco_empleado])->one();
    ?>
   <div class="panel panel-success">
        <div class="panel-heading">
            Comprobante de pago
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover" WIDTH="80%">
                <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_programacion') ?>:</th>
                    <td><?= Html::encode($model->id_programacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nro_pago') ?>:</th>
                    <td><?= Html::encode($model->nro_pago) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_desde') ?>:</th>
                    <td><?= Html::encode($model->fecha_desde) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_hasta') ?>:</th>
                    <td><?= Html::encode($model->fecha_hasta) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'salario_contrato') ?>:</th>
                    <td><?= Html::encode('$'. number_format($model->salario_contrato,0)) ?></td>

                </tr>   
                <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cedula_empleado') ?>:</th>
                    <td><?= Html::encode($model->cedula_empleado) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Empleado') ?>:</th>
                    <td colspan="3"><?= Html::encode($empleado->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_contrato') ?>:</th>
                    <td><?= Html::encode($model->id_contrato) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'salario_promedio') ?>:</th>
                    <td><?= Html::encode('$'. number_format($model->salario_promedio,0)) ?></td>

                </tr>   
                <tr style ='font-size:85%;'>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Tipo_pago') ?>:</th>
                    <td><?= Html::encode($tipo_pago->tipo_pago) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_grupo_pago') ?>:</th>
                    <td colspan="3"><?= Html::encode($grupo_pago->grupo_pago) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'dias_pago') ?>:</th>
                    <td><?= Html::encode($model->dias_pago) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_devengado') ?>:</th>
                    <td><?= Html::encode('$'. number_format($model->total_devengado,0)) ?></td>

                </tr>   
                 <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_inicio_contrato') ?>:</th>
                    <td><?= Html::encode($model->fecha_inicio_contrato) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_periodo_pago_nomina') ?>:</th>
                    <td colspan="3"><?= Html::encode($model->id_periodo_pago_nomina) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'dia_real_pagado') ?>:</th>
                    <td><?= Html::encode($model->dia_real_pagado) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_deduccion') ?>:</th>
                    <td><?= Html::encode('$'. number_format($model->total_deduccion,0)) ?></td>
                 </tr>   
                 <tr style ='font-size:85%;'>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_creacion') ?>:</th>
                    <td><?= Html::encode($model->fecha_creacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?>:</th>
                    <td  colspan="3"><?= Html::encode($model->usuariosistema) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipo_salario') ?>:</th>
                    <td><?= Html::encode($model->tipo_salario) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_pagar') ?>:</th>
                    <td><?= Html::encode('$'. number_format($model->total_pagar,0)) ?></td>

                </tr>   
                 <tr style ='font-size:85%;'>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Periodo') ?>:</th>
                    <td><?= Html::encode($periodo->nombre_periodo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Banco') ?>:</th>
                    <td  colspan="3"><?= Html::encode($banco->banco) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cuenta') ?>:</th>
                    <td><?= Html::encode($empleado->tipoCuenta) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_Cuenta') ?>:</th>
                    <td><?= Html::encode($empleado->cuenta_bancaria) ?></td>

                </tr>   
            </table>
        </div>
    </div>
  
   <?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
    ]); ?>
    <div class="table-responsive">
        <div class="panel panel-success ">
            <div class="panel-heading">
                   Detalle del pago
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-hover" WIDTH="80%">
                    <thead>
                        <tr align="center" >
                            <th scope="col" style='background-color:#B9D5CE;'>Código</th>  
                            <th scope="col" style='background-color:#B9D5CE;'>Concepto</th>
                            <th scope="col" style='background-color:#B9D5CE;'>%</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Nro_Horas</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Vr. Hora</th>                        
                             <th scope="col" style='background-color:#B9D5CE;'>Nro_Dias</th>    
                             <th scope="col" style='background-color:#B9D5CE;'>Vr. Día</th>  
                             <th scope="col" style='background-color:#B9D5CE;'>Deducción</th>   
                             <th scope="col" style='background-color:#B9D5CE;'>Devengado</th>   
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($detalle_nomina as $val):
                               $codigo_salario = 0;
                               $codigo_salario = $val->codigo_salario;
                               $concepto = ConceptoSalarios::find()->where(['=','codigo_salario', $codigo_salario])->one();
                                    if($concepto->auxilio_transporte == 1){
                                        ?>
                                       <tr style ='font-size:85%;'>
                                           <td><?= $val->codigo_salario ?></td>  
                                           <td ><?= $concepto->nombre_concepto ?></td>
                                           <td ><?= $val->porcentaje ?></td>
                                           <td align="center"><?= $val->horas_periodo_reales ?></td>
                                           <td align="right"><?= ''.number_format($val->vlr_hora, 2) ?></td>
                                           <td align="center"><?= $val->dias_transporte ?></td>
                                           <td align="right"><?= ''.number_format($val->vlr_dia, 2) ?></td>
                                           <td align="right"><?= '$'.number_format($val->vlr_deduccion,0) ?></td>
                                           <td align="right"><?= '$'.number_format($val->auxilio_transporte,0) ?></td>
                                      </tr>
                                    <?php
                                    }else{
                                         ?>
                                       <tr style ='font-size:80%;'>
                                           <td><?= $val->codigo_salario ?></td>  
                                           <td ><?= $concepto->nombre_concepto ?></td>
                                           <td ><?= $val->porcentaje ?></td>
                                           <td align="center"><?= $val->horas_periodo_reales ?></td>
                                           <td align="right"><?= ''.number_format($val->vlr_hora, 2) ?></td>
                                           <td align="center"><?= $val->dias_reales ?></td>
                                           <td align="right"><?= ''.number_format($val->vlr_dia, 2) ?></td>
                                           <td align="right"><?= '$'.number_format($val->vlr_deduccion,0) ?></td>
                                           <td align="right"><?= '$'.number_format($val->vlr_devengado,0) ?></td>
                                      </tr>
                                   <?php }
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>    
        </div> 
    </div>
     <?php ActiveForm::end(); ?>
</div>    
    


