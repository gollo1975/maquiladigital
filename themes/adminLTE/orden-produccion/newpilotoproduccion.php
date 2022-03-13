<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Ordenproduccion;
use app\models\Ordenproducciondetalle;
use app\models\TiposMaquinas;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

$this->title = 'Operaciones';
$this->params['breadcrumbs'][] = ['label' => 'Listado de operaciones', 'url' => ['view_detalle', 'id' => $id ]];
$this->params['breadcrumbs'][] = $iddetalle;
$idToken = 0;
?>
    <?php $model = Ordenproduccion::findOne($id); ?>
    <?php $modeldetalle = Ordenproducciondetalle::findOne($iddetalle); ?>
    
    <div class="modal-body">
        <p>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view_detalle', 'id' => $id ], ['class' => 'btn btn-primary btn-sm']) ?>
        </p>
        <div class="panel panel-success">
            <div class="panel-heading">
                Operaciones
            </div>
            <div class="panel-body">
                <table class="table table-responsive">
                    <tr style="font-size: 85%;">
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idordenproduccion') ?></th>
                        <td><?= Html::encode($model->idordenproduccion) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipo') ?></th>
                        <td><?= Html::encode($model->tipo->tipo) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'ordenproduccion') ?></th>
                        <td><?= Html::encode($model->ordenproduccion) ?></td>
                    </tr>
                    <tr style="font-size: 85%;">
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Producto') ?></th>
                        <td><?= Html::encode($modeldetalle->productodetalle->prendatipo->prenda.' / '.$modeldetalle->productodetalle->prendatipo->talla->talla) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo') ?></th>
                        <td><?= Html::encode($modeldetalle->codigoproducto) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cantidad') ?></th>
                        <td><?= Html::encode($modeldetalle->cantidad) ?></td>
                    </tr>
                </table>
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
        <div class="panel-footer text-right">
            <?php 
            if(count($detalle_piloto)== 0){?>
              <?= Html::a('<span class="glyphicon glyphicon-save"></span> Importar', ['orden-produccion/importarmedidapiloto', 'iddetalle' => $iddetalle, 'id' => $model->idordenproduccion], ['class' => 'btn btn-success btn-sm']); ?>      
            <?php }else{
                $estado =0;
                foreach ($detalle_piloto as $dato):
                      $estado = $dato->aplicado;
                endforeach;
                if($estado == 0){?>
                     <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Actualizar", ["class" => "btn btn-info btn-sm", 'name' => 'actualizarLinea']) ?>
                <?php }else{ ?>
                  <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nueva-Linea', ['orden-produccion/nuevalineamedida', 'iddetalle' => $iddetalle, 'id' => $model->idordenproduccion], ['class' => 'btn btn-primary btn-sm']); ?> 
                 <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Actualizar", ["class" => "btn btn-info btn-sm", 'name' => 'actualizarLinea']) ?>
                <?php }
           } ?> 
        </div>
        <div class="table table-responsive">
            <div class="panel panel-success ">
                <div class="panel-heading">
                    
                </div>
                <div class="panel-body">
                 <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr style="font-size: 85%;">
                            
                            <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                             <th scope="col" style='background-color:#B9D5CE;'>Op</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Concepto</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Medidas Ficha</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Medidas Confección</th>
                              <th scope="col" style='background-color:#B9D5CE;'>Aplicado</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Tolerancia</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                            <th scope="col" style='background-color:#B9D5CE;'></th>
                            <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($detalle_piloto as $val): ?>
                        <tr style="font-size: 85%;">
                            <td><?= $val->id_proceso ?></td>
                             <td><?= $val->idordenproduccion ?></td>
                            <td><?= ($modeldetalle->productodetalle->prendatipo->prenda.' / '.$modeldetalle->productodetalle->prendatipo->talla->talla) ?></td>
                            <td ><input type="text" size="43"  name="concepto[]" value="<?= $val->concepto ?>"  maxlength="40"></td>
                            <td ><input type="text" size="7" name="medidafichatecnica[]" value="<?= $val->medida_ficha_tecnica ?>" maxlength="6"></td>
                            <td ><input type="text" size="7" name="medidaconfeccion[]" value="<?= $val->medida_confeccion ?>"  maxlength="6"></td>
                             <td><?= $val->aplicadoproceso ?></td>
                            <?php if($val->tolerancia < 0){?>
                                  <td style="background-color:#B2F3EE; color: #F51F15;"><?= $val->tolerancia ?></td>
                                  <td style="color: #F51F15;"><?= $val->observacion ?></td>
                            <?php }else{ ?>
                                  <td style="background-color:#DAF7A6; color: #111213;"><?= $val->tolerancia ?></td>
                                   <td style="color: #117A65;"><?= $val->observacion ?></td>
                            <?php } ?>
                           
                            <td style= 'width: 25px;'>
                            <?= Html::a('', ['eliminardetallepiloto', 'id_proceso' => $val->id_proceso,'iddetalle'=>$val->iddetalleorden, 'id'=> $model->idordenproduccion], [
                                'class' => 'glyphicon glyphicon-trash',
                                'data' => [
                                    'confirm' => 'Esta seguro de eliminar el registro?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                          </td>
                            <input type="hidden" name="listado_piloto[]" value="<?= $val->id_proceso ?>">
                            <td style="width: 25px;"><input type="checkbox" name="id_proceso[]" value="<?= $val->id_proceso ?>"></td>         
                        </tr>
                        </tbody>
                        <?php endforeach; ?>
                    </table>
                    <div class="panel-footer text-right">
                        <?= Html::a('<span class="glyphicon glyphicon-export"></span> Excel', ['generarexcel', 'id' => $id], ['class' => 'btn btn-primary btn-sm ']); ?>
                         <?= Html::submitButton("<span class='glyphicon glyphicon-ok'></span> Aplicar", ["class" => "btn btn-warning btn-sm", 'name' => 'aplicarregistro']) ?>
                    </div>
                </div>
           </div>
        </div>
        
    </div>
<?php ActiveForm::end(); ?>
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