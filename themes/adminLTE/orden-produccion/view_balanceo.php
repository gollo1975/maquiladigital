<?php


use app\models\Ordenproducciondetalle;
use app\models\Ordenproducciondetalleproceso;
use app\models\Ordenproduccion;
use app\models\Cliente;
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

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$this->title = 'Detalle balanceo';
$this->params['breadcrumbs'][] = ['label' => 'Detalle balanceo', 'url' => ['proceso']];
$this->params['breadcrumbs'][] = $model->idordenproduccion;
?>
<div class="ordenproduccionproceso-view">
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-default btn"> <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['produccionbalanceo'],['class' => 'btn btn-primary btn-xs']) ?></button>
        <div class="btn-group btn-sm" role="group">
            <button type="button" class="btn btn-info  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Modulos
              <span class="caret"></span>
            </button>

              <ul class="dropdown-menu">
                    <li><?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear ', ['/balanceo/create', 'idordenproduccion' => $model->idordenproduccion], ['target' => '_blank']) ?></li>
              </ul>
        </div>
    </div>    

    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle del registro
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nro_orden')?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?></th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Orden_Cliente') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Unidades') ?>:</th>
                    <td align="right"><?= Html::encode (''.number_format($model->cantidad),0) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechallegada') ?>:</th>
                    <td><?= Html::encode($model->fechallegada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_Inicio') ?>:</th>
                    <td><?= Html::encode($model->fechaprocesada) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechaentrega') ?>:</th>
                    <td><?= Html::encode($model->fechaentrega) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Codigo_producto') ?>:</th>
                    <td><?= Html::encode($model->codigoproducto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Sam_standar') ?>:</th>
                    <td><?= Html::encode($model->duracion.'  minutos') ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Servicio') ?>:</th>
                    <td colspan="4"><?= Html::encode($model->tipo->tipo) ?></td>
                </tr>
                <tr>
                    <button class="btn btn-primary btn-sm" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        Observaciones
                      </button>
                      <div class="collapse" id="collapseExample">
                          <div class="well" style="font-size: 85%;">
                              <?= $model->observacion ?> 
                        </div>
                     </div>
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
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#operaciones" aria-controls="operaciones" role="tab" data-toggle="tab">Flujo: <span class="badge"><?= count($operaciones) ?></span></a></li>
            <li role="presentation"><a href="#modulo" aria-controls="tallas" role="modulo" data-toggle="tab">Modulos: <span class="badge"><?= count($modulos) ?></span></a></li>
            <li role="presentation"><a href="#tallas" aria-controls="tallas" role="tab" data-toggle="tab">Tallas: <span class="badge"><?= count($modeldetalles) ?></span></a></li>
        </ul>
        <div class="tab-content">
           <div role="tabpanel" class="tab-pane active" id="operaciones">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Operación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Segundos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Minutos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Ordenamiento</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha creación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Maquina</th>
                                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($operaciones as $val):
                                        $flujo = app\models\FlujoOperaciones::find()->where(['=','idproceso', $val->idproceso])->andWhere(['=','idordenproduccion', $model->idordenproduccion])->all();
                                        if(!$flujo){
                                            $table = new app\models\FlujoOperaciones();
                                            $table->idproceso = $val->idproceso;
                                            $table->idordenproduccion = $model->idordenproduccion;
                                            $table->id_tipo = $val->id_tipo;
                                            $table->segundos = $val->duracion;
                                            $table->minutos = ''.number_format($val->duracion/60,2);
                                            $table->usuariosistema = Yii::$app->user->identity->username;
                                            $table->insert();
                                        }
                                        
                                    endforeach; ?>
                                    <?php
                                    $conminuto = 0;
                                    $consegundo = 0;
                                    $prenda = app\models\FlujoOperaciones::find()->where(['=', 'idordenproduccion', $model->idordenproduccion])->all();
                                    foreach ($prenda as $registro):?>
                                            <tr style="font-size: 85%;">
                                                 <td><?= $registro->id ?></td>
                                                <td><?= $registro->idproceso ?></td>
                                                <td><?= $registro->proceso->proceso ?></td>
                                                <td><?= ''.number_format($registro->segundos,0) ?></td>
                                                <td><?= ''.number_format($registro->minutos ,2) ?></td>
                                                 <td><?= $registro->orden_aleatorio ?></td>
                                                 <td><?= $registro->fecha_creacion ?></td>
                                                <?php 
                                                if($registro->id_tipo == ''){?>
                                                       <td><?= 'Not found' ?></td>
                                                 <?php }else{?>
                                                        <td><?= $registro->tipomaquina->descripcion ?></td>
                                                <?php }?> 
                                                <td style="width: 30px;"><input type="checkbox" name="id[]" value="<?= $registro->id ?>"></td>        
                                            </tr>
                                    <?php
                                       $consegundo += $registro->segundos;
                                       $conminuto += $registro->minutos;
                                    endforeach; ?>
                                </tbody> 
                                <td colspan="3"></td><td style="font-size: 85%;"><b>Total:</b> <?= $consegundo ?> <td style="font-size: 85%;"><b>Total:</b> <?= $conminuto ?></td><td colspan="4"></td>
                            </table>
                        </div>    
                    </div>
                </div>
               <div class="panel-footer text-right">
                     <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['orden-produccion/editarflujooperaciones', 'idordenproduccion' => $model->idordenproduccion],[ 'class' => 'btn btn-success btn-sm']) ?>                                                             
                     <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar", ["class" => "btn btn-danger btn-sm", 'name' => 'eliminarflujo']) ?>
              </div>
            </div>
           <!-- TERMINA EL TABS-->
           
           <div role="tabpanel" class="tab-pane" id="modulo">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Op</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Nro modulo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha terminación</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Observación</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modulos as $val): ?>
                                    <tr style="font-size: 85%;">
                                        <td><?= $val->id_balanceo ?></td>
                                        <td><?= $val->idordenproduccion ?></td>
                                         <td><?= $val->cliente->nombrecorto ?></td>
                                        <td><?= $val->cantidad_empleados ?></td>
                                        <td><?= $val->modulo ?></td>
                                        <td><?= $val->fecha_inicio ?></td>
                                        <td><?= $val->fecha_terminacion ?></td>
                                        <td><?= $val->observacion ?></td>
                                    </tr>
                                </tbody>
                                <?php endforeach; ?>
                            </table>
                        </div>    
                    </div>
                </div>    
            </div>
           <!--TERMINA EL TABS DE MODULO-->
           
            <div role="tabpanel" class="tab-pane" id="tallas">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Producto / Talla</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Unidades x Talla</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Total segundos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Total minutos</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Minutos confección</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modeldetalles as $val): ?>
                                    <tr style="font-size: 85%;">
                                        <td><?= $val->iddetalleorden ?></td>
                                        <td><?= $val->productodetalle->prendatipo->prenda.' / '.$val->productodetalle->prendatipo->talla->talla ?></td>
                                        <td align="center"><?= $val->cantidad ?></td>
                                         <td align="right"><?= ''.number_format($val->totalsegundos,0) ?></td>
                                        <td align="right"><?= ''.number_format($val->totalsegundos / 60,0) ?></td>
                                         <td align="right"><?= ''.number_format($val->totalsegundos / 60/$val->cantidad,2) ?></td>
                                    </tr>
                                </tbody>
                                <?php endforeach; ?>
                            </table>
                        </div>    
                    </div>
                </div>    
            </div>

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