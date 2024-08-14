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

//models
use app\models\TipoProducto;
$this->title = 'Referencia/Producto';
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
    "action" => Url::toRoute("referencia-producto/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$tipoPrenda = ArrayHelper::map(TipoProducto::find()->orderBy('concepto ASC')->all(), 'id_tipo_producto', 'concepto');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
            <?= $formulario->field($form, "codigo")->input("search") ?>
            <?= $formulario->field($form, "referencia")->input("search") ?>
            <?= $formulario->field($form, 'tipo_prenda')->widget(Select2::classname(), [
                'data' => $tipoPrenda,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("referencia-producto/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros: <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
            <tr style='font-size:85%;'align="center" >     
                <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                <th scope="col" style='background-color:#B9D5CE;'>Grupo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Costo producto</th>
                <th scope="col" style='background-color:#B9D5CE;'></th> 
                <th scope="col" style='background-color:#B9D5CE;'></th> 
                  <th scope="col" style='background-color:#B9D5CE;'></th> 
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
                <tr style='font-size:84%;'>   
                    <td>R-<?= $val->codigo ?></td>
                    <td><?= $val->descripcion_referencia ?></td>
                    <td><?= $val->tipoProducto->concepto ?></td>
                    <td align="right"><?= number_format($val->costo_producto,0) ?></td>
                    <!-- Inicio Nuevo Detalle proceso -->
                    <td style= 'width: 25px;'>	
                        <?= Html::a('<span class="glyphicon glyphicon-list"></span>',
                            ['/referencia-producto/nuevo_costo_producto','id' => $val->codigo],
                            [
                                'title' => 'Crear nuevo precio de costo',
                                'data-toggle'=>'modal',
                                'data-target'=>'#modalnuevocostoproducto'.$val->codigo,
                               
                            ])    
                       ?>
                      <div class="modal remote fade" id="modalnuevocostoproducto<?= $val->codigo ?>">
                          <div class="modal-dialog modal-lg" style ="width: 500px;">
                               <div class="modal-content"></div>
                          </div>
                      </div> 
                    </td>    
                    <td style= 'width: 25px;'>				
                       <a href="<?= Url::toRoute(["referencia-producto/view", "id" => $val->codigo]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                
                    </td>
                   
                        <td style= 'width: 25px;'>				
                           <a href="<?= Url::toRoute(["referencia-producto/update", "id" => $val->codigo]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                
                        </td>
                   
                </tr>
            </tbody>
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >            
            <?php
                $form = ActiveForm::begin([
                            "method" => "post",                            
                        ]);
                ?>    
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>
                <a align="right" href="<?= Url::toRoute("referencia-producto/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>







