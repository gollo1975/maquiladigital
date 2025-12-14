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

$this->title = 'TRANSPORTADORA';
$this->params['breadcrumbs'][] = ['label' => 'Transportadora', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_transportadora;

?>
<div class="proveedor-view">

    <!--<?= Html::encode($this->title) ?>-->
    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
     
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
           Detalle del registro
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>Id:</th>
                    <td><?= $model->id_transportadora ?></td>
                    <th style='background-color:#F0F3EF;'>Tipo documento:</th>
                    <td><?= $model->tipoDocumento->tipo ?></td>
                    <th style='background-color:#F0F3EF;'>Nit/Cedula</th>
                    <td><?= $model->cedulanit ?></td>
                    <th style='background-color:#F0F3EF;' ></th>
                    <td></td>
                      <th style='background-color:#F0F3EF;' >Empresa:</th>
                    <td><?= $model->razon_social ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>Direccion</th>
                    <td><?= $model->direccion ?></td>
                    <th style='background-color:#F0F3EF;'>Email:</th>
                    <td><?= $model->email_transportadora ?></td>
                    <th style='background-color:#F0F3EF;'>Telefono:</th>
                    <td><?= $model->telefono ?></td>
                    <th style='background-color:#F0F3EF;'>Celular:</th>
                    <td><?= $model->celular ?></td>
                     <th style='background-color:#F0F3EF;'>F. registro:</th>
                    <td><?= $model->fecha_registro ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'>Departamento:</th>
                    <td><?= $model->departamento->departamento ?></td>
                    <th style='background-color:#F0F3EF;'>Municipio:</th>
                    <td><?= $model->municipio->municipio ?></td>
                     <th style='background-color:#F0F3EF;'>Contacto:</th>
                    <td><?= $model->contacto ?></td>
                    <th style='background-color:#F0F3EF;'>Celular contacto:</th>
                    <td><?= $model->celular_contacto ?></td>
                       <th style='background-color:#F0F3EF;'>user name:</th>
                    <td><?= $model->user_name ?></td>
                    
                </tr>
                 
            </table>
        </div>
    </div>
    <!--INICIO LOS TABS-->
   
</div>
