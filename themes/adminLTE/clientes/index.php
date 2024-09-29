<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;


$this->title = 'Clientes';
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
    "action" => Url::toRoute("clientes/index"),
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
            <?= $formulario->field($form, "cedulanit")->input("search") ?>
            <?= $formulario->field($form, "nombrecorto")->input("search") ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("clientes/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros: <?= $pagination->totalCount ?>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
            <tr>                
                <th scope="col" style='background-color:#B9D5CE;'>Cedula/Nit</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col"style='background-color:#B9D5CE;'>Teléfono</th>
                <th scope="col"style='background-color:#B9D5CE;'>Celular</th>
                <th scope="col" style='background-color:#B9D5CE;'>Dirección</th>
                <th scope="col" style='background-color:#B9D5CE;'>Municipio</th>
                <th scope="col" style='background-color:#B9D5CE;'>Email</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>                               
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
                <tr style="font-size: 85%;">                
                <td><?= $val->cedulanit ?></td>
                <td><?= $val->nombrecorto ?></td>
                <td><?= $val->telefonocliente ?></td>
                <td><?= $val->celularcliente ?></td>
                <td><?= $val->direccioncliente ?></td>
                <td><?= $val->municipio->municipio ?></td>
                 <td><?= $val->emailcliente ?></td>
                <td style="width: 25px;">				
                  <a href="<?= Url::toRoute(["clientes/view", "id" => $val->idcliente, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                </td>
                <td style="width: 25px;">
                  <a href="<?= Url::toRoute(["clientes/editar", "id" => $val->idcliente])?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
                <?php if($val->proceso == 1){?>
                    <td style="width: 25px;">
                        <!-- Inicio Nuevo Detalle proceso -->
                          <?= Html::a('<span class="glyphicon glyphicon-plus"></span> ',
                              ['/clientes/asignar_productos','id' => $val->idcliente],
                              [
                                  'title' => 'Permite configurar los productos que se maquilan o confeccionan',
                                  'data-toggle'=>'modal',
                                  'data-target'=>'#modalasignarproductos',
                                  'class' => ''
                              ])    
                              ?>
                       <div class="modal remote fade" id="modalasignarproductos">
                              <div class="modal-dialog modal-lg" style ="width: 600px;">
                                   <div class="modal-content"></div>
                               </div>
                       </div>
                    </td>
                <?php }else{?>
                    <td style="width: 25px;">  </td>
                <?php }?>    
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
            <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>
            <a align="right" href="<?= Url::toRoute("clientes/nuevo") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
              <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>







