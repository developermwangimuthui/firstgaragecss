<?php include('header.php'); ?>
<?php
//security
if($_SESSION['login_type'] != 'admin'){header("Location: " . WEB_URL . "logout.php");die();}
?>
<?php
//dashboard widget data
$customer = $wms->getAllCustomerList($link);
$parts_stock = $wms->partsStockTotalQty($link);
$car_stock = $wms->getAllActiveCarList($link);
$mechanic = $wms->getAllMechanicsList($link);
$settings = $wms->getWebsiteSettingsInformation($link);

//get all car info by current year
$total_car_year_sold = 0;
$sold_car = $wms->getSellCarMonthlyData($link, date('Y'));
$car_sell_report = '';
if(!empty($sold_car)) {
	foreach($sold_car as $scar) {
		$ccode = $wms->getChartColorCodeByMonth($scar['month_name']);
		$car_sell_report[] = array(
			'value'			=> $scar['total_sell'],
			'color'			=> $ccode,
			'highlight'		=> $ccode,
			'label'			=> ' Car Sold'.' '.$scar['month_name']
		);
		$total_car_year_sold += (int)$scar['total_sell'];
	}
	if(!empty($car_sell_report)){
		$car_sell_report = json_encode($car_sell_report, JSON_NUMERIC_CHECK);
	}
}

//get all parts info by current year
$sold_parts = $wms->getSellPartsMonthlyData($link, date('Y'));
$total_parts_year_sold = 0;
$parts_sell_report = '';
if(!empty($sold_parts)) {
	foreach($sold_parts as $sparts) {
		$ccode = $wms->getChartColorCodeByMonth($sparts['month_name']);
		$parts_sell_report[] = array(
			'value'			=> $sparts['total_parts'],
			'color'			=> $ccode,
			'highlight'		=> $ccode,
			'label'			=> ' Parts Sold'.' '.$sparts['month_name']
		);
		$total_parts_year_sold += (int)$sparts['total_parts'];
	}
	if(!empty($parts_sell_report)){
		$parts_sell_report = json_encode($parts_sell_report, JSON_NUMERIC_CHECK);
	}
}

//get car repair chart data
$months = array(
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July ',
    'August',
    'September',
    'October',
    'November',
    'December',
);

$total_car_repair_year = 0;
$car_repair_data_default = '0,0,0,0,0,0';
$car_repair = $wms->getCarRepairChartData($link, date('Y'));
if(!empty($car_repair)) {
	$car_repair_data = '';
	foreach($months as $month){
		$car_repair_data .= arrayValueExist($car_repair, $month).',';
	}
	$car_repair_data = trim($car_repair_data, ',');
	$car_repair_data_default = $car_repair_data;
	foreach($car_repair as $arr) {
		$total_car_repair_year += (int)$arr['total_repair'];
	}
}

function arrayValueExist($array, $value) {
	foreach($array as $arr) {
		if($arr['month_name'] == $value) {
			return $arr['total_repair'];
			break;
		}
	}
	return 0;
}

?>
<!-- Content Header (Page header) -->

<section class="content-header">
  <h1 style="text-transform:uppercase;font-weight:bold;color:#00a65a;"> <?php echo $settings['site_name'] .' Dashboard';?></h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo WEB_URL; ?>dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Dashboard</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <!-- /.row start -->
  <div class="row home_dash_box">
    <!-- col start -->
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?php echo $parts_stock; ?></h3>
          <p>PARTS IN STOCK</p>
        </div>
        <div class="icon"> <img height="80" width="80" src="img/car_parts.png"></a> </div>
        <a href="<?php echo WEB_URL; ?>parts_stock/partsstocklist.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> </div>
    </div>
    <!-- ./col end -->
    <!-- col start -->
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-orange">
        <div class="inner">
          <h3><?php echo count($customer); ?></h3>
          <p><?php echo count($customer) > 1 ? 'CUSTOMERS' : 'CUSTOMER'; ?></p>
        </div>
        <div class="icon"> <img height="80" width="80" src="img/customer.png"></a> </div>
        <a href="<?php echo WEB_URL; ?>customer/customerlist.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> </div>
    </div>
    <!-- ./col end -->
    <!-- col start -->
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-green">
        <div class="inner">
          <h3><?php echo count($car_stock); ?></h3>
          <p><?php echo count($car_stock) > 1 ? 'CARS IN STOCK' : 'CAR IN STOCK'; ?></p>
        </div>
        <div class="icon"> <img height="80" width="80" src="img/car.png"></a> </div>
        <a href="<?php echo WEB_URL; ?>carstock/buycarlist.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> </div>
    </div>
    <!-- ./col end -->
    <!-- col start -->
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-red">
        <div class="inner">
          <h3><?php echo count($mechanic); ?></h3>
          <p><?php echo count($mechanic) > 1 ? 'MECHANICS' : 'MECHANIC'; ?></p>
        </div>
        <div class="icon"> <img height="80" width="80" src="img/mechanic.png"></a> </div>
        <a href="<?php echo WEB_URL; ?>mechanics/mechanicslist.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> </div>
    </div>
    <!-- ./col end -->
  </div>
  <!-- /.row end -->
  <div class="row">
    <div class="col-md-8">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-car"></i> Monthly Car Repair Report</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
              <p class="text-center"> <strong>Car Repair: 1 Jan, <?php echo date('Y');?> - 31 December, <?php echo date('Y');?></strong> </p>
              <div class="chart">
                <canvas id="salesChart" style="height: 180px;"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="box-footer no-padding">
          <ul class="nav nav-pills nav-stacked">
            <li><a><b>Total Car Repair Year <?php echo date('Y');?></b><span class="pull-right label label-success" style="font-size:12px;"><b><?php echo $total_car_repair_year; ?></b></span></a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-wrench"></i> Monthly Parts Sold Report</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-lg-12 col-xs-12 col-md-12">
              <div class="chart-responsive">
                <canvas id="pieChart" height="120"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="box-footer no-padding">
          <ul class="nav nav-pills nav-stacked">
            <li><a><b>Total Parts Sold Year <?php echo date('Y');?></b><span class="pull-right label label-success" style="font-size:12px;"><b><?php echo $total_parts_year_sold; ?></b></span></a></li>
          </ul>
        </div>
      </div>
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-car"></i> Monthly Car Sold Report</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-lg-12 col-xs-12 col-md-12">
              <div class="chart-responsive">
                <canvas id="pieChart2" height="120"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="box-footer no-padding">
          <ul class="nav nav-pills nav-stacked">
            <li><a><b>Total Car Sold Year <?php echo date('Y');?></b><span class="pull-right label label-success" style="font-size:12px;"><b><?php echo $total_car_year_sold; ?></b></span></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Latest 10 Repair List</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin">
              <thead>
                <tr>
                  <th>Car ID</th>
                  <th>Estimate No</th>
                  <th>Customer Name</th>
                  <th>Car Name</th>
                  <th>Delivered</th>
                </tr>
              </thead>
              <tbody>
                <?php $delivery_list = $wms->getAllDeliveryCarList($link); ?>
                <?php $i=1; foreach($delivery_list as $dlist) { ?>
                <tr>
                  <td><label class="label label-success"><?php echo $dlist['repair_car_id']; ?></label></td>
                  <td><label class="label label-success"><?php echo $dlist['estimate_no']; ?></label></td>
                  <td><?php echo $dlist['c_name']; ?></td>
                  <td><?php echo $dlist['car_name']; ?></td>
                  <td><label class="label label-danger"><?php echo $wms->mySqlToDatePicker($dlist['delivery_done_date']); ?></label></td>
                </tr>
                <?php if($i==10) break;?>
                <?php $i++;} ?>
              </tbody>
            </table>
          </div>
          <!-- /.table-responsive -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix"><a href="<?php echo WEB_URL;?>delivery/deliverylist.php" class="btn btn-sm btn-success btn-flat pull-right"><b><i class="fa fa-list"></i> &nbsp;View All List</b></a> </div>
        <!-- /.box-footer -->
      </div>
    </div>
  </div>
</section>
<!-- /.content -->
<script type="text/javascript">
// Get context with jQuery - using jQuery's .get() method.
  var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
  // This will get the first returned node in the jQuery collection.
  var salesChart       = new Chart(salesChartCanvas);

  var salesChartData = {
    //labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July ', 'August', 'September', 'October', 'November', 'December'],
	labels  : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [
      {
        label               : 'Car Repair',
        fillColor           : 'rgba(0, 166, 90, 1)',
        strokeColor         : 'rgba(0, 166, 90, 1)',
        pointColor          : '#00a65a',
        pointStrokeColor    : 'rgba(0, 166, 90, 1)',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgba(0, 166, 90, 1)',
        data                : [<?php echo $car_repair_data_default; ?>]
      }
    ]
  };

  var salesChartOptions = {
    // Boolean - If we should show the scale at all
    showScale               : true,
    // Boolean - Whether grid lines are shown across the chart
    scaleShowGridLines      : true,
    // String - Colour of the grid lines
    scaleGridLineColor      : 'rgba(0,0,0,.05)',
    // Number - Width of the grid lines
    scaleGridLineWidth      : 1,
    // Boolean - Whether to show horizontal lines (except X axis)
    scaleShowHorizontalLines: true,
    // Boolean - Whether to show vertical lines (except Y axis)
    scaleShowVerticalLines  : true,
    // Boolean - Whether the line is curved between points
    bezierCurve             : true,
    // Number - Tension of the bezier curve between points
    bezierCurveTension      : 0.3,
    // Boolean - Whether to show a dot for each point
    pointDot                : true,
    // Number - Radius of each point dot in pixels
    pointDotRadius          : 4,
    // Number - Pixel width of point dot stroke
    pointDotStrokeWidth     : 1,
    // Number - amount extra to add to the radius to cater for hit detection outside the drawn point
    pointHitDetectionRadius : 20,
    // Boolean - Whether to show a stroke for datasets
    datasetStroke           : true,
    // Number - Pixel width of dataset stroke
    datasetStrokeWidth      : 2,
    // Boolean - Whether to fill the dataset with a color
    datasetFill             : true,
    // String - A legend template
    legendTemplate          : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<datasets.length; i++){%><li><span style=\'background-color:<%=datasets[i].lineColor%>\'></span><%=datasets[i].label%></li><%}%></ul>',
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio     : true,
    // Boolean - whether to make the chart responsive to window resizing
    responsive              : true
  };

  // Create the line chart
  salesChart.Line(salesChartData, salesChartOptions);

  // ---------------------------
  // - END MONTHLY SALES CHART -
  // ---------------------------
  
  
  // -------------
  // - PIE CHART -
  // -------------
  // Get context with jQuery - using jQuery's .get() method.
  var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
  var pieChart       = new Chart(pieChartCanvas);
  var PieData        = <?php echo $parts_sell_report; ?>;
  var pieOptions     = {
    // Boolean - Whether we should show a stroke on each segment
    segmentShowStroke    : true,
    // String - The colour of each segment stroke
    segmentStrokeColor   : '#fff',
    // Number - The width of each segment stroke
    segmentStrokeWidth   : 1,
    // Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    // Number - Amount of animation steps
    animationSteps       : 100,
    // String - Animation easing effect
    animationEasing      : 'easeOutBounce',
    // Boolean - Whether we animate the rotation of the Doughnut
    animateRotate        : true,
    // Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale         : false,
    // Boolean - whether to make the chart responsive to window resizing
    responsive           : true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    //maintainAspectRatio  : false,
    // String - A legend template
    //legendTemplate       : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
    // String - A tooltip template
    tooltipTemplate      : '<%=value %> <%=label%>'
  };
  // Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  // -----------------
  // - END PIE CHART -
  // -----------------
  
  
  // -------------
  // - PIE CHART -
  // -------------
  // Get context with jQuery - using jQuery's .get() method.
  var pieChartCanvas2 = $('#pieChart2').get(0).getContext('2d');
  var pieChart2       = new Chart(pieChartCanvas2);
  var PieData2        = <?php echo $car_sell_report; ?>;
  
  var pieOptions2     = {
    // Boolean - Whether we should show a stroke on each segment
    segmentShowStroke    : true,
    // String - The colour of each segment stroke
    segmentStrokeColor   : '#fff',
    // Number - The width of each segment stroke
    segmentStrokeWidth   : 1,
    // Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    // Number - Amount of animation steps
    animationSteps       : 100,
    // String - Animation easing effect
    animationEasing      : 'easeOutBounce',
    // Boolean - Whether we animate the rotation of the Doughnut
    animateRotate        : true,
    // Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale         : false,
    // Boolean - whether to make the chart responsive to window resizing
    responsive           : true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    //maintainAspectRatio  : false,
    // String - A legend template
    //legendTemplate       : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
    // String - A tooltip template
    tooltipTemplate      : '<%=value %> <%=label%>'
  };
  // Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart2.Doughnut(PieData2, pieOptions2);
  // -----------------
  // - END PIE CHART -
  // -----------------
</script>
<?php include('footer.php'); ?>
