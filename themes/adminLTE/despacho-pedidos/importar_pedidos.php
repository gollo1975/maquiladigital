<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title = 'LISTADO DE PEDIDOS';
$this->params['breadcrumbs'][] = ['label' => 'despacho-pedidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Buscar';
$conCliente = ArrayHelper::map(app\models\Cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto'); 


?>
    <div class="modal-body">
        <p>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
        </p>
        
        <?php $formulario = ActiveForm::begin([
            "method" => "get",
            "action" => Url::toRoute(["despacho-pedidos/importar_pedidos",'token' => $token]),
            "enableClientValidation" => true,
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                            'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                            'labelOptions' => ['class' => 'col-sm-2 control-label'],
                            'options' => []
                        ],

        ]);
        ?>

        <div class="panel panel-success panel-filters">
            <div class="panel-heading">
                Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
            </div>

            <div class="panel-body" id="filtrocliente">
                <div class="row" >
                    <?= $formulario->field($form, "numero")->input("search") ?>
                    <?= $formulario->field($form, 'cliente')->widget(Select2::classname(), [
                        'data' => $conCliente,
                        'options' => ['placeholder' => 'Seleccione el empleado'],
                        'pluginOptions' => [
                            'allowClear' => true ]]);
                    ?>
                </div>
                <div class="panel-footer text-right">
                    <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
                     <a align="right" href="<?= Url::toRoute(["despacho-pedidos/importar_pedidos", 'token' => $token]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
                </div>
            </div>
        </div>

        <?php $formulario->end() ?>
        
        
        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
        ]); ?>
        <div class="table table-responsive">
            <div class="panel panel-success ">
                <div class="panel-heading">
                    Productos <span class="badge"><?= $pagination->totalCount ?></span>
                </div>
                <div class="panel-body">
                     <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th scope="col" style='background-color:#B9D5CE;'>No pedido</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                             <th scope="col" style='background-color:#B9D5CE;'>Fecha entrega</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Cantidades</th>
                            <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($operacion as $val):  ?>
                        <tr style="font-size: 85%;">
                            <td><?= $val->numero_pedido ?></td>
                            <td><?= $val->cliente->nombrecorto ?></td>
                            <td><?= $val->fecha_entrega ?></td>
                            <td style="text-align: right"><?= $val->total_unidades?></td>
                            <td style= 'width: 25px; height: 25px;'><input type="checkbox" name="listado_pedidos[]" value="<?= $val->id_pedido ?>"></td> 
                        </tr>
                        </tbody>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div class="panel-footer text-right">
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar datos", ["class" => "btn btn-success btn-sm", 'name' => 'enviar_datos']) ?>
                </div>

            </div>
            <?= LinkPager::widget(['pagination' => $pagination]) ?>
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
