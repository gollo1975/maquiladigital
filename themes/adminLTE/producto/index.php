 <?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use app\models\Cliente;

//clases
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
//Modelos...


$this->title = 'Productos';
$this->params['breadcrumbs'][] = $this->title;

?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtro");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("producto/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$cliente = ArrayHelper::map(Cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
                <?= $formulario->field($form, 'idcliente')->widget(Select2::classname(), [
                   'data' => $cliente,
                   'options' => ['prompt' => 'Seleccione...'],
                   'pluginOptions' => [
                       'allowClear' => true
                   ],
               ]); ?>
             <?= $formulario->field($form, "referencia")->input("search") ?>
        
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("producto/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
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
          Registros <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr >         
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Fecha/Hora</th>
                <th scope="col" style='background-color:#B9D5CE;'>Observación</th>
                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th score="col" style='background-color:#B9D5CE;'></th>                              
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val):?>
            <tr style ='font-size: 85%;'>                
                <td><?= $val->idproducto ?></td>
                <td><?= $val->codigo ?></td>
                <td><?= $val->cliente->nombrecorto ?></td>
                <td><?= $val->fechaproceso ?></td>
                <td><?= $val->observacion ?></td>
                <td><?= $val->usuariosistema ?></td>
                <td style= 'width: 25px; height: 25px;'>
                  <a href="<?= Url::toRoute(["producto/view", "id" => $val->idproducto,'token' => $token]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                </td>
                <td style= 'width: 25px; height: 25px;'>
                    <a href="<?= Url::toRoute(["producto/update", "id" => $val->idproducto]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                </td>
                <td style= 'width: 25px; height: 25px;'>
                    <?= Html::a('', ['delete', 'id' => $val->idproducto], [
                        'class' => 'glyphicon glyphicon-trash',
                        'data' => [
                            'confirm' => 'Esta seguro de eliminar el registro?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </td>
                  
            </tr>            
            <?php endforeach; ?>
            </tbody>    
        </table> 
        <div class="panel-footer text-right" >            
          <a align="right" href="<?= Url::toRoute("producto/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
           <?php $form->end() ?>
        </div>
     </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

