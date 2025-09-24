<?php
// Incluye las clases necesarias
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
?>
<?php

$form = ActiveForm::begin([
    "method" => "post",
    'id' => 'formulario',
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'action' => Url::to(['costos-gastos-empresa/generar_seleccion_empleados', 'id' => Yii::$app->request->get('id')]),
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-8 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"></h4>
</div>
<div class="modal-body">
    <div class="table table-responsive">
        <div class="panel panel-success ">
            <div class="panel-heading" style='text-align: center'>
                Listado de empleado.
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr style='font-size:85%;'>
                            <th scope="col" style='background-color:#B9D5CE;'>Cedula</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                            <th scope="col" style='background-color:#B9D5CE;'>Grupo de pago</th>
                            <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($modelo as $dato) { ?>
                            <tr style='font-size:85%;'>
                                <td style='text-align: left'><?= $dato->cedula_empleado ?></td>
                                <td style='text-align: left'><?= $dato->empleado->nombrecorto ?></td>
                                <td style='text-align: left'><?= $dato->grupoPago->grupo_pago ?></td>
                                <td style="width: 30px;"><input type="checkbox" name="empleados_seleccionados[]" value="<?= $dato->id_programacion ?>"></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="panel-footer text-right">
                <?= Html::button("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar seleccionados", ["class" => "btn btn-primary", 'id' => 'btn-ajax-submit']) ?>
            </div>
        </div>
    </div>
</div>

<?php $form->end() ?>

<script type="text/javascript">
    function marcar(source) {
        checkboxes = document.getElementsByTagName('input');
        for (i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type == "checkbox") {
                checkboxes[i].checked = source.checked;
            }
        }
    }

    $(document).ready(function() {
        // Lógica para enviar el formulario con AJAX
        $('#btn-ajax-submit').on('click', function() {
            var form = $('#formulario');
            var url = form.attr('action');
            var formData = form.serialize();

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Muestra una alerta de éxito
                        alert(response.message);
                        // Recarga la página principal
                        window.location.reload();
                    } else {
                        // Muestra una alerta de error o advertencia
                        alert(response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Ocurrió un error al procesar la solicitud. ' + errorThrown);
                }
            });
        });
    });
</script>