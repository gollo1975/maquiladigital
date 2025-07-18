<?php
//modelos

//clases
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use yii\data\Pagination;
?>
<?php

$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
            'template' => '{label}<div class="col-sm-10 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
            'options' => []
        ],
        ]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"></h4>
</div>
<div class="ordenproduccion-view_trazabilidad">
    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <p style="text-align: center;">
        <?= Html::a('<span class="glyphicon glyphicon-eye-close"></span> Cerrar ventana', ['vista_trazabilidad','id' => $id], ['class' => 'btn btn-success btn-sm']) ?>
    </p>        
    <div class="table-responsive">
        <div class="panel panel-success ">
            <div class="panel-heading">
                Registros  <span class="badge"><?= $pagination->totalCount ?></span>
            </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr style="font-size: 85%">
                            <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Operacion</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Eficiencia</th>
                             <th scope="col" style='background-color:#B9D5CE;'>Fecha proceso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                         $currentOperario = null;
                        usort($model, function($a, $b) {
                            return strcmp($a->operarioProduccion->nombrecompleto, $b->operarioProduccion->nombrecompleto);
                        });

                        foreach ($model as $val) {
                            // Si el operario actual es diferente al operario de la fila anterior,
                            // entonces mostramos el nombre del nuevo operario y agregamos un salto de línea/separador.
                            if ($val->operarioProduccion->nombrecompleto !== $currentOperario) {
                                if ($currentOperario !== null) {
                                    // Agrega un salto de línea o un separador visual entre operarios
                                    echo '<tr><td colspan="1"><hr></td></tr>'; // Una línea horizontal
                                }
                                $currentOperario = $val->operarioProduccion->nombrecompleto;
                                // Opcional: Puedes agregar una fila para el nombre del operario para que sea más visible
                                echo '<tr style="font-size: 95%; text-align: center; background-color: #E6F3F1;"><td colspan="5"><strong>Operario: ' . $val->operarioProduccion->nombrecompleto . ' (' . ''. number_format($val->operarioProduccion->documento,0) . ')</strong></td></tr>';
                            }
                        ?>
                            <tr style="font-size: 85%">
                                <td><?= $val->operarioProduccion->nombrecompleto?></td>
                                <td><?= $val->operaciones->proceso?></td>
                                <td><?= $val->cantidad?></td>
                                <td><?= $val->porcentaje_cumplimiento?></td>
                                <td><?= $val->dia_pago?></td>                                              

                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
</div>
<?php $form->end() ?> 


