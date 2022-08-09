<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;


$this->title = 'Mecanicos';
$this->params['breadcrumbs'][] = $this->title;


?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtrocliente");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista Clientes</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("mecanico/index"),
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
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtrocliente" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "documento")->input("search") ?>
            <?= $formulario->field($form, "nombre_completo")->input("search") ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("mecanico/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
            <tr>                
                <th scope="col" style='background-color:#B9D5CE;'>Tipo documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Nombres</th>
                <th scope="col" style='background-color:#B9D5CE;'>Dirección</th>
                <th scope="col"style='background-color:#B9D5CE;'>Teléfono</th>
                <th scope="col" style='background-color:#B9D5CE;'>Email</th>
                <th scope="col" style='background-color:#B9D5CE;'>Departamento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Municipio</th>
                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>                               
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
                <tr style="font-size: 85%;">   
                <td><?= $val->tipoDocument->descripcion ?></td>    
                <td><?= $val->documento ?></td>
                <td><?= $val->nombre_completo ?></td>
                <td><?= $val->direccion_mecanico ?></td>
                <td><?= $val->celular ?></td>
                <td><?= $val->email_mecanico ?></td>
                <td><?= $val->departamentoMecanico->departamento ?></td>
                 <td><?= $val->municipio->municipio?></td>
                <td><?= $val->usuario ?></td> 
                <td style="width: 25px;">				
                  <a href="<?= Url::toRoute(["mecanico/view", "id" => $val->id_mecanico]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                </td>
                <td style="width: 25px;">
                  <a href="<?= Url::toRoute(["mecanico/update", "id" => $val->id_mecanico])?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
                <td style="width: 25px;">
		     <?= Html::a('', ['eliminar', 'id' => $val->id_mecanico], [
                        'class' => 'glyphicon glyphicon-trash',
                        'data' => [
                            'confirm' => 'Esta seguro de eliminar el registro?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </td>
            </tr>
            </tbody>
            <?php endforeach; ?>
        </table>
        <div class="panel-footer text-right" >
            <a align="right" href="<?= Url::toRoute("mecanico/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>







