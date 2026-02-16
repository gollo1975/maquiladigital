<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Importar archivo CVS';
?>

<div class="panel panel-success">
    <div class="panel-heading">
        <h3 class="panel-title">Información del archivo...</h3>
    </div>
    
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'layout' => 'horizontal', // Usar el layout de bootstrap directamente
    ]); ?>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'fileImport')->fileInput([
                    'accept' => '.csv',
                ])->label('Seleccione el archivo con extensión CVS: ') ?>
            </div>
        </div>
    </div>

    <div class="panel-footer text-right">
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar',['ingreso-personal-contrato/view', 'id' => $id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio], 
            ['class' => 'btn btn-primary btn-sm']); 
        ?>
        <?= Html::submitButton("<span class='glyphicon glyphicon-upload'></span> Subir Archivo", 
            ["class" => "btn btn-success btn-sm"]) 
        ?>
    </div>
    <div class="panel panel-success">
            <div class="panel-heading">
                Información del archivo y su configuracion para exportacion.
            </div>
        <div class="panel-body">
           
            <li>
               Solo se importan archivos de excel con extensión (CVS delimitado por coma).
            </li>
            <li>
               Los campos que se necesitan son (DOCUMENTO, EMPLEADO, NOMBRE DE LA OPERACION, CANTIDAD, VALOR UNITARIO , TOTAL DIAS). 
            </li>
            <li>
               El campo VALOR UNITARIO, CANTIDAD Y TOTAL DIAS  No puede llevar separadores de mil. (ejemplo 1000). 
            </li>
            <li>
               Debe de solicitar el archivo para que se procesado al (3233083629) o crear uno en excel con los campos aca informados y su respectivo formato.
            </li>
        </div>    
    </div>

    <?php ActiveForm::end(); ?>
</div>