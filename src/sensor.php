<?php
$pageTitle       = 'Sensoren';
$pageDescription = 'Alle lesenden Geräte';
$pageSkin        = 'black';
require_once("_header.php");

$devices = getDevices($pimaticUsername, $pimaticPassword, $pimaticHost);

$footerDataDyn = "";
$ids           = 0;

if (count($devices) === 0)
{
    echo renderError('Sensoren');
}

foreach ($devices as $device)
{
    if ($device->template !== 'temperature')
    {
        continue;
    }

    renderGraph($device);
    $footerDataDyn = generateGraphData($device, $ids, $footerDataDyn);
    $ids++;
}

$footerJs = <<<EOT
<!-- ChartJS 1.0.1 -->
<script src="adminlte/plugins/chartjs/Chart.min.js"></script>

<!-- page script -->
<script>
  $(function () {

    var lineChartOptions = {
      //Boolean - If we should show the scale at all
      showScale: true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines: false,
      //String - Colour of the grid lines
      scaleGridLineColor: "rgba(0,0,0,.05)",
      //Number - Width of the grid lines
      scaleGridLineWidth: 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines: true,
      //Boolean - Whether the line is curved between points
      bezierCurve: true,
      //Number - Tension of the bezier curve between points
      bezierCurveTension: 0.3,
      //Boolean - Whether to show a dot for each point
      pointDot: false,
      //Number - Radius of each point dot in pixels
      pointDotRadius: 4,
      //Number - Pixel width of point dot stroke
      pointDotStrokeWidth: 1,
      //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
      pointHitDetectionRadius: 20,
      //Boolean - Whether to show a stroke for datasets
      datasetStroke: true,
      //Number - Pixel width of dataset stroke
      datasetStrokeWidth: 2,
      //Boolean - Whether to fill the dataset with a color
      datasetFill: false,
      //String - A legend template
      legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
      //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio: true,
      //Boolean - whether to make the chart responsive to window resizing
      responsive: true
    };

    $footerDataDyn
  });
</script>
EOT;

require_once("_footer.php");
