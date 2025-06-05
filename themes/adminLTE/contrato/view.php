<?php
use app\models\FormatoContenido;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Contrato */

$this->title = 'Detalle Contrato';
$this->params['breadcrumbs'][] = ['label' => 'Contratos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_contrato;
$view = 'contrato';
?>
<div class="contrato-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?php
        if($token == 0){
            echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']);
        }else{
            echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index_search'], ['class' => 'btn btn-primary btn-sm']);
        }
        if($token == 0){?>
            <?php if ($model->contrato_activo == 1){ ?>
                <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_contrato], ['class' => 'btn btn-success btn-sm']) ?>
            <?php }?>
            <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimircontrato', 'id' => $model->id_contrato], ['class' => 'btn btn-default btn-sm']); ?>
            <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 11, 'codigo' => $model->id_contrato,'view' => $view,'token' => $token], ['class' => 'btn btn-default btn-sm']) ?>        
            <?php if ($model->contrato_activo == 1){ ?>
                    <!-- Inicio Cerrar contrato -->
                    <?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar Contrato',            
                        ['/contrato/cerrarcontrato','id' => $model->id_contrato,'token' => $token],
                        [
                            'title' => 'Cerrar Contrato',
                            'data-toggle'=>'modal',
                            'data-target'=>'#modalcerrarcontrato'.$model->id_contrato,
                            'class' => 'btn btn-default btn-sm'
                        ]
                    );
                    ?>
            <?php }else{ ?>
                    <!-- Abrir contrato-->
                    <?= Html::a('<span class="glyphicon glyphicon-open"></span> Abrir contrato', ['abrircontrato', 'id' => $model->id_contrato, 'token' => $token], ['class' => 'btn btn-default btn-sm']); ?>
            <?php }?>        
            <div class="modal remote fade" id="modalcerrarcontrato<?= $model->id_contrato ?>">
                <div class="modal-dialog modal-md">
                    <div class="modal-content"></div>
                </div>
            </div>
            <!-- Fin Cerrar contrato -->
        <?php }else{?>
              <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimircontrato', 'id' => $model->id_contrato], ['class' => 'btn btn-default btn-sm']); ?>
            <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 11, 'codigo' => $model->id_contrato,'view' => $view,'token' => $token], ['class' => 'btn btn-default btn-sm']) ?>        
        <?php } ?>    
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Contrato
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_contrato') ?>:</th>
                    <td><?= Html::encode($model->id_contrato) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'identificacion') ?>:</th>
                    <td><?= Html::encode($model->identificacion) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Empleado') ?>:</th>
                     <td><?= Html::encode($model->empleado->nombrecorto) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_inicio') ?>:</th>
                    <td><?= Html::encode($model->fecha_inicio) ?></td>
                </tr>
                <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipo_salario') ?>:</th>
                    <td><?= Html::encode($model->tipo_salario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'salario') ?>:</th>
                    <td><?= Html::encode('$ '.number_format($model->salario,0)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_tiempo') ?>:</th>
                    <td><?= Html::encode($model->tiempoServicio->tiempo_servicio) ?></td>
                     <?php
                    if($model->fecha_final == '2099-12-31'){?>
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_final') ?>:</th>
                         <td><?= Html::encode('INDEFINIDO') ?></td>
                    <?php }else{?>
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_final') ?>:</th>
                         <td><?= Html::encode($model->fecha_final) ?></td>
                    <?php }?>     
                   
                    
                </tr>
                 <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_eps') ?>:</th>
                    <td><?= Html::encode($model->pagoEps->concepto_eps) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Salud') ?>:</th>
                    <td><?= Html::encode($model->entidadSalud->entidad) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_pension') ?>:</th>
                    <td><?= Html::encode($model->pagoPension->concepto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Pension') ?>:</th>
                    <td><?= Html::encode($model->entidadPension->entidad) ?></td>
                </tr>
                <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_centro_trabajo') ?>:</th>
                    <td><?= Html::encode($model->centroTrabajo->centro_trabajo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_cargo') ?>:</th>
                    <td><?= Html::encode($model->cargo->cargo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_grupo_pago') ?>:</th>
                    <td><?= Html::encode($model->grupoPago->grupo_pago) ?></td>
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_cesantia') ?>:</th>
                    <td><?= Html::encode($model->cesantia->cesantia) ?></td>
                </tr>
                <tr style ='font-size:85%;'>
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ciudad_laboral') ?>:</th>
                    <td><?= Html::encode($model->ciudadLaboral->municipio) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ciudad_contratado') ?>:</th>
                    <td><?= Html::encode($model->ciudadContratado->municipio) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'auxilio_transporte') ?>:</th>
                    <td><?= Html::encode($model->auxilio) ?></td>  
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'horario_trabajo') ?>:</th>
                    <td><?= Html::encode($model->horario_trabajo) ?></td>           
                </tr>                
                <tr style ='font-size:85%;'>
                    
                    
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_tipo_cotizante') ?>:</th>
                    <td><?= Html::encode($model->tipoCotizante->tipo) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_subtipo_cotizante') ?>:</th>
                    <td><?= Html::encode($model->subtipoCotizante->subtipo) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_tipo_contrato') ?>:</th>
                    <td><?= Html::encode($model->tipoContrato->contrato) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'dias_contrato') ?>:</th>
                    <td><?= Html::encode($model->dias_contrato) ?></td>                
                             
                </tr>
               
                <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_motivo_terminacion') ?>:</th>
                    <?php if ($model->id_motivo_terminacion){ ?>
                    <td colspan="8"><?= Html::encode($model->motivoTerminacion->motivo) ?></td>
                    <?php }else{ ?>
                    <td colspan="8"></td>
                    <?php } ?>
                
                </tr>
                <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'comentarios') ?>:</th>
                    <td colspan="7"><?= Html::encode($model->comentarios) ?></td> 
                </tr>    
                <tr>    
                    <th style='background-color:#F0F3EF;'   ><?= Html::activeLabel($model, 'funciones_especificas') ?>:</th>
                     <td colspan="7"><?= Html::encode($model->funciones_especificas) ?></td>
                </tr>
            </table>
  <?php
            $form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
            'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
            'options' => []
        ],
        ]);
    ?>        
        </div>
          
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                 <div class="panel-heading" role="tab" id="headingOne">
                       <h4 class="panel-title">
                           <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                               Cambio de salario: <span class="badge"><?= $registros?></span>
                           </a>
                       </h4>
                 </div>
                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead >
                                   <tr style ='font-size:85%;'>
                                       <td scope="col" align="center"><b>Id</b></td>                        
                                       <td scope="col" align="center"><b>Nro_Contrato</b></td>                        
                                       <td scope="col" align="center"><b>Nuevo salario</b></td>                        
                                       <th scope="col" align="center">Fecha_Aplicación</th>                        
                                       <th scope="col" align="center">Formato</th> 
                                       <th scope="col" align="center">Fecha proceso</th> 
                                       <th scope="col" align="center">Usuario</th>                        
                                       <th scope="col" align="center">Observación</th>   
                                       <th scope="col"></th>
                                   </tr>
                            </thead>
                            <tbody>
                                   <?php foreach ($cambio_salario as $val): ?>
                                      <tr style ='font-size:85%;'>
                                          <td><?= $val->id_cambio_salario ?></td>
                                          <td><?= $val->id_contrato ?></td>
                                          <td><?= '$'.number_format($val->nuevo_salario,0)?></td>
                                          <td><?= $val->fecha_aplicacion ?></td>
                                          <td><?= $val->formatoContenido->nombre_formato ?></td>
                                          <td><?= $val->fecha_creacion ?></td>
                                          <td><?= $val->usuariosistema ?></td>
                                          <td><?= $val->observacion ?></td>
                                          <td>
                                              <a href="<?= Url::toRoute(["imprimircambiosalario",'id_cambio_salario'=>$val->id_cambio_salario, 'id'=>$model->id_contrato]) ?>" ><span class="glyphicon glyphicon-print" title="Imprimir "></span></a>
                                          </td>    
                                      </tr>
                                  <?php endforeach; ?>
                            </tbody>  
                            <?php
                            if($token == 0){
                                if($model->contrato_activo == 1){?>
                                   <div align="right">  
                                      <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear', ['contrato/nuevocambiosalario', 'id' => $model->id_contrato, 'token' => $token], ['class' => 'btn btn-info btn-sm']) ?>                    
                                    </div>
                                <?php }
                            }?>
                        </table>  
                    </div>
                </div>    
                        
            </div>  
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingTwo">
                    <h4 class="panel-title">
                      <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                          Adicion al contrato: <span class="badge"><?= $contador_adicion?></span>
                      </a>
                    </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                               <thead >
                                 <tr style ='font-size:85%;'>
                                     <td scope="col" align="center"><b>Id</b></td>                        
                                     <td scope="col" align="center"><b>Nro_Contrato</b></td>                        
                                     <td scope="col" align="center"><b>Vlr_adición</b></td>
                                     <th scope="col" align="center">Concepto salarial</th>  
                                     <th scope="col" align="center">Fecha_Aplicación</th>   
                                     <th scope="col" align="center">Fecha proceso</th>  
                                     <th scope="col" align="center">Tipo formato</th>  
                                     <th scope="col" align="center">Usuario</th>                        
                                     <th scope="col"></th>
                                      <th scope="col"></th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php foreach ($adicion_contrato as $val): ?>
                                    <tr style ='font-size:85%;'>
                                        <td><?= $val->id_pago_adicion ?></td>
                                        <td><?= $val->id_contrato ?></td>
                                        <td><?= '$'.number_format($val->vlr_adicion,0)?></td>
                                        <td><?= $val->codigoSalario->nombre_concepto ?></td>
                                        <td><?= $val->fecha_aplicacion ?></td>
                                        <td><?= $val->fecha_proceso ?></td>
                                        <td><?= $val->formatoContenido->nombre_formato ?></td>
                                        <td><?= $val->usuariosistema ?></td>
                                        <?php
                                           if($val->estado_adicion == 1){?>
                                                <td >
                                                     <a href="<?= Url::toRoute(["contrato/imprimirotrosi",'id_pago_adicion'=>$val->id_pago_adicion]) ?>" ><span class="glyphicon glyphicon-print" title="Imprimir "></span></a>
                                                </td> 
                                               <?php  if($token == 0){?>
                                                <td style="width: 25px;">
                                                    <a href="<?= Url::toRoute(['editarpagoadicion', 'id_pago_adicion'=>$val->id_pago_adicion, 'id'=>$model->id_contrato, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-pencil" title="Editar pago"></span> </a>                   

                                                </td>
                                               <?php }else{?>
                                                <td style="width: 25px;"></td>
                                               <?php }
                                            }else{?>        
                                                <td>
                                                     <a href="<?= Url::toRoute(["contrato/imprimir",'id_pago_adicion'=>$val->id_pago_adicion]) ?>" ><span class="glyphicon glyphicon-print" title="Imprimir "></span></a>
                                                </td>  
                                                <td></td>
                                           <?php } ?>     
                                    </tr>
                                <?php endforeach; ?>
                              </tbody>
                              <?php
                              if($token == 0){
                                if($model->contrato_activo == 1){?>
                                    <div align="right">  
                                        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear', ['contrato/nuevaadicioncontrato', 'id' => $model->id_contrato, 'token' => $token], ['class' => 'btn btn-info btn-sm']) ?>                    
                                    </div>
                                <?php }
                             }?>
                        </table>  
                    </div>
                </div>
                
            </div>
           <?php
               if($model->genera_prorroga == 0){
               }else{?>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThree">
                            <h4 class="panel-title">
                              <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                  Prorroga al contrato: <span class="badge"><?= $cont?></span>
                              </a>
                            </h4>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                            <div class="panel-body">
                                 <table class="table table-bordered table-responsive">
                                    <thead >
                                      <tr style ='font-size:85%;'>
                                          <td scope="col" align="center"><b>Id</b></td>                        
                                          <td scope="col" align="center"><b>Nro_Contrato</b></td>                        
                                          <td scope="col" align="center"><b>Fecha_Nueva_Inicio</b></td>                        
                                          <th scope="col" align="center">Fecha_Terminación</th>                        
                                          <th scope="col" align="center">Fecha_Preaviso</th>       
                                          <th scope="col" align="center">Dias_Preaviso</th>                        
                                          <th scope="col" align="center">Dias_Contratados</th> 
                                          <th scope="col" align="center">Tipo formato</th> 
                                          <th scope="col" align="center">Usuario</th> 
                                          <th scope="col"></th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prorrogas as $prorroga): ?>
                                           <tr style ='font-size:85%;'>
                                               <td><?= $prorroga->id_prorroga_contrato ?></td>
                                               <td><?= $prorroga->id_contrato ?></td>
                                               <td><?= $prorroga->fecha_desde ?></td>
                                               <td><?= $prorroga->fecha_hasta ?></td>
                                               <td><?= $prorroga->fecha_preaviso ?></td>
                                               <td><?= $prorroga->dias_preaviso ?></td>
                                               <td><?= $prorroga->dias_contratados ?></td>
                                               <td><?= $prorroga->formatoContenido->nombre_formato ?></td>
                                               <td><?= $prorroga->usuariosistema ?></td>  
                                                
                                               <td>
                                                    <a href="<?= Url::toRoute(["contrato/imprimirprorroga",'id_prorroga_contrato'=>$prorroga->id_prorroga_contrato]) ?>" ><span class="glyphicon glyphicon-print" title="Imprimir "></span></a>
                                               </td>   
                                           </tr>
                                       <?php endforeach; ?>
                                    </tbody> 
                                    <?php
                                    if($token == 0){
                                        if($model->contrato_activo == 1){
                                                if($cont < 3){?>
                                                    <div align="right">  
                                                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Renovar', ['contrato/nuevaprorroga', 'id' => $model->id_contrato, 'token' => $token], ['class' => 'btn btn-info btn-sm']) ?>                    
                                                   </div>
                                                <?php }else{?>
                                                   <div align="right">  
                                                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Contrato 1 año', ['contrato/nuevaprorrogaano', 'id' => $model->id_contrato, 'token' => $token], ['class' => 'btn btn-warning btn-sm']) ?>                    
                                                   </div>
                                                <?php }
                                        }
                                    } ?>    
                                </table>  
                            </div>
                        </div>

                   </div>  
              <?php } ?>    
        </div>
    </div>
                  
     <?php $form->end() ?>    
    </div>   
</div>


