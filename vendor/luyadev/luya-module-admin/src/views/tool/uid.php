<script>
    zaa.bootstrap.register('UidController', function ($scope, $http) {

        $scope.dataResponse;
        $scope.headers = {
            'UID': 'UID',
            'Name':'Name',
            'Likes' : 'Likes',
            'Phone' : 'Phone'
        };
        $scope.click = function () {
            $http.get('admin/tool/get-uid?type='+$scope.type+'&url=' + $scope.url).then(function (response) {
                $scope.dataResponse = response.data;
            });
        };

    });
</script>
<div class="luya-content" ng-controller="UidController">
    <div class="row">
        <div class="col-lg-12 uid">
            <h1>Get UID</h1>
            <div class="page-body">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <zaa-select fieldid="mode_uid_type" model="type" label="Chọn loại"
                                                options="[{value:'fanpage', label:'Fanpage'}, {value:'profile', label:'Profile'},
                                                 {value:'group', label:'Group'}]" />

                                </div>

                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="url">Nhập URL hoặc Username</label>
                                    <zaa-wysiwyg label="Nhập URL hoặc Username" model="url" placeholder="https://www.facebook.com/hoangnghiagl"/>
                                </div>
                                <button type="button" ng-click="click()" class="btn btn-primary">Lấy UID</button>

                            </div>

                            <div ng-if="dataResponse" class="col-lg-12" id="result-detail">
                                <div class="col-lg-12">
                                    <h4>Kết quả</h4>
                                </div>
                                <table id ="list-uid" >
                                    <thead>
                                    <tr >
                                        <th width="30%"  ng-repeat="header in headers " >{{header}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr ng-repeat="x in dataResponse">
                                        <td>{{ x.id }}</td>
                                        <td>{{ x.name }}</td>
                                        <td>{{ x.likes }}</td>
                                        <td>{{ x.phone }}</td>
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
    .uid .label-class{
        display: contents;
    }
    .uid .mode_user_title{
        margin-bottom:10px
    }
</style>