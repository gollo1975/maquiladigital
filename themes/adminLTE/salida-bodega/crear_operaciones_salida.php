<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Ordenproduccion;
use app\models\TiposMaquinas;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

$this->title = 'Listado de operaciones';
$this->params['breadcrumbs'][] = ['label' => 'Listado de operaciones', 'url' => ['view', 'id' => $id ]];
$this->params['breadcrumbs'][] = $id;
$maquinas = ArrayHelper::map(TiposMaquinas::find()->where(['=','estado', 1])->all(), 'id_tipo', 'descripcion');
?>
   <?php $model = Ordenproduccion::findOne($id); ?>
    <div class="modal-body">
        <p>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view', 'id' => $id, 'token' => $token], ['class' => 'btn btn-primary btn-sm']) ?>
        </p>
        
        <?php $formulario = ActiveForm::begin([
            "method" => "get",
            "action" => Url::toRoute(["salida-bodega/cargar_operaciones", 'id' => $id, 'token' => $token]),
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
                    <?= $formulario->field($form, "q")->input("search") ?>
                </div>
                <div class="panel-footer text-right">
                    <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
                    <a align="right" href="<?= Url::toRoute(["salida-bodega/cargar_operaciones", 'id' => $id, 'token' => $token]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                    Operaciones <span class="badge"><?= $pagination->totalCount ?></span>
                </div>
                <div class="panel-body">
                     <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Proceso</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Estandar</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Segundos</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Minutos</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Maquinas</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($operacion as $val): ?>
                        <tr style="font-size: 85%;">
                            <td><?= $val->idproceso ?></td>
                            <td><?= $val->proceso ?></td>
                            <?php if($val->segundos == 0){?> 
                              <td style="background:#ADB9D1; font-weight:bold;"><?= $val->segundos ?></td>
                            <?php }else{?>
                                <td style="background:#ADD1D1; font-weight:bold;"><?= $val->segundos ?></td>
                            <?php }?>
                            <td><input type="text" name="segundos[]" style="text-align: right;"  value="<?= $val->segundos ?>" required></td>
                            <td><input type="text"  name="minutos[]" style="text-align: right;" value="<?= number_format($val->minutos,2) ?>" required></td>
                            <td><?= Html::dropDownList('id_tipo[]', $val->estado, $maquinas, ['class' => 'col-sm-12', 'prompt' => 'Seleccion la maquina']) ?></td>
                            <input type="hidden" name="idproceso[]" value="<?= $val->idproceso ?>">
                            
                        </tr>
                        </tbody>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div class="panel-footer text-right">
                    <?= Html::submitButton("<span class='glyphicon glyphicon-send'></span> Enviar", ["class" => "btn btn-success btn-sm", 'name' => 'guardar_operacion']) ?>
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
