<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use kartik\select2\Select2;
//models
use app\models\Talla;
use app\models\DetalleColorTalla;

$this->title = 'Nueva combinacion';
$this->params['breadcrumbs'][] = ['label' => 'Inventario punto de venta', 'url' => ['view','id'=> $id, 'token' => $token]];
$this->params['breadcrumbs'][] = $this->title;

if($codigo == 0){
    $conTalla = ArrayHelper::map(Talla::find()->orderBy(' idtalla ASC')->all(), 'idtalla', 'tindex');
}else{
   $conTalla = DetalleColorTalla::find()->where(['=','id_inventario', $codigo])->orderBy(' idtalla ASC')->all();
   $conTalla = ArrayHelper::map($conTalla, 'idtalla', 'nombreTalla'); 
}

?>
<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view','id' => $id, 'token' =>$token, 'codigo' =>$codigo], ['class' => 'btn btn-primary btn-sm']) ?>
</p>   
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute(["inventario-punto-venta/generar_combinacion_talla_color", 'id' => $id,'token' => $token,'codigo' => $codigo]),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-3 control-label'],
                    'options' => []
                ],

]);?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading">
        Busqueda por codigo de barras
    </div>

    <div class="panel-body" id="entrada_producto">
        <div class="row" >
            <?= $formulario->field($form, 'codigo_talla')->widget(Select2::classname(),[
                'data' => $conTalla,
                'options' => ['prompt' => 'Seleccione la talla...'],
                'pluginOptions' => [
                    'allowClear' => true,
                ]

                ])?>
       </div>
    </div>    
    <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Cargar colores", ["class" => "btn btn-primary btn-sm",]) ?>
    </div>

</div>
<?php $formulario->end() ?>
<?php $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<?php if($conColores){?>
    <div class="panel panel-success">
        <div class="panel-heading">
            Colores
        </div>
        <div class="panel-body">
             <table class="table table-bordered table-hover">
                <thead>
                    <tr style='font-size:90%;'>
                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>                      
                        <th scope="col" style='background-color:#B9D5CE;'>Nombre del color</th> 
                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($codigo == 0){
                        foreach ($conColores  as $val):?>
                            <tr>
                                <td><?= $val->id?></td>
                                <td><?= $val->color?></td> 
                                <td style= 'width: 25px; height: 25px;'><input type="checkbox" name="nuevo_color[]" value="<?= $val->id ?>"></td> 
                            </tr>
                        <?php endforeach;
                    }else{
                        $auxiliar = 0;
                        foreach ($conColores  as $val):
                            if($auxiliar <> $val->id){
                               $auxiliar = $val->id; ?>
                                <tr>
                                    <td><?= $val->id?></td>
                                    <td><?= $val->color->color?></td> 
                                    <td style= 'width: 25px; height: 25px;'><input type="checkbox" name="nuevo_color[]" value="<?= $val->id ?>"></td> 
                                </tr>
                            <?php }?>    

                        <?php endforeach;
                    }?>
                                
                </tbody>
            </table>    
        </div>
    </div>
    <div class="panel-footer text-right">
       <a href="<?= Url::toRoute(['inventario-punto-venta/view', 'id' => $id, 'token' =>$token,'codigo' => $codigo]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
       <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Generar", ["class" => "btn btn-success btn-sm", 'name' => 'enviarcolores']) ?>        
    </div>
    <?php $form->end() ?>
<?php }else{?>
<?php $form->end() ?>
<?php } ?>         
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

         