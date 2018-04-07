<script>
    zaa.bootstrap.register('UidController', function ($scope, $http) {

        $scope.dataResponse;
        $scope.headers = {
            'UID': 'UID',
            'Name': 'Name',
            'Likes': 'Likes',
            'Phone': 'Phone'
        };
        $scope.types = [{value: 'fanpage', label: 'Fanpage'}, {value: 'profile', label: 'Profile'},
            {value: 'group', label: 'Group'}];
        $scope.click = function () {
            $http.get('admin/tool/get-uid?type=' + $scope.type + '&url=' + $scope.url).then(function (response) {
                $scope.dataResponse = response.data;
            });
        };
        $scope.findByGroupName = function () {
            var token = 'EAAAAAYsX7TsBAG434mBc5TeWJUfSbgBfCVgm1qkjDZBjyDYbRhMNf7DUGvm04prQyhBnsCswr1ZAT9qnBlFyoXFHx5Lk2zDIg4ZBHhqa9bDVKZAlSFTwjZARWWZCdwFoxMFUHBZAGGVroHrWQlJjXUsLEM5ZARKG47TO8r0EiYb9ovzL9u0dA5Uq9ZCLh60UIFro54QtD0xZC9JHbRlv6c1gnkUJLP8dx4tDkZD';
            $.ajax({
                type: 'GET',
                url: 'https://graph.facebook.com/search?type=group&limit=100&offset=0' + '&access_token=' + token,
                data: {
                    q: $scope.group_name
                },
                success: function(a) {
                    $('#lstSearchUIDGroupByName')['html']('');
                    for (var b = 0; b < a['data']['length']; b++) {
                        var c = a['data'][b];
                        $('#lstSearchUIDGroupByName').append('<li class="list-group-item"><span>' + c['name'] + '</span><a data-id="' + c['id'] + '" style="float:right" href="javascript:;" class="btn btn-xs blue"><i class="material-icons"></i></a></li>')
                    };
                },
                error: function(a) {}
            })
        }
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
                                    <zaa-select model="selectedType" fieldid="mode_uid_type" label="Tìm theo"
                                                options="[{value:'group', label:'Tìm UID thành viên nhóm'}, {value:'page', label:'UID like comment share bài viết, page, profile'},
                                                 {value:'friend', label:'Tìm UID bạn bè của UID'}]"/>
                                    <!--                                    <select  ng-change="changeType(this)" ng-model="type">-->
                                    <!--                                        <option ng-repeat="x in types" value="{{x.value}}">{{x.label}}</option>-->
                                    <!--                                    </select>-->

                                </div>

                            </div>
                            <div class="col-lg-12">
                                <div ng-show="selectedType == 'group'">
                                    <div class="form-group">
                                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                            <input class="form-control"
                                                   ng-model="group_name" type="text" placeholder="Tìm theo nhóm tên">
                                            <div class="input-group-addon" ng-click="findByGroupName()">
                                                <i class="material-icons">search</i>
                                            </div>
                                        </div>
                                        <ul style="max-height: 250px;overflow: auto;margin-bottom: -300px;z-index: 9;position: relative;box-shadow: 1px 1px 8px rgba(0, 0, 0, 0.4);" id="lstSearchUIDGroupByName" class="list-group"></ul>

                                    </div>
                                </div>
                                <div ng-show="selectedType == 'page'">
                                    <div class="form-group">
                                        <label for="url">Nhập URL hoặc Username</label>
                                        <zaa-wysiwyg label="Nhập URL hoặc Username" model="url"
                                                     placeholder="https://www.facebook.com/hoangnghiagl"/>
                                    </div>
                                </div>
                                <div ng-show="selectedType == 'friend'">
                                    <div class="form-group">
                                        <label for="url">Nhập URL hoặc Username</label>
                                        <zaa-wysiwyg label="Nhập URL hoặc Username" model="url"
                                                     placeholder="https://www.facebook.com/hoangnghiagl"/>
                                    </div>
                                </div>

                                <button type="button" ng-click="click()" class="btn btn-primary">Lấy UID</button>

                            </div>

                            <div ng-if="dataResponse" class="col-lg-12" id="result-detail">
                                <div class="col-lg-12">
                                    <h4>Kết quả</h4>
                                </div>
                                <table id="list-uid">
                                    <thead>
                                    <tr>
                                        <th width="30%" ng-repeat="header in headers ">{{header}}</th>
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
    .uid .label-class {
        display: contents;
    }

    .uid .mode_user_title {
        margin-bottom: 10px
    }
</style>