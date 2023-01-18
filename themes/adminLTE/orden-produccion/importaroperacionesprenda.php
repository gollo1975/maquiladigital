<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;


$this->title = 'Importar operaciones';
$this->params['breadcrumbs'][] = $this->title;

?>
<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute(["orden-produccion/importaroperacionesprenda", 'id' => $id, 'iddetalleorden' => $iddetalleorden]),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-3 control-label'],
                    'options' => []
                ],

]);
?>
<p>
     <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view_detalle', 'id' => $id ], ['class' => 'btn btn-primary btn-sm']) ?>
   </p>
<div class="panel panel-success panel-filters">
    <div class="panel-heading">
        Parametros de entrada
    </div>
	
    <div class="panel-body" id="importaroperacionesprenda">
        <div  class="row">
          <?= $formulario->field($form, "orden_produccion")->input("search") ?>  
        </div>  
         
     </div>    
    <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar operaciones", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute(["orden-produccion/importaroperacionesprenda", 'id' => $id, 'iddetalleorden' => $iddetalleorden]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Limpiar</a>
    </div>
    
</div>
<?php $formulario->end() ?>
<?php
    $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        <?php if($model <> 0){?>
            Registros <span class="badge"> <?= count($model)?></span>
        <?php }?>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>                
                    <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Nombre de operacion</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Segundos</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Tipo de maquina</th>
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                    
                </tr>
            </thead>
            <tbody>
                <?php 
                if($model <> 0){
                    foreach ($model as $val):?>
                            <tr style='font-size:85%;'>             
                                <td><?= $val->idproceso ?></td>    
                                <td><?= $val->proceso ?></td>
                                <td style="text-align: right"><?= ''.number_format($val->total,0)?></td>
                                <td><?= $val->tipomaquina->descripcion ?></td>
                                <td style= 'width: 25px; height: 25px;'><input type="checkbox" name="operaciones[]" value="<?= $val->idproceso ?>"></td> 
                                <input type="hidden" name="id_detalle[]" value="<?= $val->iddetalleproceso ?>">

                            </tr>  
                    <?php endforeach;
                }?>
            </tbody> 
        </table>   
       <?php  if($model <> 0){?>
            <div class="panel-footer text-right" >  
                <?= Html::submitButton("<span class='glyphicon glyphicon-import'></span> Importar operaciones", ["class" => "btn btn-info btn-sm", 'name' => 'importaroperaciones']) ?>
                                

            </div>
       <?php }?> 
    </div>
</div>
<?php $formulario->end() ?>

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



