<script>
    zaa.bootstrap.register('SyncDataGetResponseController', function($scope, $http, LuyaLoading) {

        $scope.dataResponse;

        $scope.click = function() {
            LuyaLoading.start(i18n['js_dir_sync_data_getresponse_to_here']);
            $http.post('admin/api-user-sync-data/sync-data-get-response').then(function(response) {
                $scope.dataResponse = response.data;
                LuyaLoading.stop();
            });

        };

    });
</script>
<div class="luya-content" ng-controller="SyncDataGetResponseController">
    <h1>Đồng bộ dữ liệu từ Get Response</h1>

    <button type="button" ng-click="click()" class="btn btn-primary">Sync</button>

    <div ng-if="dataResponse">
        User added: {{ dataResponse.created }}
    </div>
</div>