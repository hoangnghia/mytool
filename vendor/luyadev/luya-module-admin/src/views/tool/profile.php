<script>
    zaa.bootstrap.register('ProfileController', function ($scope, $http) {

        $scope.dataResponse;
        $scope.headers = {
            'UID': 'UID',
            'Full Name':'Full Name',
            'Birthday':'Birthday',
            'Phone':'Profile Name',
            'Email':'Email',
            'Address' : 'Address'
        };
        $scope.click = function () {
            $http.get('admin/tool/get-profile?uid=' + $scope.uid).then(function (response) {
                $scope.dataResponse = response.data;
            });
        };

        $scope.importData = function () {

            // image does not exists make request.
            $http.post('admin/tool/get-profile', $.param({ file : $scope.file}), {
                headers : {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).then(function(response) {

            }, function(error) {

            });
        };

        $scope.uploadFile = function(files) {
            var fd = new FormData();
            //Take the first selected file
            fd.append("file", files[0]);

            $http.post('admin/tool/get-profile', fd, {
                withCredentials: true,
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                transformRequest: angular.identity
            }).then(function(response) {

            }, function(error) {

            });

        };



    });
</script>
<div class="luya-content" ng-controller="ProfileController">
    <div class="row">
        <div class="col-lg-12 like">
            <h1>Get Profiles Details</h1>
            <div class="page-body">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <zaa-wysiwyg label="Nhập URL hoặc Username" model="uid" placeholder="https://www.facebook.com/hoangnghiagl"/>
                                </div>
                                <button type="button" ng-click="click()" class="btn btn-primary">Get</button>

                            </div>
                            <hr>
                            <div class="col-lg-12">
                                <tool-file-manager/>
<!--                                <input type="file" name="file" onchange="angular.element(this).scope().uploadFile(this.files)"/>-->
<!--                                <input ng-model="file" type="file" name="file">-->
<!--                                <button type="button" ng-click="importData()" class="btn btn-primary">Import</button>-->
                            </div>

                            <div ng-if="dataResponse" class="col-lg-12" id="result-detail">
                                <div class="col-lg-12">
                                    <h4>Kết quả</h4>
                                </div>
<!--                                <zaa-table model="{{ dataResponse.data }}"/>-->
                                <table id ="myTable" >
                                    <thead>
                                    <tr >
                                        <th width="30%"  ng-repeat="header in headers " >{{header}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr ng-repeat="x in dataResponse">
                                        <td>{{ x.id }}</td>
                                        <td>{{ x.name }}</td>
                                        <td>{{ x.birthday }}</td>
                                        <td>{{ x.mobile_phone }}</td>
                                        <td>{{ x.email }}</td>
                                        <td>{{ x.location.name }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div
            </div>
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<style>
    .like .label-class{
        display: contents;
    }
    .like .label-class label{
        margin-bottom: 10px;
    }
</style>