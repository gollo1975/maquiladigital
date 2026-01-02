<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\ComprobanteEgreso */

$this->title = 'Tallas creadas';
$this->params['breadcrumbs'][] = ['label' => 'Pedidos', 'url' => ['view','id' => $id]];
$this->params['breadcrumbs'][] = $model->id_pedido;
$colores = ArrayHelper::map(app\models\Color::find()->orderBy('color ASC')->all(), 'id', 'color')
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
                    <td><?= Html::encode($model->inventario->codigo_producto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Referencia') ?>:</th>
                    <td><?= Html::encode($model->inventario->nombre_producto) ?></td>
                   
                </tr>
                <tr style="font-size: 85%;">
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
            <li role="presentation" class="active"><a href="#listadotallas" aria-controls="listadotallas" role="tab" data-toggle="tab">Tallas <span class="badge"><?= count($tallas) ?></span></a></li>
            <li role="presentation"><a href="#listadocolores" aria-controls="listadocolores" role="tab" data-toggle="tab">Colores <span class="badge"><?= count($ConColores) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="listadotallas">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style="font-size: 90%;">
                                        <th scope="col" style='background-color:#B9D5CE;width: 5%'>Codigo</th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 5%'>Talla</th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 5%'>Cantidad</th>
                                        <th scope="col" style='background-color:#B9D5CE; width: 10%'></th>
                                        <th scope="col" style='background-color:#B9D5CE;width: 1%''></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $confInventario = app\models\ConfiguracionInventario::findOne(1);
                                    foreach ($tallas as $val):?>
                                       <tr style="font-size: 85%;">
                                           <td><?= $val->idtalla ?></td>
                                            <td><?= $val->talla->talla ?></td>
                                            <td style="text-align: right;"><input type="text" name="cantidad[]" style="text-align: right" size = "5" value="<?= $val->cantidad ?>" required></td>
                                            <input type="hidden" name="listado_tallas[]" value="<?= $val->codigo ?>">
                                            <?php 
                                            if($confInventario->aplica_inventario_talla_color == 1){?>
                                                <td style= 'width: 25px; height: 25px;'>
                                                   <?php
                                                    $coloresSeleccionados = isset($coloresTallas[$val->idtalla]) ? $coloresTallas[$val->idtalla] : [];
                                                    $fieldName = 'colores_por_talla[' . $val->idtalla . ']'; 

                                                    echo Select2::widget([
                                                        'name' => $fieldName,
                                                        'data' => $colores, 
                                                        'value' => $coloresSeleccionados,
                                                        'options' => [
                                                            'placeholder' => 'Selecciona colores...',
                                                            'multiple' => true, 
                                                            'id' => 'select-color-' . $val->idtalla,
                                                        ],
                                                        'pluginOptions' => [
                                                            'allowClear' => true,
                                                        ],
                                                    ]);
                                                ?>
                                                </td>
                                            <?php }else {?>
                                                <td style= 'width: 25px; height: 25px;'></td>
                                           <?php }    
                                            if($model->pedido->autorizado == 0) {?>
                                                <td style="width: 10px; height: 10px">	
                                                        <?= Html::a('', ['eliminar_tallas', 'id' => $id, 'id_detalle' => $id_detalle, 'dato_eliminar' => $val->codigo, 'token' => $token], [
                                                          'class' => 'glyphicon glyphicon-trash',
                                                          'data' => [
                                                              'confirm' => 'Esta seguro de eliminar el registro?',
                                                              'method' => 'post',
                                                          ],
                                                        ]) ?>
                                                </td>
                                            <?php }else{?>
                                                <td style="width: 10px; height: 10px"></td>	
                                            <?php }?>    
                                       </tr>  
                                    <?php endforeach;?>   
                                </tbody>
                            </table>
                        </div>
                        <div class="panel-footer text-right"> 
                            <?php 
                            if($model->pedido->autorizado == 0 && count($tallas) > 0){
                                echo Html::submitButton("<span class='glyphicon glyphicon-refresh'></span> Actualizar", ["class" => "btn btn-warning btn-xs", 'name' => 'actualizar_cantidades']);
                            }else{
                                echo Html::a('<span class="glyphicon glyphicon-download-alt"></span> Expotar excel', ['excel_tallas_pedido', 'id' => $id, 'id_detalle' => $id_detalle], ['class' => 'btn btn-primary btn-xs']);
                            } ?>
                        </div>     
                    </div>    
                </div>
            </div> 
            <!--TERMINA TABS DE OPERACIONES-->
            <div role="tabpanel" class="tab-pane" id="listadocolores">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style="font-size: 85%;">
                                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Color</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                                         <th scope="col" style='background-color:#B9D5CE'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($ConColores as $color) {?>
                                        <tr style="font-size: 85%;">
                                            <td> <?= $color->id ?></td>
                                            <td> <?= $color->colores->color ?></td>
                                            <td> <?= $color->tallas->talla ?></td>
                                            <?php if($model->pedido->autorizado == 0) {?>
                                                <td style="width: 20px; height: 20px">	
                                                        <?= Html::a('', ['eliminar_colores', 'id' => $id, 'id_detalle' => $id_detalle, 'dato_eliminar' => $color->id_entrada,'token' => $token], [
                                                          'class' => 'glyphicon glyphicon-trash',
                                                          'data' => [
                                                              'confirm' => 'Esta seguro de eliminar el registro?',
                                                              'method' => 'post',
                                                          ],
                                                        ]) ?>
                                                </td>
                                            <?php }else {?>
                                                <td style="width: 20px; height: 20px"></td>
                                            <?php }?>    
                                        </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--TERMINA SEGUINDO TABS-->
        </div>
    </div>
     <?php ActiveForm::end(); ?>
</div>

