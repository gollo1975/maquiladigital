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

$this->title = 'Detalle mecanico';
$this->params['breadcrumbs'][] = ['label' => 'Mecanico', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$view = 'mecanico';
?>

<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $table->id_mecanico], ['class' => 'btn btn-success btn-sm']) ?>
    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['eliminar', 'id' => $table->id_mecanico], [
        'class' => 'btn btn-danger btn-sm',
        'data' => [
            'confirm' => 'Esta seguro de eliminar el registro?',
            'method' => 'post',
        ],
    ]) ?>
    <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 18, 'codigo' => $table->id_mecanico,'view' => $view], ['class' => 'btn btn-default btn-sm']) ?>
</p>

<div class="panel panel-success">
    <div class="panel-heading">
        Información del mecanico
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <tr style="font-size: 85%;">
                <th style='background-color:#F0F3EF;'>Documento:</th>
                <td><?= $table->documento ?></td>
                <th style='background-color:#F0F3EF;'>Tipo documento:</th>
                <td><?= $table->tipoDocument->descripcion ?></td>
                <th style='background-color:#F0F3EF;'>Nombres:</th>
                <td><?= $table->nombres ?></td>
                <th style='background-color:#F0F3EF;'>Apellidos:</th>
                <td><?= $table->apellidos ?></td>
            </tr>
            <tr style="font-size: 85%;">
            
                <th style='background-color:#F0F3EF;'>Celular:</th>
                <td><?= $table->celular ?></td>
                <th style='background-color:#F0F3EF;'>Email:</th>
                <td><?= $table->email_mecanico ?></td>
                <th style='background-color:#F0F3EF;'>Dirección:</th>
                <td><?= $table->direccion_mecanico ?></td>
                <th style='background-color:#F0F3EF;'>Usuario:</th>
                <td><?= $table->usuario ?></td>    
            </tr>
            <tr style="font-size: 85%;">
                <th style='background-color:#F0F3EF;'>Registro:</th>
                <td><?= $table->fecha_registro ?></td>
                <th style='background-color:#F0F3EF;'>Departamento:</th>
                <td><?= $table->departamentoMecanico->departamento ?></td>
                <th style='background-color:#F0F3EF;' >Municipio:</th>
                <td><?= $table->municipio->municipio ?></td>
                 <th style='background-color:#F0F3EF;'>Activo:</th>
                 <td colspan="3"><?= $table->Activo ?></td>
            </tr>
            <tr style="font-size: 85%;">
                 <th style='background-color:#F0F3EF;'>Observaciones:</th>
                 <td colspan="8"><?= $table->observacion ?></td>
            </tr>
        </table>
    </div>
    
 </div>

