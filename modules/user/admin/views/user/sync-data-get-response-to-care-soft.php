<script>
    zaa.bootstrap.register('SyncDataCareSoftController', function($scope, $http) {

        $scope.dataResponse;

        $scope.click = function() {
            $http.get('useradmin/user/data').then(function(response) {
                $scope.dataResponse = response.data;
            });
        };

    });
</script>
<div class="luya-content" ng-controller="SyncDataCareSoftController">
    <h1>Đồng bộ dữ liệu từ Get Response to Care Soft</h1>

    <button type="button" ng-click="click()" class="btn btn-primary">Sync</button>

    <div ng-if="dataResponse">
        The time is: {{ dataResponse.time }}
    </div>
</div>