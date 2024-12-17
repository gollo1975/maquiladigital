<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Municipio;
use app\models\Departamento;
use app\models\TipoDocumento;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
?>

<?php

$this->title = 'Detalle Cliente';
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$view = 'clientes';
?>

<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['editar', 'id' => $table->idcliente], ['class' => 'btn btn-success btn-sm']) ?>
    <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 5, 'codigo' => $table->idcliente,'view' => $view, 'token' => $token], ['class' => 'btn btn-default btn-sm']) ?>
</p>
<div class="clientes-view">
    <div class="panel panel-success">
        <div class="panel-heading">
            Información Cliente
        </div>
        <div class="panel-body">
            <table class="table table-bordered">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>Código:</th>
                    <td><?= $table->idcliente ?></td>
                    <th style='background-color:#F0F3EF;'>Tipo Identificación:</th>
                    <td><?= $table->tipo->tipo ?></td>
                    <th style='background-color:#F0F3EF;'>Cedula/Nit:</th>
                    <td><?= $table->cedulanit ?></td>
                    <th style='background-color:#F0F3EF;'>DV:</th>
                    <td><?= $table->dv ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <?php if ($table->id_tipo_documento == 1){ ?>
                    <th style='background-color:#F0F3EF;'>Nombres:</th>
                    <td><?= $table->nombrecliente ?></td>
                    <th style='background-color:#F0F3EF;'>Apellidos:</th>
                    <td><?= $table->apellidocliente ?></td>
                    <?php } elseif ($table->id_tipo_documento == 5) { ?>
                    <th style='background-color:#F0F3EF;'>Razon Social:</th>
                    <td><?= $table->razonsocial ?></td>
                    <th style='background-color:#F0F3EF;'></th>
                    <td></td>
                    <?php } else { ?>
                    <th style='background-color:#F0F3EF;'>Nombres:</th>
                    <td><?= $table->nombrecliente ?></td>
                    <th style='background-color:#F0F3EF;'>Apellidos:</th>
                    <td><?= $table->apellidocliente ?></td>    
                    <?php }?>
                    <th style='background-color:#F0F3EF;'>Email:</th>
                    <td><?= $table->emailcliente ?></td>
                    <th style='background-color:#F0F3EF;' >Dirección:</th>
                    <td><?= $table->direccioncliente ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>Teléfono:</th>
                    <td><?= $table->telefonocliente ?></td>
                    <th style='background-color:#F0F3EF;'>Celular:</th>
                    <td><?= $table->celularcliente ?></td>
                    <th style='background-color:#F0F3EF;'>Departamento:</th>
                    <td><?= $table->departamento->departamento ?></td>
                    <th style='background-color:#F0F3EF;' >Municipio:</th>
                    <td><?= $table->municipio->municipio ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>Contacto:</th>
                    <td><?= $table->contacto ?></td>
                    <th style='background-color:#F0F3EF;'>Teléfono:</th>
                    <td><?= $table->telefonocontacto ?></td>
                    <th style='background-color:#F0F3EF;'>Celular:</th>
                    <td><?= $table->celularcontacto ?></td>
                     <th style='background-color:#F0F3EF;'>Forma de Pago:</th>
                    <td><?= $table->formaPago->concepto  ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>Tipo Regimen:</th>
                    <td><?= $table->regimen ?></td>
                    <th style='background-color:#F0F3EF;'>AutoRetenedor:</th>
                    <td><?= $table->autoretener ?></td>
                    <th style='background-color:#F0F3EF;'>Retención Fuente:</th>
                    <td><?= $table->retenerfuente ?></td>
                    <th style='background-color:#F0F3EF;'>Retención Iva:</th>
                    <td><?= $table->reteneriva ?></td>
                </tr>

                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>Observacion:</th>
                    <td colspan="8"><?= $table->observacion ?></td>
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
            <li role="presentation" class="active"><a href="#listadoproducto" aria-controls="listadoproducto" role="tab" data-toggle="tab">Listado de producto <span class="badge"><?= count($listado_producto) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="flujo">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style="font-size: 85%;">
                                        <th scope="col" style='background-color:#B9D5CE;'>Concepto</th>
                                        <th scope="col" style='background-color:#B9D5CE; text-align: center'>Valor confeccion</th>
                                        <th scope="col" style='background-color:#B9D5CE; text-align: center'>Valor terminacion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($listado_producto as $key => $val) {?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $val->tipoProducto->concepto?></td>
                                            <td style="background:#ADB9D1; font-weight:bold; text-align: center"><input type="text" name="valor_confeccion[]" style = "text-align: right" size = '10' value="<?= $val->valor_confeccion?>"></td>
                                            <td style="background:#ADB9D1; font-weight:bold; text-align: center"><input type="text" name="valor_terminacion[]" style = "text-align: right" size = '10' value="<?= $val->valor_terminacion?>"></td>
                                             <input type="hidden" name="listado[]" value="<?= $val->id ?>">
                                        </tr>
                                        
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if(count($listado_producto) > 0){?>
                            <div class="panel-footer text-right">
                                 <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-warning btn-sm", 'name' => 'actualizar_price']) ?>		
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--TERMINA TABS-->    
     <?php ActiveForm::end(); ?>
</div>
