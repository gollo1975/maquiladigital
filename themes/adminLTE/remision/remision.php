<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Session;
use yii\db\ActiveQuery;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Remision */

$this->title = 'Remision de Entrega';
$this->params['breadcrumbs'][] = ['label' => 'Orden Produccion', 'url' => ['orden-produccion/view', 'id' => $idordenproduccion]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="Fichatiempo-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['/orden-produccion/view', 'id' => $idordenproduccion], ['class' => 'btn btn-primary btn-sm']);
        if($model->cerrar_remision == 0){         
            if ($model->numero == 0) {?> 
                <?= Html::a('<span class="glyphicon glyphicon-check"></span> Generar Nro', ['generarnro', 'id' => $model->id_remision], ['class' => 'btn btn-default btn-sm']);
            }else{ ?>
                <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimir', 'id' => $model->id_remision], ['class' => 'btn btn-default btn-sm']); ?>
                <?= Html::a('<span class="glyphicon glyphicon-folder-close"></span> Cerrar', ['cerrarremision', 'id' => $model->id_remision], ['class' => 'btn btn-default btn-sm',
                     'data' => ['confirm' => 'Esta seguro que desea cerrar la remisión de auditoria No '. $model->numero. '', 'method' => 'post']]); ?>
                <?=  Html::a('<span class="glyphicon glyphicon-list-alt"></span> Segundas', ['/remision/clasificarsegundas', 'id' => $model->id_remision,'id_orden' => $model->idordenproduccion], ['class' => 'btn btn-default btn-sm']);?>                             
            <?php } ?>

             <!-- Editar modal detalle -->
             <a href="#" data-toggle="modal" data-target="#fecha<?= $model->idordenproduccion ?>" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-pencil"></span> Mod Fecha</a>
             <div class="modal fade" role="dialog" aria-hidden="true" id="fecha<?= $model->idordenproduccion ?>">
                 <div class="modal-dialog">
                     <div class="modal-content">
                         <div class="modal-header">
                             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                             <h4 class="modal-title">Remisión</h4>
                         </div>                            
                         <?= Html::beginForm(Url::toRoute(["remision/fechamodificar", 'id' => $model->idordenproduccion]), "POST") ?>                            

                         <div class="modal-body">
                             <div class="panel panel-success">
                                 <div class="panel-heading">
                                     <h4>Modificar Fecha de Remisión</h4>
                                 </div>
                                 <div class="panel-body">
                                     <div class="col-lg-2">
                                         <label>Fecha:</label>
                                     </div>
                                     <div class="col-lg-3">
                                         <input type="date" name="fecha" value="<?php echo $model->fechacreacion ?>" size="50"  required>
                                     </div>                                                                                
                                 </div>
                             </div>
                         </div>
                         <div class="modal-footer">
                             <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                             <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Modificar</button>
                         </div>
                         <?= Html::endForm() ?>
                     </div><!-- /.modal-content -->
                 </div><!-- /.modal-dialog -->
             </div><!-- /.modal -->
        <?php }else{ ?>     
             <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimir', 'id' => $model->id_remision], ['class' => 'btn btn-default btn-sm']); ?>
             <?=  Html::a('<span class="glyphicon glyphicon-eye-close"></span> Ver segundas', ['/remision/clasificarsegundas', 'id' => $model->id_remision,'id_orden' => $model->idordenproduccion], ['class' => 'btn btn-default btn-sm']);?>                             
        <?php }?>      

    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Remision de Entrega
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_remision') ?>:</th>
                    <td><?= Html::encode($model->id_remision) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idordenproduccion') ?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechacreacion') ?>:</th>
                    <td><?= Html::encode($model->fechacreacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_tulas') ?>:</th>
                    <td><?= Html::encode($model->total_tulas) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Op_Cliente') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion->ordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigoProducto') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion->codigoproducto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero') ?>:</th>
                    <td><?= Html::encode($model->numero) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_exportacion') ?>:</th>
                    <td><?= Html::encode($model->total_exportacion) ?></td>
                </tr>
              
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Contacto') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion->cliente->contacto) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'totalsegundas') ?>:</th>
                    <td><?= Html::encode($model->totalsegundas) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_colombia') ?>:</th>
                    <td><?= Html::encode($model->total_colombia) ?></td>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'TipoOrden') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion->tipo->tipo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Unidades') ?>:</th>
                    <td><?= Html::encode($model->ordenproduccion->cantidad) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_confeccion') ?>:</th>
                    <td><?= Html::encode($model->total_confeccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_despachadas') ?>:</th>
                    <td><?= Html::encode($model->total_despachadas) ?></td>
                </tr>
            </table>
        </div>
    </div>
    

</div>
<?php
$form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
        ]);
?>
<?php
$colores = ArrayHelper::map(app\models\Color::find()->all(), 'id', 'color');

?>
<div class="panel panel-success ">
    <div class="panel-heading">
        Lineas de empaque  <span class="badge"> <?= $count ?></span>
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-striped table-responsive-xl">
            <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Color</th>
                    <th scope="col" style='background-color:#B9D5CE;'>OC</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Tula</th>
                        <?php if ($datostallas){ 
                            foreach ($datostallas as $val): ?>
                            <th scope="col" style='background-color:#B9D5CE;'><?= $val ?></th>
                        <?php endforeach; 
                     } else {  ?>
                        <th scope="col"></th>
                    <?php }?>    
                    <th scope="col" style='background-color:#B9D5CE;'>Estado</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Unidad por Tula</th>                    
                    <th scope="col" style='background-color:#B9D5CE;'></th>
                </tr>
            </thead>
            <tbody>
                <?php                    
                    $ttula = 0;
                    $tunidades = 0;                    
                    $total = 0;
                ?>
                <?php foreach ($remisiondetalle as $val): ?>                
                    <tr style="font-size: 85%;">                                            
                        <td style="padding-left: 1;padding-right: 1;"><?= Html::dropdownList('color[]', $val->id_color ,$colores, ['class' => 'col-xs-13','prompt' => 'Seleccione...','required' => true]) ?>
                        <?php if ($val->oc == 1) { ?>
                            <td style="padding-left: 1;padding-right: 1;"><?= Html::dropdownList('oc[]', $val->oc ,['0' => 'COLOMBIA', '1' => 'EXPORTACION'], ['class' => 'col-xs-13','prompt' => 'Seleccione...','required' => true, 'style'=> 'background-color:silver']) ?>
                        <?php } else { ?>    
                            <td style="padding-left: 1;padding-right: 1;"><?= Html::dropdownList('oc[]', $val->oc ,['0' => 'COLOMBIA', '1' => 'EXPORTACION'], ['class' => 'col-xs-13','prompt' => 'Seleccione...','required' => true]) ?>
                        <?php } ?>        
                        <td style="padding-left: 1;padding-right: 1;"><input type="text" name="tula[]" value="<?= $val->tula ?>" size="1" onkeypress="return esInteger(event)" required></td>
                        <?php if ($val->oc == 1 || $val->estado == 1) { ?>
                            <?php if ($val->txs == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="xs[]" value="<?= $val->xs ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->ts == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="s[]" value="<?= $val->s ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->tm == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="m[]" value="<?= $val->m ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>    
                            <?php if ($val->tl == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="l[]" value="<?= $val->l ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->txl == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="xl[]" value="<?= $val->xl ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->txxl == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="xxl[]" value="<?= $val->xxl ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>    
                            <?php if ($val->t2 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="2[]" value="<?= $val['2'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t4 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="4[]" value="<?= $val['4'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t6 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="6[]" value="<?= $val['6'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t8 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="8[]" value="<?= $val['8'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t10 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="10[]" value="<?= $val['10'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t12 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="12[]" value="<?= $val['12'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t14 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="14[]" value="<?= $val['14'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t16 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="16[]" value="<?= $val['16'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t18 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="18[]" value="<?= $val['18'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t20 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="20[]" value="<?= $val['20'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t22 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="22[]" value="<?= $val['22'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t28 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="28[]" value="<?= $val['28'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t30 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="30[]" value="<?= $val['30'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t32 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="32[]" value="<?= $val['32'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t34 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="34[]" value="<?= $val['34'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t36 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="36[]" value="<?= $val['36'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t38 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="38[]" value="<?= $val['38'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>
                            <?php if ($val->t42 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="42[]" value="<?= $val['42'] ?>" size="1" onkeypress="return esInteger(event)" style="background-color:silver" required></td>
                            <?php } ?>    
                            <td style="padding-left: 1;padding-right: 1;"><?= Html::dropdownList('estado[]', $val->estado ,['0' => 'PRIMERA', '1' => 'SEGUNDA'], ['class' => 'col-xs-13','prompt' => 'Seleccione...','required' => true, 'style'=> 'background-color:silver']) ?>
                        <?php } else { ?>
                            <?php if ($val->txs == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="xs[]" value="<?= $val->xs ?>" size="1" onkeypress="return esInteger(event)"  required></td>
                            <?php } ?>
                            <?php if ($val->ts == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="s[]" value="<?= $val->s ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->tm == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="m[]" value="<?= $val->m ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>    
                            <?php if ($val->tl == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="l[]" value="<?= $val->l ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->txl == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="xl[]" value="<?= $val->xl ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->txxl == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="xxl[]" value="<?= $val->xxl ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>    
                            <?php if ($val->t2 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="2[]" value="<?= $val['2'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t4 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="4[]" value="<?= $val['4'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t6 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="6[]" value="<?= $val['6'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t8 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="8[]" value="<?= $val['8'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t10 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="10[]" value="<?= $val['10'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t12 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="12[]" value="<?= $val['12'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t14 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="14[]" value="<?= $val['14'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t16 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="16[]" value="<?= $val['16'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t18 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="18[]" value="<?= $val['18'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t20 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="20[]" value="<?= $val['20'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t22 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="22[]" value="<?= $val['22'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t28 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="28[]" value="<?= $val['28'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t30 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="30[]" value="<?= $val['30'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t32 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="32[]" value="<?= $val['32'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t34 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="34[]" value="<?= $val['34'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t36 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="36[]" value="<?= $val['36'] ?>" size="1" onkeypress="return esInteger(event)"  required></td>
                            <?php } ?>
                            <?php if ($val->t38 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="38[]" value="<?= $val['38'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <?php if ($val->t42 == 1) { ?>    
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="42[]" value="<?= $val['42'] ?>" size="1" onkeypress="return esInteger(event)" required></td>
                            <?php } ?>
                            <td style="padding-left: 1;padding-right: 1;"><?= Html::dropdownList('estado[]', $val->estado ,['0' => 'PRIMERA', '1' => 'SEGUNDA'], ['class' => 'col-xs-13','prompt' => 'Seleccione...','required' => true]) ?>
                        <?php } ?>                                                     
                        <td style="padding-left: 1;padding-right: 1;"><?= $val->unidades ?></td>
                        <input type="hidden" name="id_remision_detalle[]" value="<?= $val->id_remision_detalle ?>">
                        <td>
                        <?php if($model->cerrar_remision == 0){?>    
                            <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['eliminar', 'id' => $model->idordenproduccion, 'iddetalle' => $val->id_remision_detalle], [
                                     'class' => '',
                                     'data' => [
                                         'confirm' => 'Esta seguro de eliminar el registro?',
                                         'method' => 'post',
                                     ],
                                 ])
                             ?>
                         <?php } ?>   
                        </td>
                    </tr>
                </tbody>
                <?php                    
                    
                    $ttula = $ttula + $val->tula;
                    $tunidades = $tunidades + $val->unidades;
                    
                ?>
            <?php endforeach; ?>
            <tr>
                <td></td>
                <th scope="col">Totales Cliente:</th>
                <th scope="col"><?= $ttula ?></th>
                <?php if ($datostallas){ 
                    foreach ($datostallas as $val): ?>                    
                        <?php $ordendetalle = app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion',$model->idordenproduccion])->all();
                        foreach ($ordendetalle as $val2){
                            if ($val == $val2->productodetalle->prendatipo->talla->talla){
                                $total = $val2->cantidad;                                
                            }
                        }
                        
                        ?>
                        <th scope="col"><?= $total ?></th>
                <?php endforeach; 
                 } else {  ?>
                    <th scope="col"></th>
                <?php }?>
                <th scope="col"></th>
                <th scope="col"><?= $model->ordenproduccion->cantidad ?></th>                
                <td></td>
            </tr>
            <tr>
                <td></td>
                <th scope="col">Totales Confección:</th>
                <th scope="col"><?= $ttula ?></th>
                <?php if ($datostallas){ 
                    foreach ($datostallas as $val): ?>
                        <?php if ($val == 'xs' or $val == 'XS'){ ?>
                            <th scope="col"><?= $cxs ?></th>
                        <?php } ?>
                        <?php if ($val == 's' or $val == 'S'){ ?>
                            <th scope="col"><?= $cs ?></th>
                        <?php } ?>
                        <?php if ($val == 'm' or $val == 'M'){ ?>
                            <th scope="col"><?= $cm ?></th>
                        <?php } ?>
                        <?php if ($val == 'l' or $val == 'L'){ ?>
                            <th scope="col"><?= $cl ?></th>
                        <?php } ?>
                        <?php if ($val == 'xl' or $val == 'XL'){ ?>
                            <th scope="col"><?= $cxl ?></th>
                        <?php } ?>
                        <?php if ($val == 'xxl' or $val == 'XXL'){ ?>
                            <th scope="col"><?= $cxxl ?></th>
                        <?php } ?>    
                        <?php if ($val == '2' or $val == '2'){ ?>
                            <th scope="col"><?= $c2 ?></th>
                        <?php } ?>
                        <?php if ($val == '4' or $val == '4'){ ?>
                            <th scope="col"><?= $c4 ?></th>
                        <?php } ?>
                        <?php if ($val == '6' or $val == '6'){ ?>
                            <th scope="col"><?= $c6 ?></th>
                        <?php } ?>
                        <?php if ($val == '8' or $val == '8'){ ?>
                            <th scope="col"><?= $c8 ?></th>
                        <?php } ?>
                        <?php if ($val == '10' or $val == '10'){ ?>
                            <th scope="col"><?= $c10 ?></th>
                        <?php } ?>
                        <?php if ($val == '12' or $val == '12'){ ?>
                            <th scope="col"><?= $c12 ?></th>
                        <?php } ?>
                        <?php if ($val == '14' or $val == '14'){ ?>
                            <th scope="col"><?= $c14 ?></th>
                        <?php } ?>
                        <?php if ($val == '16' or $val == '16'){ ?>
                            <th scope="col"><?= $c16 ?></th>
                        <?php } ?>
                        <?php if ($val == '18' or $val == '18'){ ?>
                            <th scope="col"><?= $c18 ?></th>
                        <?php } ?>
                        <?php if ($val == '20' or $val == '20'){ ?>
                            <th scope="col"><?= $c20 ?></th>
                        <?php } ?>
                        <?php if ($val == '22' or $val == '22'){ ?>
                            <th scope="col"><?= $c22 ?></th>
                        <?php } ?>
                        <?php if ($val == '28' or $val == '28'){ ?>
                            <th scope="col"><?= $c28 ?></th>
                        <?php } ?>
                        <?php if ($val == '30' or $val == '30'){ ?>
                            <th scope="col"><?= $c30 ?></th>
                        <?php } ?>
                        <?php if ($val == '32' or $val == '32'){ ?>
                            <th scope="col"><?= $c32 ?></th>
                        <?php } ?>
                        <?php if ($val == '34' or $val == '34'){ ?>
                            <th scope="col"><?= $c34 ?></th>
                        <?php } ?>
                        <?php if ($val == '36' or $val == '36'){ ?>
                            <th scope="col"><?= $c36 ?></th>
                        <?php } ?>
                        <?php if ($val == '38' or $val == '38'){ ?>
                            <th scope="col"><?= $c38 ?></th>
                        <?php } ?>
                        <?php if ($val == '42' or $val == '42'){ ?>
                            <th scope="col"><?= $c42 ?></th>
                        <?php } 
                        ?>    
                <?php endforeach; 
                 } else {  ?>
                    <th scope="col"></th>
                <?php } 
                ?>                    
                    <th scope="col"></th>
                    <th scope="col"><?= $tunidades ?>  </th>                
                    <td></td>
                </tr>  
                
        </table>        
    </div>
    <div class="panel-footer text-right">
    <?php if($model->cerrar_remision == 0){?>    
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nuevo', ['remision/nuevodetalle', 'id' => $model->id_remision, 'idordenproduccion' => $idordenproduccion], ['class' => 'btn btn-success btn-sm']); ?>        
        <?php if ($datostallas) { ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar y Nuevo", ["class" => "btn btn-success btn-sm", 'name' => 'actualizarynuevo']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-success btn-sm", 'name' => 'actualizar']) ?>
        <?php } 
    } ?>
        
    </div>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    function esInteger(e) {
        var charCode
        charCode = e.keyCode
        status = charCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false
        }
        return true
    }
</script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>