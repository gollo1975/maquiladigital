<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Proyección de prestaciones';

?>
<style>
    .grid-view-custom {
        font-size: 12px; /* Ajusta aquí el tamaño de la letra */
    }
    .grid-view-custom th {
        text-align: center;
        background-color: #f9f9f9;
    }
</style>
<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
    <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Exportar a Excel', 
        ['exportar-excel', 'id' => $model->id_proyeccion], 
        ['class' => 'btn btn-success btn-sm']) 
    ?>
</p>
<?php $form = ActiveForm::begin(['action' => ['generar-masivo']]); ?>
<?= Html::hiddenInput('id', $model->id_proyeccion) ?>
<div class="panel-footer">
        <label>Generar para seleccionados:</label>
        <?= Html::submitButton('Generar Primas', ['name' => 'tipo', 'value' => 'primas', 'class' => 'btn btn-success']) ?>
        <?= Html::submitButton('Generar Cesantías', ['name' => 'tipo', 'value' => 'cesantias', 'class' => 'btn btn-info']) ?>
        <?= Html::submitButton('Generar Vacaciones', ['name' => 'tipo', 'value' => 'vacaciones', 'class' => 'btn btn-warning']) ?>
    </div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Seleccione empleados y el tipo de prestación a generar
        </h3>
    </div>
    
    <div class="panel-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            // Agregamos 'grid-view-custom' para el tamaño de letra
            'options' => ['class' => 'grid-view-custom'], 
            // Agregamos 'table-condensed' para reducir el padding de las celdas
            'tableOptions' => ['class' => 'table table-bordered table-striped table-hover table-condensed'],
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'seleccion[]',
                ],
                'cedula_empleado',
                'nombre_empleado',
                'fecha_inicio_contrato',
                'fecha_inicio',
                'fecha_corte',
                'numero_dias',
                // Opcional: formatear los valores numéricos para que se vean mejor
                ['attribute' => 'valor_prima', 'format' => ['decimal', 0]],
                ['attribute' => 'valor_cesantia', 'format' => ['decimal', 0]],
                ['attribute' => 'valor_intereses', 'format' => ['decimal', 0]],
                ['attribute' => 'valor_vacacion', 'format' => ['decimal', 0]],
                ['attribute' => 'total_linea', 'format' => ['decimal', 0]],
            ],
        ]); ?>
    </div>

    
</div>

<?php ActiveForm::end(); ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Resumen Consolidado de la Proyección</h3>
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <?php foreach ($model->getResumenValores() as $concepto => $valor): ?>
                <tr>
                    <th style="width: 70%;"><?= $concepto ?></th>
                    <td class="text-right"><strong><?= $valor ?></strong></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
