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

$this->title = 'Listado de tallas';
$this->params['breadcrumbs'][] = ['label' => 'Inventario punto de venta', 'url' => ['view','id'=> $id, 'token' => $token]];
$this->params['breadcrumbs'][] = $this->title;
$conTalla = ArrayHelper::map(Talla::find()->orderBy(' idtalla ASC')->all(), 'idtalla', 'tindex');
?>
<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view','id' => $id, 'token' =>$token, 'codigo' =>$codigo], ['class' => 'btn btn-primary btn-sm']) ?>
</p>   

<?php $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<?php if($tallas){?>
    <div class="panel panel-success">
        <div class="panel-heading">
            Listado de colores
        </div>
        <div class="panel-body">
             <table class="table table-bordered table-hover">
                <thead>
                    <tr style='font-size:90%;'>
                        <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>                      
                        <th scope="col" style='background-color:#B9D5CE;'>Nombre de la talla</th> 
                        <th scope="col" style='background-color:#B9D5CE;'>Genero</th> 
                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($codigo == 0){
                        foreach ($tallas  as $val):?>
                            <tr style='font-size:85%;'> 
                                <td><?= $val->idtalla?></td>
                                <td><?= $val->talla?></td> 
                                 <td><?= $val->sexo?></td> 
                                <td style= 'width: 25px; height: 25px;'><input type="checkbox" name="nuevo_talla[]" value="<?= $val->idtalla ?>"></td> 
                            </tr>
                        <?php endforeach;
                    }else{
                        $auxiliar = 0;
                        foreach ($tallas  as $val):
                            if($auxiliar <> $val->idtalla){
                               $auxiliar = $val->idtalla; ?>
                                <tr>
                                    <td><?= $val->idtalla?></td>
                                    <td><?= $val->talla?></td> 
                                    <td><?= $val->sexo?></td> 
                                    <td style= 'width: 25px; height: 25px;'><input type="checkbox" name="nuevo_talla[]" value="<?= $val->idtalla ?>"></td> 
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
       <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Adicionar talla", ["class" => "btn btn-success btn-sm", 'name' => 'adicionar_talla']) ?>        
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

         