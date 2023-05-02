<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Producto */

$this->title = 'Detalle Producto';
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idproducto;
$view = 'producto';
?>
<div class="producto-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->idproducto], ['class' => 'btn btn-primary btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->idproducto], ['class' => 'btn btn-success btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->idproducto], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => 'Esta seguro de eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
        <?php echo Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 9, 'codigo' => $model->idproducto,'view' => $view, 'token' => $token], ['class' => 'btn btn-default btn-sm']); ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Producto
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idproducto') ?>:</th>
                    <td><?= Html::encode($model->idproducto) ?></td>                                                            
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechaproceso') ?>:</th>
                    <td><?= Html::encode($model->fechaproceso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo') ?>:</th>
                    <td><?= Html::encode($model->codigo) ?></td>
                </tr>               
                <tr style="font-size: 85%;">                                        
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idcliente') ?>:</th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'usuariosistema') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'activo') ?>:</th>
                    <td><?= Html::encode($model->estado) ?></td>
                </tr>                
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan="5"><?= Html::encode($model->observacion) ?></td>
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
    ]); ?>
    <div class="table-responsive">
        <div class="panel panel-success ">
            <div class="panel-heading">
                Detalles
            </div>
            <div class="panel-body">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>                        
                        <th scope="col" style='background-color:#B9D5CE;'>Prenda/Talla</th>                        
                        <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($modeldetalles as $val): ?>
                        <tr style="font-size: 85%;">
                        <td><?= $val->idproductodetalle ?></td>                        
                        <td><?= $val->prendatipo->prenda.' / '.$val->prendatipo->talla->talla ?></td>                                             
                        <td><input type="checkbox" name="idproductodetalle[]" value="<?= $val->idproductodetalle ?>"></td>                        
                    </tr>
                    </tbody>
                    <?php endforeach; ?>
                </table>
            </div>            
                <div class="panel-footer text-right">
                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Nuevo', ['producto/nuevodetalles', 'idproducto' => $model->idproducto, 'token' => $token], ['class' => 'btn btn-success btn-sm']) ?>                    
                    <?= Html::submitButton("<span class='glyphicon glyphicon-trash'></span> Eliminar", ["class" => "btn btn-danger btn-sm", 'name' => 'eliminar']) ?>
                </div>            
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

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