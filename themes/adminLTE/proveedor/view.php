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

$this->title = 'Detalle Proveedor';
$this->params['breadcrumbs'][] = ['label' => 'Proveedores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$view = 'proveedor';
?>

<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['editar', 'id' => $table->idproveedor], ['class' => 'btn btn-success btn-sm']) ?>
    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['eliminar', 'id' => $table->idproveedor], [
        'class' => 'btn btn-danger btn-sm',
        'data' => [
            'confirm' => 'Esta seguro de eliminar el registro?',
            'method' => 'post',
        ],
    ]) ?>
    <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 5, 'codigo' => $table->idproveedor,'view' => $view, 'token' => $token], ['class' => 'btn btn-default btn-sm']) ?>
</p>

<div class="panel panel-success">
    <div class="panel-heading">
        Información Proveedor
    </div>
    <div class="panel-body">
       <table class="table table-bordered table-striped table-hover">
            <tr style="font-size: 85%;">
                <th style='background-color:#F0F3EF;'>Código:</th>
                <td><?= $table->idproveedor ?></td>
                <th style='background-color:#F0F3EF;'>Tipo Identificación:</th>
                <td><?= $table->tipo->tipo ?></td>
                <th style='background-color:#F0F3EF;'>Cedula/Nit:</th>
                <td><?= $table->cedulanit ?></td>
                <th style='background-color:#F0F3EF;' >DV:</th>
                <td><?= $table->dv ?></td>
            </tr>
            <tr style="font-size: 85%;">
                <?php if ($table->id_tipo_documento == 1){ ?>
                <th style='background-color:#F0F3EF;'>Nombres:</th>
                <td><?= $table->nombreproveedor ?></td>
                <th style='background-color:#F0F3EF;'>Apellidos:</th>
                <td><?= $table->apellidoproveedor ?></td>
                <?php } elseif ($table->id_tipo_documento == 5) { ?>
                <th style='background-color:#F0F3EF;'>Razon Social:</th>
                <td><?= $table->razonsocial ?></td>
                <th style='background-color:#F0F3EF;'></th>
                <td></td>
                <?php } else { ?>
                <th style='background-color:#F0F3EF;'>Nombres:</th>
                <td><?= $table->nombreproveedor ?></td>
                <th style='background-color:#F0F3EF;'>Apellidos:</th>
                <td><?= $table->apellidoproveedor ?></td>    
                <?php }?>
                <th style='background-color:#F0F3EF;'>Email:</th>
                <td><?= $table->emailproveedor ?></td>
                <th style='background-color:#F0F3EF;' >Dirección:</th>
                <td><?= $table->direccionproveedor ?></td>
            </tr>
            <tr style="font-size: 85%;">
                <th style='background-color:#F0F3EF;'>Teléfono:</th>
                <td><?= $table->telefonoproveedor ?></td>
                <th style='background-color:#F0F3EF;'>Celular:</th>
                <td><?= $table->celularproveedor ?></td>
                <th style='background-color:#F0F3EF;'>Departamento:</th>
                <td><?= $table->departamento->departamento ?></td>
                <th style='background-color:#F0F3EF;'>Municipio:</th>
                <td><?= $table->municipio->municipio ?></td>
            </tr>
            <tr style="font-size: 85%;">
                <th style='background-color:#F0F3EF;'>Entidad Bancaria:</th>
                <td><?= $table->banco ?></td>
                <th style='background-color:#F0F3EF;'>Tipo Cuenta:</th>
                <td><?= $table->tcuenta ?></td>
                <th style='background-color:#F0F3EF;'>Numero Cuenta:</th>
                <td><?= $table->cuentanumero ?></td>
                <th style='background-color:#F0F3EF;'>Maquila:</th>
                <td><?= $table->generamoda ?></td>
            </tr>
        </table>
    </div>
    <div class="panel-heading">
        Información Contacto
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
            <tr style="font-size: 85%;">
                <th style='background-color:#F0F3EF;'>Contacto:</th>
                <td><?= $table->contacto ?></td>
                <th style='background-color:#F0F3EF;'>Teléfono:</th>
                <td><?= $table->telefonocontacto ?></td>
                <th style='background-color:#F0F3EF;'>Celular:</th>
                <td><?= $table->celularcontacto ?></td>
                <th style='background-color:#F0F3EF;'></th>
                <td></td>
            </tr>
        </table>
    </div>
    <div class="panel-heading">
        Información Tributaria
    </div>
    <div class="panel-body">
       <table class="table table-bordered table-striped table-hover">
            <tr style="font-size: 85%;">
                <th style='background-color:#F0F3EF;'>Nit/Matricula:</th>
                <td><?= $table->nitmatricula ?></td>
                <th style='background-color:#F0F3EF;'>Forma de Pago:</th>
                <td><?php if ($table->formapago = 1){echo "Contado";} else {echo "Crédito";}  ?></td>
                <th style='background-color:#F0F3EF;'>Plazo:</th>
                <td><?= $table->plazopago ?></td>
                <th style='background-color:#F0F3EF;'></th>
                <td></td>
            </tr>
            <tr style="font-size: 85%;">
                <th style='background-color:#F0F3EF;'>Tipo Regimen:</th>
                <td><?= $table->regimen ?></td>
                <th style='background-color:#F0F3EF;'>AutoRetenedor:</th>
                <td><?= $table->autoretener ?></td>
                <th style='background-color:#F0F3EF;'>Naturaleza:</th>
                <td><?= $table->naturalezas ?></td>
                <th style='background-color:#F0F3EF;'>Sociedad:</th>
                <td><?= $table->sociedades ?></td>
            </tr>
        </table>
    </div>
    <div class="panel-heading">
        Observaciones
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
            <tr>
                <td><?= $table->observacion ?></td>
            </tr>
        </table>
    </div>    
</div>

