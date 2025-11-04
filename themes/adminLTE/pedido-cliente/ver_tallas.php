<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\ComprobanteEgreso */

$this->title = 'Tallas de la referencia';
$this->params['breadcrumbs'][] = ['label' => 'Pedido del cliente', 'url' => ['view','id' => $id, 'token' => $token]];
$this->params['breadcrumbs'][] = $model->id_pedido;
$view = 'pedido-cliente';
?>
<div class="pedido-cliente-ver_talla">

    <p>
        <div class="btn-group btn-sm" role="group">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view', 'id' => $id, 'token' => $token], ['class' => 'btn btn-primary btn-sm']);?>
        </div>    
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h5><?= Html::encode($this->title) ?></h5>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Codigo') ?>:</th>
                    <td><?= Html::encode($model->codigo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Referencia') ?>:</th>
                    <td><?= Html::encode($model->referencia) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'user_name') ?>:</th>
                    <td><?= Html::encode($model->user_name) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cantidad') ?>:</th>
                    <td align="right"><?= Html::encode(''.number_format($model->cantidad,0)) ?></td>
                   
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
   
    <!--INICIOS DE TABS-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#listadotallas" aria-controls="listadotallas" role="tab" data-toggle="tab">Listado de tallas <span class="badge"><?= count($tallas_referencia) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="listadotallas">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style="font-size: 90%;">
                                        <th scope="col" style='background-color:#B9D5CE;'>Nombre de talla</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Numero pedido</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 25%'>Nota</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tallas_referencia as $val):?>
                                       <tr style="font-size: 90%;">
                                            <td><?= $val->talla->talla ?></td>
                                            <td><?= $model->referencia ?></td>
                                            <td><?= $model->pedido->numero_pedido ?></td>
                                            <td style="text-align: right;"><input type="text" name="cantidad[]" style="text-align: right" value="<?= $val->cantidad ?>" required></td>
                                            <td style="text-align: right;">
                                                <input type="text" name="nota[]" style="text-align: left" value="<?= $val->nota ?>" size="100" maxlength="100"  >
                                            </td>
                                            <input type="hidden" name="listado_tallas[]" value="<?= $val->codigo_talla ?>">
                                           <td style= 'width: 25px; height: 25px;'>
                                               <?php 
                                                if($model->pedido->autorizado == 0){
                                                    echo Html::a('', ['eliminar_lineas', 'id_talla' => $val->codigo_talla, 'id' => $id, 'token' => $token, 'id_referencia' =>$id_referencia], [
                                                        'class' => 'glyphicon glyphicon-trash',
                                                        'data' => [
                                                            'confirm' => 'Esta seguro de eliminar el registro?',
                                                            'method' => 'post',
                                                        ],
                                                    ]);
                                                }?>
                                            </td>
                                       </tr>  
                                    <?php endforeach;?>   
                                </<body>
                            </table>
                        </div>
                        <div class="panel-footer text-right"> 
                            <?php 
                            if($model->pedido->autorizado == 0){
                                echo Html::submitButton("<span class='glyphicon glyphicon-refresh'></span> Actualizar", ["class" => "btn btn-warning btn-sm", 'name' => 'actualizar_cantidades']);
                            }else{
                                echo Html::a('<span class="glyphicon glyphicon-download-alt"></span> Expotar excel', ['excel_tallas_pedido', 'id' => $id, 'token'=>$token, 'id_referencia' => $id_referencia], ['class' => 'btn btn-primary btn-sm']);
                            } ?>
                        </div>     
                    </div>    
                </div>
            </div> 
            <!--TERMINA TABS DE OPERACIONES-->
        </div>
    </div>
     <?php ActiveForm::end(); ?>
</div>

