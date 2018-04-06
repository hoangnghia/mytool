<script xmlns="http://www.w3.org/1999/html">
    zaa.bootstrap.register('SyncDataCareSoftController', function($scope, $http, LuyaLoading) {

        $scope.dataResponse;

        $scope.click = function() {
            LuyaLoading.start(i18n['js_dir_sync_data_caresoft_to_getresponse']);
            $http.post('admin/api-user-sync-data/sync-data-care-soft-to-get-response').then(function(response) {
                console.log(response.data);
                $scope.dataResponse = response.data;
                LuyaLoading.stop();
            });

        };
    });
</script>
<div class="luya-content" ng-controller="SyncDataCareSoftController">
    <h1>Đồng bộ dữ liệu từ Care Soft to Ges Response</h1>

    <button type="button" ng-click="click()" class="btn btn-primary">Sync</button>

    <div ng-if="dataResponse">
        Contact added: {{ dataResponse.created }} </br>
        Contact exists: {{ dataResponse.exists }} </br>
    </div>
</div>