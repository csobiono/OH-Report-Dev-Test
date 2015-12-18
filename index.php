<html lang="en" ng-app="reportApp"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Orderhood Report</title>

    <!-- Bootstrap core CSS -->
    <link href="styles/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="styles/dashboard.css" rel="stylesheet">
	
	<!--jQuery UI-->
	<link rel="stylesheet" href="styles/jquery-ui.css">
	
	<link rel="stylesheet" href="styles/custom.css">
	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	<!--Firebase :)-->
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
    <script src="javascript/firebase.js"></script>
    <script src="javascript/angularfire.min.js"></script>
	<script src="javascript/ui-bootstrap-tpls-0.14.3.min.js"></script>
	<script src="javascript/moment.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ng-csv/0.3.6/ng-csv.min.js"></script>
	<script src="javascript/angular-sanitize.min.js"></script>
  </head>

  <body cz-shortcut-listen="true" ng-controller="ReportController">

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><img src="styles/images/logo.png" class="img-responsive" width="35%"></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse navbar-right col-md-10">
          <form class="navbar-form navbar-right">
            <div class="col-md-5">
				<p class="input-group">
				  <input type="text" class="form-control" uib-datepicker-popup="{{format}}" ng-model="startDate" is-open="status.opened" min-date="minDate" max-date="maxDate" datepicker-options="dateOptions" date-disabled="disabled(date, mode)" ng-required="true" ng-change="toggleMin()" close-text="Close" />
				  <span class="input-group-btn">
					<button type="button" class="btn btn-default" ng-click="open('startDate')"><i class="glyphicon glyphicon-calendar"></i></button>
				  </span>
				</p>
			</div>
			<div class="col-md-2"><h3 id="dateSelectorTo"><center>TO</center></h3></div>
			<div class="col-md-5">
				<p class="input-group">
				  <input type="text" class="form-control" uib-datepicker-popup="{{format}}" ng-model="endDate" is-open="status.openedb" min-date="minBDate" max-date="maxDate" datepicker-options="dateOptions" date-disabled="disabled(date, mode)" ng-required="true" close-text="Close" ng-change="queryByDate()" />
				  <span class="input-group-btn">
					<button type="button" class="btn btn-default" ng-click="open('endDate')"><i class="glyphicon glyphicon-calendar"></i></button>
				  </span>
				</p>
			</div>
          </form>
        </div>
      </div>
    </nav>

    <div class="container-fluid" id="results" style="display: none;">
      <div class="row">
		<div id="test"></div>
		<div class="col-md-8 col-sm-6"></div>
		<div class="col-md-4 col-sm-6">
			
		</div>
        <div class="col-sm-12 col-md-12 main">
          <h3 class="page-header">Orders - {{ startDate | date:"fullDate" }} to {{ endDate | date:"fullDate" }} ({{ resultLength }} Total Results) <button style=float:right;" class ="btn" id="exportCSV" ng-csv="exportCSV()"  csv-header="columnsNeeded" csv-column-order="columnsNeeded" filename="report.csv" style="display: none;">Export CSV</button></h3>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>
					<button class="sort" ng-click="order('$id')">Order ID</button>
					<span class="sortorder" ng-show="predicate === '$id'" ng-class="{reverse:reverse}"></span>
				  </th>
                  <th>
					<button class="sort" ng-click="order('restaurant')">Restaurant Name</button>
					<span class="sortorder" ng-show="predicate === 'restaurant'" ng-class="{reverse:reverse}"></span>
				  </th>
                  <th>
					<button class="sort" ng-click="order('restaurant')">Runner Name</button>
					<span class="sortorder" ng-show="predicate === 'restaurant'" ng-class="{reverse:reverse}"></span>
				  </th>
                  <th>
					<button class="sort" ng-click="order('status')">Order Status</button>
					<span class="sortorder" ng-show="predicate === 'status'" ng-class="{reverse:reverse}"></span>
				  </th>
				  <th>
					<button class="sort" ng-click="order('orderAmount')">Order Amount</button>
					<span class="sortorder" ng-show="predicate === 'orderAmount'" ng-class="{reverse:reverse}"></span>
				  </th>
				  <th>
					<button class="sort"  ng-click="order('tipAmount')">Tip Amount</button>
					<span class="sortorder" ng-show="predicate === 'tipAmount'" ng-class="{reverse:reverse}"></span>
				  </th>
				  <th>
					<button class="sort" ng-click="order('ordererName')">Orderer Name</button>
					<span class="sortorder" ng-show="predicate === 'orderer.name'" ng-class="{reverse:reverse}"></span>
				  </th>
				  <th>
					<button class="sort" ng-click="order('orderTime')">Order Date & Time</button>
					<span class="sortorder" ng-show="predicate === 'orderTime'" ng-class="{reverse:reverse}"></span>
				  </th>
                </tr>
              </thead>
              <tbody>
				<tr ng-repeat="order in filteredOrders | orderBy:predicate:reverse">
				  <td><div class="orderId">{{ order.$id }}</div></td>
				  <td>{{ order.restaurantName }}</td>
				  <td>{{ order.runnerName }}</td>
			      <td>{{ order.status }}</td>
				  <td>{{ order.orderAmount }}</td>
				  <td>{{ order.tipAmount }}</td>
				  <td>{{ order.ordererName | capitalize:true }}</td>
				  <td>{{ order.orderTime }}</td>
				</tr>
              </tbody>
            </table>
          </div>
		  <div id="nodata" style="display: none;"><center><span style="font-size: 25px;">No Data Found - Please try another set of dates</center></div>
        </div>
      </div>
    </div>
	<div id="spinner"><center><span style="font-size: 25px;">Generating </span><img src="styles/images/ellipsis.gif" /></center></div>
		  

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="javascript/jquery.min.js"></script>
	<!--jQuery UI-->
	<script src="javascript/jquery-ui.js"></script>
	<!--Bootstrap-->
    <script src="javascript/bootstrap.min.js"></script>
	<!--AngularFire Callback-->
	<script src="javascript/callback.js"></script>
  
</body></html>