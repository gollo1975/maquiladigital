<?php
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
/* @var $model app\models\Empleado */

$this->title = 'AGENTES COMERCIALES';
$this->params['breadcrumbs'][] = ['label' => 'Agentes comerciales', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_agente;
?>
<div class="agentes-comerciales-view">

    <!--<?= Html::encode($this->title) ?>-->
    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
        
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
           AGENTES COMERCIALES
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>Id:</th>
                    <td><?= $model->id_agente ?></td>
                    <th style='background-color:#F0F3EF;'>Tipo documento:</th>
                    <td><?= $model->tipoDocumento->tipo ?></td>
                    <th style='background-color:#F0F3EF;'>Documento:</th>
                    <td><?= $model->nit_cedula ?></td>
                    <th style='background-color:#F0F3EF;' >Agente comercial:</th>
                    <td><?= $model->nombre_completo ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>Email:</th>
                    <td><?= $model->email_agente ?></td>
                    <th style='background-color:#F0F3EF;'>Celular:</th>
                    <td><?= $model->celular_agente ?></td>
                    <th style='background-color:#F0F3EF;'>Activo:</th>
                    <td><?= $model->estadoRegistro ?></td>
                     <th style='background-color:#F0F3EF;'>Direccion:</th>
                    <td><?= $model->direccion ?></td>
                 

                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>User name:</th>
                    <td><?= $model->user_name ?></td>
                    <th style='background-color:#F0F3EF;'>Departamento:</th>
                    <td><?= $model->codigoDepartamento->departamento ?></td>
                    <th style='background-color:#F0F3EF;'>Municipio:</th>
                    <td><?= $model->codigoMunicipio->municipio ?></td>
                    <th style='background-color:#F0F3EF;'>Fecha registro:</th>
                    <td><?= $model->fecha_registro ?></td>
                </tr>
                                
                 
            </table>
        </div>
    </div>
    <!--INICIO LOS TABS-->
   
</div>   
  

