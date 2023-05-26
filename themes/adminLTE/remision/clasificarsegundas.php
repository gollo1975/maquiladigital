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

$this->title = 'Clasificación segundas';
$this->params['breadcrumbs'][] = ['label' => 'Clasificacion de segundas', 'url' => ['/remision/remision', 'id' => $id, 'token' => $token, 'id_remision' => $id_remision ]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="clasificacion-remision">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?php if(count($clasificar) > 0){?>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['/remision/remision', 'id' => $id, 'token' => $token, 'id_remision' => $id_remision], ['class' => 'btn btn-primary btn-sm']);?>
            <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimirsegundas', 'id_remision' => $id_remision], ['class' => 'btn btn-default btn-sm']); ?>
        <?php } else {?>
             <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['/remision/remision', 'id' => $id, 'token' => $token, 'id_remision' => $id_remision], ['class' => 'btn btn-primary btn-sm']);?>
        <?php }?>
    </p>
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
$orden = app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $id])->all();
$global = count($orden);
$remision = app\models\Remision::findOne($id_remision);
$tipo = ArrayHelper::map(app\models\TipoClasificacionSegundas::find()->all(), 'id_tipo', 'concepto');

?>
<div class="panel panel-success ">
    <div class="panel-heading">
        Detalle de la clasificación
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-striped table-responsive-xl">
            <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Concepto</th>
                     <?php
                            foreach ($detalle as $val):
                                if($val->xs > 0){?> 
                                    
                                    <th scope="col" style='background-color:#B9D5CE;'><?= 'XS' ?></th>
                                <?php }
                                if($val->s > 0){?> 
                                    <th scope="col" style='background-color:#B9D5CE;'><?= 'S' ?></th>
                                <?php }    
                                if($val->m > 0){?> 
                                    <th scope="col" style='background-color:#B9D5CE;'><?= 'M' ?></th>
                                <?php }
                                if($val->l > 0){?>
                                    <th scope="col" style='background-color:#B9D5CE;'><?= 'L' ?></th>
                                <?php }
                                if($val->xl > 0){?>
                                    <th scope="col" style='background-color:#B9D5CE;'><?= 'XL' ?></th>
                                <?php }
                                if($val->xxl > 0){?>
                                    <th scope="col" style='background-color:#B9D5CE;'><?= 'XXL' ?></th>
                                <?php }
                                 if($val->t2 > 0){?>
                                    <th scope="col" style='background-color:#B9D5CE;'><?= '2' ?></th>
                                <?php }
                                 if($val->t4 > 0){?>
                                    <th scope="col" style='background-color:#B9D5CE;'><?= '4' ?></th>
                                <?php }
                                 if($val->t6 > 0){?>
                                    <th scope="col" style='background-color:#B9D5CE;'><?= '6' ?></th>
                                <?php }
                                 if($val->t8 > 0){?>
                                    <th scope="col" style='background-color:#B9D5CE;'><?= '8' ?></th>
                                <?php }
                                if($val->t10 > 0){?>
                                    <th scope="col" style='background-color:#B9D5CE;'><?= '10' ?></th>
                                <?php }
                                 if($val->t12 > 0){?>
                                    <th scope="col" style='background-color:#B9D5CE;'><?= '12' ?></th>
                                <?php }
                                 if($val->t14 > 0){?>
                                    <th scope="col" style='background-color:#B9D5CE;'><?= '14' ?></th>
                                <?php }
                                 if($val->t16 > 0){?>
                                    <th scope="col" style='background-color:#B9D5CE;'><?= '16' ?></th>
                                <?php }
                         endforeach; ?>
                    <th scope="col" style='background-color:#B9D5CE;'>Cantidades</th>                    
                    <th scope="col" style='background-color:#B9D5CE;'></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0; $linea = 0;
                    foreach ($clasificar as $val):
                        $total += $val->unidades;
                         $linea +=1;
                        ?>
                        <tr>
                            <td style="padding-left: 1;padding-right: 1;"><?= Html::dropdownList('tipos[]', $val->id_tipo ,$tipo, ['class' => 'col-xs-12','prompt' => 'Seleccione...','required' => true]) ?>
                            <?php if ($val->txs == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="xs[]" value="<?= $val->xs ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>
                            <?php if ($val->ts == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="s[]"  value="<?= $val->s ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white; "required></td>
                            <?php } ?>
                            <?php if ($val->tm == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="m[]" value="<?= $val->m ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>
                            <?php if ($val->tl == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="l[]" value="<?= $val->l ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>
                            <?php if ($val->txl == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="xl[]" value="<?= $val->xl ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>    
                            <?php if ($val->txxl == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="xxl[]" value="<?= $val->xxl ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>    
                                <?php if ($val->t2 == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="t2[]" value="<?= $val->a2 ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>
                            <?php if ($val->t4 == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="t4[]" value="<?= $val->a4 ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>
                            <?php if ($val->t6 == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="t6[]" value="<?= $val->a6 ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>
                            <?php if ($val->t8 == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="t8[]" value="<?= $val->a8 ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>
                            <?php if ($val->t10 == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="t10[]" value="<?= $val->a10 ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>    
                            <?php if ($val->t12 == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="t12[]" value="<?= $val->a12 ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>    
                            <?php if ($val->t14 == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="t14[]" value="<?= $val->a14 ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>    
                            <?php if ($val->t16 == 1) { ?>
                                <td style="padding-left: 1;padding-right: 1;"><input type="text" name="t16[]" value="<?= $val->a16 ?>" size="1" onkeypress="return esInteger(event)"  style="background-color:white" required></td>
                            <?php } ?>     
                            <td style="padding-left: 1;padding-right: 1; text-align: center"><?= $val->unidades ?></td>    
                              <input type="hidden" name="clasificacion[]" value="<?= $val->id_clasificacion ?>">
                                </div>
                            <?php if ($remision->cerrar_remision == 0){?>  
                                <td style="width: 25px; height: 25px;">
                                    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['eliminarsegundas', 'id_detalle' => $val->id_clasificacion, 'id' => $id, 'id_remision' => $id_remision, 'token' => $token], [
                                             'class' => '',
                                             'data' => [
                                                 'confirm' => 'Esta seguro de eliminar el registro?',
                                                 'method' => 'post',
                                             ],
                                         ])
                                     ?>
                                </td>    
                            <?php }else{?>    
                                <td style="width: 25px; height: 25px;">
                            <?php } ?>    
                        </tr>                       
                    <?php endforeach;?>
            </body>    
          <tr>
              <?php if($global <= 5){?>
                    <td colspan="5"></td>
                    <td align="center"><b>TOTAL</b></td>
                    <td align="center" ><b><?= ' '.number_format($total,0); ?></b></td>
                    <td colspan="3"></td>
              <?php } else {?>
                    <td colspan="7"></td>
                    <td align="center"><b>TOTAL</b></td>
                    <td align="center" ><b><?= ' '.number_format($total,0); ?></b></td>
                    <td colspan="3"></td>
              <?php } ?>    
            </tr>
            
        </table>        
    </div>
    <?php if ($remision->cerrar_remision == 0){?>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nuevo', ['remision/nuevalineaclasificacion', 'id' => $id, 'id_remision' => $id_remision, 'token' => $token], ['class' => 'btn btn-primary btn-sm']); ?>        
            <?php if($linea > 0){?>
               <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-success btn-sm", 'name' => 'actualizarsegundas']) ?>
            <?php }?>
        </div> 
    <?php }?>
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