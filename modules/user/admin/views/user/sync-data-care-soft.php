<script>
    zaa.bootstrap.register('SyncDataCareSoftController', function($scope, $http, LuyaLoading) {

        $scope.dataResponse;

        $scope.click = function() {
            LuyaLoading.start(i18n['js_dir_sync_data_caresoft_to_here']);
            $http.post('admin/api-user-sync-data/sync-data-care-soft').then(function(response) {
                $scope.dataResponse = response.data;
                LuyaLoading.stop();
            });

        };

    });
</script>
<div class="luya-content" ng-controller="SyncDataCareSoftController">
    <h1>Đồng bộ dữ liệu từ Care Soft</h1>

    <button type="button" ng-click="click()" class="btn btn-primary">Sync</button>

    <div ng-if="dataResponse">
        User added: {{ dataResponse.created }}
    </div>
</div>