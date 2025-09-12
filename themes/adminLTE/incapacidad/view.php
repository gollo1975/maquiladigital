<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\Session;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $model app\models\Producto */

$this->title = 'Detalle incapacidad';
$this->params['breadcrumbs'][] = ['label' => 'Detalle de incapacidad', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_incapacidad;
?>
<div class="incapacidad-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?php if($token == 0){?>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_incapacidad], ['class' => 'btn btn-success btn-sm']) ?>
        <?php }else{
            echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index_search'], ['class' => 'btn btn-primary btn-sm']);
        }?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle incapacidad
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style='font-size: 85%;'>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_incapacidad') ?>:</th>
                    <td><?= Html::encode($model->numero_incapacidad) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_incapacidad') ?>:</th>
                    <td><?= Html::encode($model->codigoIncapacidad->nombre) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_diagnostico') ?>:</th>
                    <td><?= Html::encode($model->codigo_diagnostico) ?></td>  
                </tr>   
                 <tr style='font-size: 85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_inicio') ?>:</th>
                    <td><?= Html::encode($model->fecha_inicio) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_final') ?>:</th>
                    <td><?= Html::encode($model->fecha_final) ?></td>    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'dias_incapacidad') ?>:</th>
                    <td><?= Html::encode($model->dias_incapacidad) ?></td>
                </tr>          
                 <tr style='font-size: 85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_documento_fisico') ?>:</th>
                    <td><?= Html::encode($model->fecha_documento_fisico) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_aplicacion') ?>:</th>
                    <td><?= Html::encode($model->fecha_aplicacion) ?></td>    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nombre_medico') ?>:</th>
                    <td><?= Html::encode($model->nombre_medico) ?></td>
                    
                   
                </tr>          
                 <tr style='font-size: 85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'identificacion') ?>:</th>
                    <td><?= Html::encode($model->identificacion) ?></td>    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_empleado') ?>:</th>
                    <td><?= Html::encode($model->empleado->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_grupo_pago') ?>:</th>
                    <td><?= Html::encode($model->grupoPago->grupo_pago) ?></td>
                </tr>  
                <tr style='font-size: 85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'transcripcion') ?>:</th>
                    <td><?= Html::encode($model->transcripcionincapacidad) ?></td>    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cobrar_administradora') ?>:</th>
                    <td><?= Html::encode($model->cobraradministradora) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'aplicar_adicional') ?>:</th>
                    <td><?= Html::encode($model->aplicaradicional) ?></td>
                </tr>    
                <tr style='font-size: 85%;'>
                     
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'pagar_empleado') ?>:</th>
                    <td><?= Html::encode($model->pagarempleado) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'prorroga') ?>:</th>
                    <td><?= Html::encode($model->prorrogaincapacidad) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_entidad_salud') ?>:</th>
                    <td><?= Html::encode($model->entidadSalud->entidad) ?></td>  
                </tr>   
                <tr style='font-size: 85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'vlr_liquidado') ?>:</th>
                    <td><?= Html::encode('$'.number_format($model->vlr_liquidado,0)) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'vlr_cobro_administradora') ?>:</th>
                     <td><?= Html::encode('$'.number_format($model->vlr_cobro_administradora,0)) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'usuariosistema') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                </tr>      
                  <tr style='font-size: 85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan="6"><?= Html::encode($model->observacion) ?></td>
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
                   Registros:<span class="badge"><?= $registros ?></span>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-hover">
                    <thead >
                        <tr style='font-size: 85%;'>
                            <td scope="col" align="center"><b>Id</b></td>                        
                            <td scope="col" align="center"><b>Observación</b></td>                        
                            <th scope="col">Fecha_proceso</th>                        
                            <td scope="col" align="center"><b>Usuario</b></td>   
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($seguimiento as $val): ?>
                        <tr style='font-size: 85%;'>
                            <td><?= $val->id_seguimiento ?></td>
                            <td><?= $val->nota ?></td>
                            <td><?= $val->fecha_proceso ?></td>
                            <td><?= $val->usuariosistema ?></td>
                            <td style=' width: 25px;'>
                               <a href="<?= Url::toRoute(["incapacidad/editarseguimiento",'id_incapacidad'=>$model->id_incapacidad, "id_seguimiento" => $val->id_seguimiento]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                            </td>                            
                        </tr>
                    </tbody>
                    <?php endforeach; ?>
                </table>
            </div>     
            <?php if($token == 0){?>
                <div class="panel-footer text-right">                    
                     <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nuevo', ['incapacidad/nuevoseguimiento', 'id_incapacidad' => $model->id_incapacidad], ['class' => 'btn btn-success btn-sm']) ?>                    
                </div>  
            <?php }?>
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