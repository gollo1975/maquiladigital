<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Grafica</title>

                 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                
		<style type="text/css">
#container {
    height: 400px;
}

.highcharts-figure,
.highcharts-data-table table {
    min-width: 310px;
    max-width: 800px;
    margin: 1em auto;
}

.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}

.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}

.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
}

		</style>
	</head>
	<body>
<script src="Highcharts-9.1.0/code/highcharts.js"></script>
<script src="Highcharts-9.1.0/code/modules/exporting.js"></script>
<script src="Highcharts-9.1.0/code/modules/export-data.js"></script>
<script src="Highcharts-9.1.0/code/modules/accessibility.js"></script>

<figure class="highcharts-figure">
    <div id="container"></div>
    <p class="highcharts-description">
        
    </p>
</figure>

<script type="text/javascript">
    var venta = <?php echo $venta ?>;
    var costo = <?php echo $costo?>;
    var utilidad = <?php echo $utilidad?>;
Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Grafica de rentabilidad del lote.'
    },
    subtitle: {
        text: 'Systime</a>'
    },
    xAxis: {
        categories: [
            'Venta',
            'Costo',
            'Utilidad',
        ],
        crosshair: true
    },
    
    yAxis: {
        min: 0,
        title: {
            text: 'Valores en miles'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:1f} </b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    series: [{
        name: 'Resultado',
        data: 
             [venta, costo, utilidad],
             
        }],    
         dataLabels: {
            enabled: true,
            rotation: -90,
            color: '#FFFFFF',
            align: 'right',
            format: '{point.y:.0f}', // one decimal
            y: 10, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
        
});

		</script>
	</body>
</html>
