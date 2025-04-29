<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Contrato;
use app\models\Incapacidad;
use app\models\EstudioEmpleado;
use app\models\Licencia;
use app\models\Credito;

/* @var $this yii\web\View */
/* @var $model app\models\Empleado */

$this->title = 'Detalle Empleado';
$this->params['breadcrumbs'][] = ['label' => 'Empleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_empleado;
$view = 'empleado';
$contrato = Contrato::find()->where(['=','id_empleado', $model->id_empleado])->orderBy('id_contrato DESC')->all();
$incapacidad = Incapacidad::find()->where(['=','id_empleado', $model->id_empleado])->orderBy('id_incapacidad DESC')->all();
$licencia = Licencia::find()->where(['=','id_empleado', $model->id_empleado])->orderBy('id_licencia_pk DESC')->all();
$credito = Credito::find()->where(['=','id_empleado', $model->id_empleado])->orderBy('id_credito DESC')->all();
$estudio = EstudioEmpleado::find()->where(['=','id_empleado', $model->id_empleado])->orderBy('id DESC')->all();
?>
<div class="empleado-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?php if($token == 0){?>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_empleado], ['class' => 'btn btn-success btn-sm']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 10, 'codigo' => $model->id_empleado,'view' => $view, 'token' => $token], ['class' => 'btn btn-default btn-sm']) ?>
        <?php }else{?>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['indexconsulta'], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 10, 'codigo' => $model->id_empleado,'view' => $view, 'token' => $token], ['class' => 'btn btn-default btn-sm']) ?>
        <?php }?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_empleado') ?>:</th>
                    <td><?= Html::encode($model->id_empleado) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipo_documento') ?>:</th>
                    <td><?= Html::encode($model->tipoDocumento->descripcion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'identificacion') ?>:</th>
                    <td><?= Html::encode($model->identificacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'dv') ?>:</th>
                    <td><?= Html::encode($model->dv) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipo_empleado') ?>:</th>
                    <td><?= Html::encode($model->tipo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_expedicion') ?>:</th>
                    <td><?= Html::encode($model->fecha_expedicion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ciudad_expedicion') ?>:</th>
                    <td><?= Html::encode($model->ciudadExpedicion->municipio) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'rh') ?>:</th>
                    <td><?= Html::encode($model->rh->rh) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nombre1') ?>:</th>
                    <td><?= Html::encode($model->nombre1) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nombre2') ?>:</th>
                    <td><?= Html::encode($model->nombre2) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'apellido1') ?>:</th>
                    <td><?= Html::encode($model->apellido1) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'apellido2') ?>:</th>
                    <td><?= Html::encode($model->apellido2) ?></td>                    
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'direccion') ?>:</th>
                    <td><?= Html::encode($model->direccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'telefono') ?>:</th>
                    <td><?= Html::encode($model->telefono) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'iddepartamento') ?>:</th>
                    <td><?= Html::encode($model->departamento->departamento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idmunicipio') ?>:</th>
                    <td><?= Html::encode($model->municipio->municipio) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'barrio') ?>:</th>
                    <td><?= Html::encode($model->barrio) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'celular') ?>:</th>
                    <td><?= Html::encode($model->celular) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'sexo') ?>:</th>
                    <td><?= Html::encode($model->sexo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_estado_civil') ?>:</th>
                    <td><?= Html::encode($model->estadoCivil->estado_civil) ?></td>
                </tr>
                <tr style="font-size: 85%;"> 
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'estatura') ?>:</th>
                    <td><?= Html::encode($model->estatura) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'peso') ?>:</th>
                    <td><?= Html::encode($model->peso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'libreta_militar') ?>:</th>
                    <td><?= Html::encode($model->libreta_militar) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'distrito_militar') ?>:</th>
                    <td><?= Html::encode($model->distrito_militar) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_nacimiento') ?>:</th>
                    <td><?= Html::encode($model->fecha_nacimiento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ciudad_nacimiento') ?>:</th>
                    <td><?= Html::encode($model->ciudadNacimiento->municipio) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'padre_familia') ?>:</th>
                    <td><?= Html::encode($model->padreFamilia) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cabeza_hogar') ?>:</th>
                    <td><?= Html::encode($model->cabezaHogar) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'horario') ?>:</th>
                    <td><?= Html::encode($model->horario->horario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'discapacidad') ?>:</th>
                    <td><?= Html::encode($model->discapacitado) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'banco_empleado') ?>:</th>
                    <td><?= Html::encode($model->bancoEmpleado->banco) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipo_cuenta') ?>:</th>
                    <td> <?php if ($model->tipo_cuenta == 'S'){?>
                         <?= 'AHORRO' ?>
                    <?php }else{?>
                        <?= 'CORRIENTE' ?>
                    <?php }?>     
                    </td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cuenta_bancaria') ?>:</th>
                    <td><?= Html::encode($model->cuenta_bancaria) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_centro_costo') ?>:</th>
                    <td ><?= Html::encode($model->centroCosto->centro_costo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'contrato') ?>:</th>
                    <td><?= Html::encode($model->contratado) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario_creador') ?>:</th>
                    <td><?= Html::encode($model->usuario_crear) ?></td>
                    
                </tr>
                <tr style="font-size: 85%;">
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario_Editado') ?>:</th>
                    <td><?= Html::encode($model->usuario_editar) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan="6"><?= Html::encode($model->observacion) ?></td>                    
                </tr>
            </table>
        </div>
    </div>
    <!--INICIO LOS TABS-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <?php
              $cont = count($contrato);
              $inca = count($incapacidad);
              $lice = count($licencia);
              $cred = count($credito);
              $estu = count($estudio);
             ?>
            <li role="presentation" class="active"><a href="#contrato" aria-controls="contrato" role="tab" data-toggle="tab">Contratos <span class="badge"><?= $cont ?></span></a></li>
            <li role="presentation"><a href="#incapacidad" aria-controls="incapacidad" role="tab" data-toggle="tab">Incapacidades <span class="badge"><?= $inca ?></span></a></li>
            <li role="presentation"><a href="#licencia" aria-controls="licencia" role="tab" data-toggle="tab">Licencias <span class="badge"><?= $lice ?></span></a></li>
            <li role="presentation"><a href="#credito" aria-controls="licencia" role="tab" data-toggle="tab">Créditos <span class="badge"><?= $cred ?></span></a></li>
            <li role="presentation"><a href="#estudio" aria-controls="estudio" role="tab" data-toggle="tab">Estudios <span class="badge"><?= $estu ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="contrato">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Número</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'Tipo contrato</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'Tiempo</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Grupo pago</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha final</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cargo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Salario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Activo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     foreach ($contrato as $valor):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $valor->id_contrato ?></td>
                                            <td><?= $valor->tipoContrato->abreviatura?></td>
                                            <td><?= $valor->tiempoServicio->tiempo_servicio ?></td>
                                            <td><?= $valor->grupoPago->grupo_pago ?></td>
                                            <td><?= $valor->fecha_inicio ?></td>
                                            <?php if($valor->fecha_final == '2099-12-31'){?>
                                                <td style='background-color:#B9D5CE;'><?= 'INDEFINIDO'?></td>
                                            <?php }else{?>
                                                <td style='background-color:#B9D5DE;'><?= $valor->fecha_final ?></td>
                                            <?php } ?>                                                
                                            
                                            <td><?= $valor->cargo->cargo ?></td>
                                            <td align="right"><?= '$'.number_format($valor->salario,0) ?></td>
                                            <td><?= $valor->activo ?></td>
                                            
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                        </div>
                    </div>   
                </div>
            </div>
            <!--INICIO EL OTRO TABS -->
            <div role="tabpanel" class="tab-pane" id="incapacidad">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Número</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha final</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Dias</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Medico</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Grupo pago</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Salario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Vlr_Incapacidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     foreach ($incapacidad as $valor):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $valor->numero_incapacidad ?></td>
                                            <td><?= $valor->codigoIncapacidad->nombre?></td>
                                            <td><?= $valor->codigo_diagnostico ?></td>
                                            <td><?= $valor->fecha_inicio ?></td>
                                            <td><?= $valor->fecha_final ?></td>
                                            <td><?= $valor->dias_incapacidad ?></td>
                                            <td><?= $valor->nombre_medico ?></td>
                                            <td><?= $valor->grupoPago->grupo_pago ?></td>
                                            <td><?= '$'.number_format($valor->salario,0) ?></td>
                                            <td><?= '$'.number_format($valor->vlr_liquidado,0) ?></td>
                                            
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                        </div>
                    </div>   
                </div>
            </div>
            <!--INICIO EL OTRO TABS -->
            <div role="tabpanel" class="tab-pane" id="licencia">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Número</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Nro contrato</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha final</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Dias</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Grupo pago</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Salario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Vlr_licencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     foreach ($licencia as $valor):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $valor->id_licencia_pk ?></td>
                                            <td><?= $valor->codigoLicencia->concepto?></td>
                                            <td><?= $valor->id_contrato ?></td>
                                            <td><?= $valor->fecha_desde ?></td>
                                            <td><?= $valor->fecha_hasta ?></td>
                                            <td><?= $valor->dias_licencia ?></td>
                                            <td><?= $valor->grupoPago->grupo_pago ?></td>
                                            <td><?= '$'.number_format($valor->salario,0) ?></td>
                                            <td><?= '$'.number_format($valor->vlr_licencia,0) ?></td>
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                        </div>
                    </div>   
                </div>
            </div>
            <!--INICIO EL OTRO TABS -->
            <div role="tabpanel" class="tab-pane" id="credito">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Número</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo_crédito</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Forma pago</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Vr. Crédito</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Vr. Cuota</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Nro cuotas</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Saldo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Grupo pago</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     foreach ($credito as $valor):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $valor->id_credito ?></td>
                                            <td><?= $valor->codigoCredito->nombre_credito?></td>
                                            <td><?= $valor->tipoPago->descripcion ?></td>
                                            <td><?= $valor->fecha_inicio ?></td>
                                            <td><?= '$'.number_format($valor->vlr_credito,0) ?></td>
                                            <td><?= '$'.number_format($valor->vlr_cuota,0) ?></td>
                                             <td><?= $valor->numero_cuotas ?></td>
                                            <td><?= '$'.number_format($valor->saldo_credito,0) ?></td>
                                            <td><?= $valor->grupoPago->grupo_pago ?></td>
                                            <td><?= $valor->usuariosistema ?></td>
                                            
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                        </div>
                    </div>   
                </div>
            </div>
            <!-- COMIENZA OTRO TABS-->
             <div role="tabpanel" class="tab-pane" id="estudio">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Titulo obtenido</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Institucion educativa</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Municipio</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha terminación</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Estudio/Profesión</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Año cursado</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Graduado</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Registro</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     foreach ($estudio as $valor):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $valor->id ?></td>
                                            <td><?= $valor->titulo_obtenido?></td>
                                            <td><?= $valor->institucion_educativa ?></td>
                                            <td><?= $valor->municipio->municipio ?></td>
                                             <td><?= $valor->fecha_inicio ?></td>
                                             <td><?= $valor->fecha_terminacion ?></td>
                                            <td><?= $valor->tipoEstudio->estudio ?></td>
                                            <td><?= $valor->anio_cursado ?></td>
                                            <td><?= $valor->graduadoestudio ?></td>
                                            <td><?= $valor->registro ?></td>
                                            <td><?= $valor->usuariosistema ?></td>
                                            
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                        </div>
                    </div>   
                </div>
            </div>
            
            
        </div>
    </div>    
</div>
