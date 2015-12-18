angular.module('reportApp', ['ngSanitize', 'ngCsv','firebase','ui.bootstrap']).controller('ReportController', ['$scope', '$firebaseArray',
    function($scope, $firebaseArray) {
        var ref = new Firebase('https://oh-developer-test.firebaseio.com/orders');

		$scope.startDate = moment.utc().startOf("isoweek").subtract(7, 'days').valueOf();
		$scope.endDate = moment.utc().startOf("isoweek").subtract(1, 'day').valueOf();

		$scope.queryByDate = function(){
			
			$("#spinner").show();
			$("#nodata").hide();
			$("#exportCSV").hide();
			$scope.resultLength = 0;
			
			$scope.startDate = moment.utc($scope.startDate).hour(0).minute(0).second(0)
			$scope.startDate = moment.utc($scope.startDate).valueOf()
						
			$scope.endDate = moment.utc($scope.endDate).hour(15)
			$scope.endDate = moment.utc($scope.endDate).valueOf()
			
			console.log("Start Date: "+$scope.startDate)
			console.log("End Date: "+$scope.endDate)
			
			var query = ref.orderByChild("log/orderTime").startAt($scope.startDate).endAt($scope.endDate);
			
			// the $firebaseArray service properly handles database queries as well
			$scope.filteredOrders = $firebaseArray(query);
			
			$scope.filteredOrders.$loaded()
			  .then(function() {
				console.log($scope.filteredOrders); // true
				if($scope.filteredOrders.length == 0){
					$("#nodata").show();
					$("#exportCSV").hide();
				}
				else {
					angular.forEach($scope.filteredOrders, function(o){
						o.ordererName = o.orderer.name;
						o.orderTime = moment.utc(o.log.orderTime).format("L hh:mm:ss a")
						var restaurantID = new Firebase("https://oh-developer-test.firebaseio.com/restaurants/"+o.restaurant)
						
						restaurantID.on("value",function(snap){
						
							var d = snap.val();
							o.restaurantName = d.name
						})
						
						if(o.runner){
							var runnerID = new Firebase("https://oh-developer-test.firebaseio.com/runners/"+o.runner)
						
							runnerID.on("value",function(snap){
							
								var d = snap.val();
								o.runnerName = d.name
							})
						}
						else {
							o.runnerName = "Runner Not Yet Assigned"
						}
						
					})
					$("#nodata").hide();
					$("#exportCSV").show();
				}
				$("#results").show();
				$("#spinner").hide();
				$scope.resultLength = $scope.filteredOrders.length
			  })
			  .catch(function(error) {
				console.error("Error:", error);
				$scope.loaded = false;
				$("#spinner").hide();
			  });
		  
		}
		$scope.queryByDate();
			
			$scope.predicate = 'log.orderTime';
			$scope.reverse = true;
			$scope.order = function(predicate) {
				$scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
				$scope.predicate = predicate;
			};
			
			//ALL AngularUI			
			  $scope.disabled = function(date, mode) {
				//return ( mode === 'day' && ( date.getDay() === 0 || date.getDay() === 6 ) ); // this will disable weekends
				return 0;
			  };
			
			  $scope.toggleMin = function() {
				$scope.minDate = $scope.minDate ? null : 0;
				$scope.minBDate = $scope.startDate ? $scope.startDate : 0;
				$scope.queryByDate();
			  };
			  $scope.toggleMin();
			  
			  $scope.maxDate = moment.utc();

			  $scope.open = function(dateInput) {
				  if(dateInput == "startDate"){
					  $scope.status.opened = true;
				  }
				  else if(dateInput == "endDate"){
					  $scope.status.openedb = true;
				  }
			  };

			  $scope.setDate = function(year, month, day) {
				$scope.startDate = new Date(year, month, day);
				$scope.endDate = new Date(year, month, day);
			  };

			  $scope.dateOptions = {
				formatYear: 'yy',
				startingDay: 1
			  };

			  $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
			  $scope.format = $scope.formats[0];

			  $scope.status = {
				opened: false,
				openedb:false
			  };
			  
			  /*CSV download*/
			  $scope.exportCSV = function() {
				  return $scope.filteredOrders;
			};
				$scope.columnsNeeded = ['$id','restaurantName','runnerName','status','orderAmount', 'tipAmount', 'ordererName', 'orderTime'];
				
    }])
	.filter('capitalize', function() {
		return function(input, all) {
		  var reg = (all) ? /([^\W_]+[^\s-]*) */g : /([^\W_]+[^\s-]*)/;
		  return (!!input) ? input.replace(reg, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();}) : '';
		}
	  })
	 
