<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Producto */

$this->title = 'Prestaciones';
$this->params['breadcrumbs'][] = ['label' => 'Prestaciones sociales', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_prestacion;
$view = 'prestaciones sociales';
?>
<div class="prestaciones-sociales-view">

   <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?php 

    if($model->estado_generado == 0){?>
        <p>
           <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
           <?= Html::a('<span class="glyphicon glyphicon-export"></span> Generar', ['prestaciones-sociales/generarconceptos', 'id' => $model->id_prestacion, 'pagina' =>$pagina], ['class' => 'btn btn-info btn-xs']) ?>
       </p>
    <?php }else{
        if($model->estado_aplicado == 0){
            ?>   
           <p>
             <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
             <?= Html::a('<span class="glyphicon glyphicon-refresh"></span> Desgenerar', ['prestaciones-sociales/desgenerar', 'id' => $model->id_prestacion, 'pagina' => $pagina], ['class' => 'btn btn-default btn-xs']) ?>  
             <?= Html::a('<span class="glyphicon glyphicon-import"></span> Aplicar pagos', ['prestaciones-sociales/aplicarpagos', 'id' => $model->id_prestacion , 'pagina' => $pagina], ['class' => 'btn btn-warning btn-xs']) ?>
        <?php }else{
            if($model->estado_cerrado == 0){
                ?>
              <p>
                 <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
                 <?= Html::a('<span class="glyphicon glyphicon-refresh"></span> Desgenerar', ['prestaciones-sociales/desgeneraraplicar', 'id' => $model->id_prestacion, 'pagina' => $pagina], ['class' => 'btn btn-default btn-xs']) ?>  
                 <?= Html::a('<span class="glyphicon glyphicon-remove-circle"></span> Cerrar prestación', ['prestaciones-sociales/cerrarprestacion', 'id' => $model->id_prestacion, 'pagina' => $pagina], ['class' => 'btn btn-default btn-xs']) ?>
                 <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['prestaciones-sociales/imprimir', 'id' => $model->id_prestacion, 'pagina' => $pagina], ['class' => 'btn btn-default btn-xs']) ?> 
              </p> 
              
            <?php } else
                if($pagina == 1){?>
                    <p>
                      <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['comprobantepagoprestaciones'], ['class' => 'btn btn-primary btn-sm']) ?>
                      <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimir', 'id' => $model->id_prestacion], ['class' => 'btn btn-default btn-xs']) ?> 
                    </p>
                <?php }else{?>
                    <p>
                       <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
                       <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimir', 'id' => $model->id_prestacion], ['class' => 'btn btn-default btn-xs']) ?> 
                    </p> 
                    
                <?php}?>    
               
            <?php }
        }    
    }?>
   
    <div class="panel panel-success">
        <div class="panel-heading">
            Prestaciones sociales
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style='font-size:85%;'>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_prestacion') ?>:</th>
                    <td><?= Html::encode($model->id_prestacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nro_pago') ?>:</th>
                    <td><?= Html::encode($model->nro_pago) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'documento') ?>:</th>
                    <td><?= Html::encode($model->documento) ?></td>  
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_empleado') ?>:</th>
                    <td><?= Html::encode(mb_strtoupper($model->empleado->nombrecorto)) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'salario') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($model->salario,0)) ?></td> 
                </tr>   
                 <tr style='font-size:85%;'>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_inicio_contrato') ?>:</th>
                    <td><?= Html::encode($model->fecha_inicio_contrato) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_termino_contrato') ?>:</th>
                    <td><?= Html::encode($model->fecha_termino_contrato) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_grupo_pago') ?>:</th>
                    <td><?= Html::encode($model->grupoPago->grupo_pago) ?></td> 
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cargo') ?>:</th>
                    <td><?= Html::encode($model->contrato->cargo->cargo) ?></td>  
                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_devengado') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($model->total_devengado,0)) ?></td>
                   
                </tr>   
                <tr style='font-size:85%;'>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ultimo_pago_prima') ?>:</th>
                    <td><?= Html::encode($model->ultimo_pago_prima) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ultimo_pago_cesantias') ?>:</th>
                    <td><?= Html::encode($model->ultimo_pago_cesantias) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ultimo_pago_vacaciones') ?>:</th>
                    <td><?= Html::encode($model->ultimo_pago_vacaciones) ?></td>  
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_contrato') ?>:</th>
                    <td><?= Html::encode($model->id_contrato) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_deduccion') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($model->total_deduccion,0)) ?></td>
                </tr>  
                 <tr style='font-size:85%;'>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Banco') ?>:</th>
                    <td><?= Html::encode($model->empleado->bancoEmpleado->banco) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cuenta') ?>:</th>
                    <td><?= Html::encode($model->empleado->cuenta_bancaria) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipo_cuenta') ?>:</th>
                    <td><?= Html::encode($model->empleado->tipo_cuenta) ?></td>  
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'centro_trabajo') ?>:</th>
                    <td><?= Html::encode($model->contrato->centroTrabajo->centro_trabajo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_pagar') ?>:</th>
                    <td align="right"><?= Html::encode('$'.number_format($model->total_pagar,0)) ?></td>
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
    ]);?>
    <div>
          <ul class="nav nav-tabs" role="tablist">
            <?php $con = count($detalle_prestacion);?>
            <?php $conAdicion = count($adicion_prestacion);?>
            <?php $conDescuento = count($descuento_prestacion);?>
            <?php $conCredito = count($descuento_credito);?>
              
            <li role="presentation" class="active"><a href="#detalleprestacion" aria-controls="detalleprestacion" role="tab" data-toggle="tab">Administradoras <span class="badge"><?= $con ?></span></a></li>
            <li role="presentation"><a href="#credito" aria-controls="credito" role="tab" data-toggle="tab">Créditos <span class="badge"><?= $conCredito ?></span></a></li>
            <li role="presentation"><a href="#descuento" aria-controls="descuento" role="tab" data-toggle="tab">Descuentos <span class="badge"><?= $conDescuento ?></span></a></li>
            <li role="presentation"><a href="#adicionpago" aria-controls="adicionpago" role="tab" data-toggle="tab">Adición <span class="badge"><?= $conAdicion ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="detalleprestacion">
                <div class="table-responsive">
                    <div class="panel panel-success ">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead >
                                    <tr style='font-size:85%;'>
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'><b>Id</b></th>                        
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'><b>Concepto pago</b></th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha final</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Nro dias</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Dias ausentes</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Dias pagar</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Salario promedio</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><b>A. Transporte</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Total pagar</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     foreach ($detalle_prestacion as $dato):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $dato->id ?></td>
                                            <td><?= $dato->codigoSalario->nombre_concepto ?></td>
                                            <td><?= $dato->fecha_inicio ?></td>
                                            <td><?= $dato->fecha_final ?></td>
                                            <td><?= $dato->nro_dias ?></td>
                                            <td><?= $dato->dias_ausentes ?></td>
                                            <td><?= $dato->total_dias ?></td>
                                            <td align="right"><?= '$'.number_format($dato->salario_promedio_prima,0) ?></td>
                                            <td align="right"><?= '$'.number_format($dato->auxilio_transporte,0) ?></td>
                                            <td align="right"><?= '$'.number_format($dato->valor_pagar,0) ?></td>
                                             <?php
                                            if($model->estado_aplicado == 0){?>
                                                <td style= 'width: 35px;'>
                                                    <a href="<?= Url::toRoute(["prestaciones-sociales/editarconcepto", "id_adicion" => $dato->id, 'id' => $dato->id_prestacion, 'codigo' => $dato->codigo_salario, 'pagina' => $pagina]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                                                </td>
                                            <?php }else{?>
                                                <td style= 'width: 35px;'></td>
                                             
                                            <?php }?>  
                                             <td style= 'width: 35px;'><input type="checkbox" name="id_detalle[]" value="<?= $dato->id ?>"></td>
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                        </div>
                    </div>
                </div><!-- table responsive -->
                <?php
                if($model->estado_aplicado == 0){?>
                    <div class="panel-footer text-right"> 
                        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Adición', ['prestaciones-sociales/adicionsalario', 'id' => $model->id_prestacion , 'pagina' => $pagina], ['class' => 'btn btn-primary btn-sm']) ?>
                         <?= Html::a('<span class="glyphicon glyphicon-minus-sign"></span> Descuento', ['prestaciones-sociales/descuento', 'id' => $model->id_prestacion, 'pagina' => $pagina], ['class' => 'btn btn-primary btn-sm']) ?>
                        <?= Html::a('<span class="glyphicon glyphicon-minus-sign"></span> Créditos', ['prestaciones-sociales/descuentocredito','id_empleado' => $model->id_empleado, 'id' => $model->id_prestacion, 'pagina' => $pagina], ['class' => 'btn btn-primary btn-sm']) ?>
                        <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar", ["class" => "btn btn-danger btn-sm", 'name' => 'eliminardetalles']) ?>
                    </div>
                <?php }else{ ?>
                     <div class="panel-footer text-right"> 
                        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Adición', ['prestaciones-sociales/adicionsalario', 'id' => $model->id_prestacion], ['class' => 'btn btn-primary btn-sm disabled']) ?>
                         <?= Html::a('<span class="glyphicon glyphicon-minus-sign"></span> Descuento', ['prestaciones-sociales/descuento', 'id' => $model->id_prestacion], ['class' => 'btn btn-primary btn-sm disabled']) ?>
                        <?= Html::a('<span class="glyphicon glyphicon-minus-sign"></span> Créditos', ['prestaciones-sociales/descuentocredito','id_empleado' => $model->id_empleado, 'id' => $model->id_prestacion], ['class' => 'btn btn-primary btn-sm disabled']) ?>
                    </div>
                <?php } ?>
            
            </div><!--termina el tabs de detalle--> 
            <div role="tabpanel" class="tab-pane" id="credito">
                <div class="table-responsive">
                    <div class="panel panel-success ">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead >
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Nro crédito</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Tipo credito</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Valor credito</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Saldo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Deducción</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     foreach ($descuento_credito as $dcto_credito):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $dcto_credito->id_credito ?></td>
                                            <td><?= $dcto_credito->codigoSalario->nombre_concepto ?></td>
                                            <td><?= '$'.number_format($dcto_credito->valor_credito,0) ?></td> 
                                            <td><?= '$'.number_format($dcto_credito->saldo_credito,0) ?></td>
                                            <td><?= '$'.number_format($dcto_credito->deduccion,0) ?></td>
                                            <td><?= $dcto_credito->fecha_inicio?></td>
                                            <td><?= $dcto_credito->fecha_creacion ?></td>
                                            <td><?= $dcto_credito->usuariosistema ?></td>
                                            <?php
                                            if($model->estado_aplicado == 0){?>
                                                <td style= 'width: 25px;' >
                                                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>',            
                                                   ['/prestaciones-sociales/editarcredito','id_credito' => $dcto_credito->id, 'id'=>$dcto_credito->id_prestacion, 'pagina' => $pagina],
                                                       [
                                                           'title' => 'Editar',
                                                           'data-toggle'=>'modal',
                                                           'data-target'=>'#modaleditarcredito'.$dcto_credito->id,
                                                           'class' => 'btn btn-info btn-xs'
                                                       ]
                                                   );
                                                   ?>
                                                    <div class="modal remote fade" id="modaleditarcredito<?= $dcto_credito->id ?>">
                                                       <div class="modal-dialog modal-lg">
                                                           <div class="modal-content"></div>
                                                       </div>
                                                    </div>
                                               </td>    
                                            <?php }else{?>
                                                <td style= 'width: 35px;'></td>
                                            <?php }?>    
                                                
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                        </div>
                    </div>
                </div><!-- table responsive -->
            </div><!--termina el tabs de credito-->
            
            <div role="tabpanel" class="tab-pane" id="descuento">
                <div class="table-responsive">
                    <div class="panel panel-success ">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead >
                                    <tr style='font-size:85%;'>
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'><b>Id</b></th>  
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'><b>Nro prestación</b></th>
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'><b>Concepto</b></th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Valor descuento</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Fecha proceso</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Usuario</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                         <th scope="col" style='background-color:#B9D5CE;'></th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     foreach ($descuento_prestacion as $descuento):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $descuento->id_adicion ?></td>
                                            <td><?= $descuento->id_prestacion ?></td>
                                            <td><?= $descuento->codigoSalario->nombre_concepto ?></td>
                                            <td><?= '$'.number_format($descuento->valor_adicion,0) ?></td>
                                            <td><?= $descuento->fecha_creacion ?></td>
                                            <td><?= $descuento->usuariosistema ?></td>
                                            <?php
                                            if($model->estado_aplicado == 0){?>
                                                <td style= 'width: 35px;'>
                                                    <a href="<?= Url::toRoute(["prestaciones-sociales/update", "id_adicion" => $descuento->id_adicion, "tipo_adicion"=>$descuento->tipo_adicion, 'id' => $descuento->id_prestacion, 'pagina' => $pagina]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                                                </td>
                                                <td style= 'width: 35px;'>
                                                    <?= Html::a('', ['eliminaradicion', 'id_adicion' => $descuento->id_adicion, 'id' => $descuento->id_prestacion, 'pagina' => $pagina], [
                                                        'class' => 'glyphicon glyphicon-trash',
                                                        'data' => [
                                                            'confirm' => 'Esta seguro de eliminar el registro?',
                                                            'method' => 'post',
                                                        ],
                                                    ]) ?>
                                                </td>
                                            <?php }else{?>
                                                <td style= 'width: 35px;'>
                                                </td>
                                                <td style= 'width: 35px;'>
                                                </td>
                                            <?php }?>    
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                        </div>
                    </div>
                </div><!-- table responsive -->
            </div><!--termina el tabs de descuento-->
            
            <div role="tabpanel" class="tab-pane" id="adicionpago">
                <div class="table-responsive">
                    <div class="panel panel-success ">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead >
                                    <tr style='font-size:85%;'>
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'><b>Id</b></th>  
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'><b>Nro prestación</b></th>
                                        <th scope="col" align="center" style='background-color:#B9D5CE;'><b>Concepto</b></th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Valor adición</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Fecha proceso</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'><b>Usuario</b></th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     foreach ($adicion_prestacion as $adicion):?>
                                        <tr style='font-size:85%;'>
                                            <td><?= $adicion->id_adicion ?></td>
                                            <td><?= $adicion->id_prestacion ?></td>
                                            <td><?= $adicion->codigoSalario->nombre_concepto ?></td>
                                            <td><?= '$'.number_format($adicion->valor_adicion,0) ?></td>
                                            <td><?= $adicion->fecha_creacion ?></td>
                                            <td><?= $adicion->usuariosistema ?></td>
                                            <?php 
                                            if($model->estado_aplicado == 0){?>
                                                <td style= 'width: 35px;'>
                                                    <a href="<?= Url::toRoute(["prestaciones-sociales/update", "id_adicion" => $adicion->id_adicion, "tipo_adicion"=>$adicion->tipo_adicion, 'id' => $adicion->id_prestacion, 'pagina' => $pagina]) ?>" ><span class="glyphicon glyphicon-pencil align-center"></span></a>                   
                                                </td>
                                                <td style= 'width: 35px;'>
                                                    <?= Html::a('', ['eliminaradicion', 'id_adicion' => $adicion->id_adicion, 'id' => $adicion->id_prestacion, 'pagina' => $pagina], [
                                                        'class' => 'glyphicon glyphicon-trash align-center',
                                                        'data' => [
                                                            'confirm' => 'Esta seguro de eliminar el registro?',
                                                            'method' => 'post',
                                                        ],
                                                    ]) ?>
                                                </td>
                                            <?php }else{?>    
                                                <td style= 'width: 35px;'>
                                                    
                                                </td>
                                                <td style= 'width: 35px;'>
                                                </td>
                                            <?php }?>    
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                        </div>
                    </div>
                </div><!-- table responsive -->
            </div><!--termina el tabs de adicio-->
            
        </div>
    </div>     
    <?php ActiveForm::end(); ?>
</div>

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