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
        </table>
    </div>
    <div class="panel-heading">
        Información Contacto
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <tr style="font-size: 85%;">
                <th style='background-color:#F0F3EF;'>Contacto:</th>
                <td><?= $table->contacto ?></td>
                <th style='background-color:#F0F3EF;'>Teléfono:</th>
                <td><?= $table->telefonocontacto ?></td>
                <th style='background-color:#F0F3EF;'>Celular:</th>
                <td colspan="4"><?= $table->celularcontacto ?></td>
               
            </tr>
        </table>
    </div>
    <div class="panel-heading">
        Información Tributaria
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <tr style="font-size: 85%;">
                <th style='background-color:#F0F3EF;'>Nit/Matricula:</th>
                <td><?= $table->nitmatricula ?></td>
                <th style='background-color:#F0F3EF;'>Forma de Pago:</th>
                <td><?php if ($table->formapago = 1){echo "Contado";} else {echo "Crédito";}  ?></td>
                <th style='background-color:#F0F3EF;'>Plazo:</th>
                <td colspan="4"><?= $table->plazopago ?></td>
                
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
        </table>
    </div>
    <div class="panel-heading">
        Información Confección
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <tr style="font-size: 85%;">
                <th style='background-color:#F0F3EF;'>Minuto Confección:</th>
                <td><?= $table->minuto_confeccion ?></td>
                <th style='background-color:#F0F3EF;'>Minuto Terminación:</th>
                <td colspan="6"><?= $table->minuto_terminacion ?></td>
               
            </tr>
        </table>
    </div>
    <div class="panel-heading">
        Observaciones
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <tr style="font-size: 85%;">
                <td><?= $table->observacion ?></td>
            </tr>
        </table>
    </div>    
</div>

