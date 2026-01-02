<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use kartik\select2\Select2;
use app\models\PedidoTallas;
use app\models\PedidoColores;
//models
$this->title = 'PACKING (Referencia: '.$model->inventario->codigo_producto.'-' .$model->inventario->nombre_producto.')';
$this->params['breadcrumbs'][] = ['label' => 'Listar packing', 'url' => ['view_lista', 'id' => $id, 'token' => $token]];
$this->params['breadcrumbs'][] = $id;

//
$cantidad_caja = \app\models\PackingPedidoDetalle::find()
    ->where(['=', 'id_packing', $packin->id_packing])
    ->orderBy([
        'cerrar_linea' => SORT_ASC, // Primer campo de ordenación
        'id_detalle' => SORT_ASC    // Segundo campo de ordenación (se usa como desempate)
    ])
    ->all();
//$cantidad_caja = \app\models\PackingPedidoDetalle::find()->where(['=','id_packing', $packin->id_packing])->orderBy(['cerrar_linea, id_detalle' => SORT_ASC])->all();

$tallas = ArrayHelper::map(PedidoTallas::find()->where(['id_detalle' => $id_detalle])->orderBy('idtalla ASC')->all(), 'idtalla', 'nombreTalla');
$colores = ArrayHelper::map(PedidoColores::find()->where(['id_detalle' => $id_detalle])->orderBy('id ASC')->all(), 'id', 'nombreColor');

if(!$cantidad_caja) {
    Yii::$app->getSession()->setFlash('warning','Favor crear la cantidad de cajas para el PÀCKING de este despacho. ');
}
?>
<div class="btn-group btn-sm" role="group">    
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view_vista', 'id' => $id, 'token' => $token], ['class' => 'btn btn-primary btn-sm']) ?>
 </div>  
<?php $form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-2 control-label'],
                'options' => []
            ],
        ]);
?>
<div class="panel panel-success">
    <div class="panel-heading">
        CANTIDADES A DESPACHAR
    </div>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'cantidad_despachada')->textInput(['disabled' => true]) ?>
        </div>
        <div class="row">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr style="font-size: 85%;">
                        <th scope="col" style='background-color:#B9D5CE;'>Nro de caja</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Tallas</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Colores</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad despachada</th>
                         <th scope="col" style='background-color:#B9D5CE;'>Cerrado</th>
                        <th scope="col" style='background-color:#B9D5CE'></th>
                     
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_unidades = 0;
                    foreach ($cantidad_caja as $caja) {
                        $total_unidades += $caja->cantidad_despachada;
                        ?>
                        <tr style="text-align: center; font-size: 90%;">
                            <td><?= $caja->numero_caja?></td>
                            <?php if ($caja->cerrar_linea == 0){?>
                             
                                <td>
                                    <?= Html::dropDownList('talla[]',
                                        $caja->idtalla,
                                        $tallas,
                                        ['class' => 'col-sm-8', 'prompt' => 'Seleccione la talla']) ?>
                                </td>
                            
                                <td>
                                    <?= Html::dropDownList('color[]',
                                        $caja->id  ,
                                        $colores,
                                        ['class' => 'col-sm-8', 'prompt' => 'Seleccione el color']) ?>
                                </td>
                            <?php }else{?>
                                <td><?= $caja->inventario->nombre_producto?></td>
                                <td><?= $caja->talla->talla?></td>
                                <?php if($caja->id != null){?>
                                    <td><?= $caja->colores->color?></td>
                                <?php }else{?>
                                    <td> <?= 'NOT FOUND' ?></td>
                                <?php }    
                             }?>    
                                <td style="text-align: right; padding-right: 1px;">
                                <input type="text" name="cantidad[]" value="<?= $caja->cantidad_despachada ?>" size="9" style="text-align: right;">
                            </td> 
                            <?php if($caja->cerrar_linea == 0){?>
                                <td>
                                    <a href="<?= Url::toRoute(['despacho-pedidos/cerrar_linea', 'id' => $id, 'id_detalle'=> $id_detalle,'codigo'=> $codigo, 'id_inventario'=> $id_inventario,'token' => $token,'id_caja' => $caja->id_detalle])?>"
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('¿Estás seguro de que deseas cerrar esta linea de despacho? ¡Esta acción no se puede deshacer!');">
                                             <?= $caja->cerrarLinea?></a>
                                </td> 
                            <?php }else{?>
                                <td>
                                    <a href=""
                                            class="btn btn-info btn-sm"
                                            onclick="return ('¿Estás seguro de que deseas cerrar esta linea de despacho? ¡Esta acción no se puede deshacer!');">
                                             <?= $caja->cerrarLinea?></a>
                                </td>     
                            <?php }    
                             if($caja->cantidad_despachada != 0 && $packin->cerrado_packing == 0){?>
                                    
                                    <td style="width: 25px; height: 25px;">
                                        <?= Html::a('<span class="glyphicon glyphicon-plus-sign"></span>', ['duplicar_caja_packing', 'id' => $id, 'id_detalle'=> $id_detalle,'codigo'=> $codigo, 'id_inventario'=> $id_inventario,'token' => $token,'id_caja' => $caja->id_detalle],['class' => 'btn btn-primary btn-xs',
                                          'data' => ['confirm' => 'Esta seguro de duplicar esta caja para seguir con el packing.', 'method' => 'post']]);?>
                                    </td>  
                            <?php }else{ ?>
                                    <td style="width: 25px; height: 25px;"></td>
                            <?php } ?>         
                           <input type="hidden" name="listado_unidades[]" value="<?= $caja->id_detalle ?>">
                        </tr>
                    <?php }?>
                </tbody>    
            </table>  
            <table class="table table-bordered table-hover" style="margin-left: auto; margin-right: auto;">
            <tr>
              
                  

                   <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                   </td>
                   <td colspan="4" style="font-size: 95%; background: #277da1; color: #FFFFFF; text-align: center;">
                       <b>Total unidades despachadas: <?= ''.number_format($total_unidades, 0) ?></b> 
                   </td>
                   <td colspan="4" style="font-size: 90%; background: #277da1; color: #FFFFFF; text-align: center;">
                      
                   </td>

               </tr>    
            </table> 
        </div> 
        <div class="panel-footer text-right">  
            <?php 
            if($packin->cerrado_packing == 0){?>
                <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-warning btn-sm", 'name' => "actualizar_unidades"]) ?>
                

            <?php }?>
        </div>   
    </div>
</div>    
        
  
</div>
<?php $form->end() ?>

<script type="text/javascript">
	function marcar(source) 
	{
		checkboxes=document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
		for(i=0;i<checkboxes.length;i++) //recoremos todos los controles
		{
			if(checkboxes[i].type == "checkbox") //solo si es un checkbox entramos
			{
				checkboxes[i].checked=source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
			}
		}
	}
</script>