<script>
    zaa.bootstrap.register('UidController', function ($scope, $http, $compile) {
        $scope.token = 'EAAAAAYsX7TsBAMJ1KJ0QejC4U1oi1HWaeWZCQnKjugVdBoZBmSZBZC9kfbWqpIX9wy8JReJvrtwTwgsXvqK2SUD0V53of8LyEQTnjvNMtzhlgoaOpuZAh3OFQxWtjy6ZBpo51p3PHBm2aAcHra545wTvNZAoK3C478573k2yLHc2YBsbU3ySRNl7dA2ZB3TFvbDlFgxVCHHR3wwL52MgRkmDd4wGwTJuoPMvTdk0VRZCF3wZDZD';
        $scope.dataResponse;
        $scope.paging;
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
            var cHtml = '';
            if($scope.group_name == '' || $scope.group_name == undefined){
                alert('Vui long nhap ten group');
                return false;
            }
            $('.login-spinner').show();
            $.ajax({
                type: 'GET',
                url: 'https://graph.facebook.com/search?type=group&limit=50&offset=0' + '&access_token=' + $scope.token,
                data: {
                    q: $scope.group_name
                },
                success: function(a) {
                    $('#lstSearchUIDGroupByName')['html']('');
                    for (var b = 0; b < a['data']['length']; b++) {
                        var c = a['data'][b];
                        $scope.paging = a['paging']['next'];
                        cHtml = $compile('<li class="list-group-item"><span>' + c['name'] + '</span><a data-id="' + c['id'] + '" style="float:right;color: #FFF; background-color: #3598dc; border-color: #3598dc;" href="javascript:;" class="btn btn-xs blue"><i class="material-icons">search</i></a></li>')($scope);
                        $('#lstSearchUIDGroupByName').append(cHtml)
                    };
                    cHtml = $compile('<li class="list-group-item view-more-group" ng-click="viewMoreGroup()"  ><span class="load-more-group" style="color: #4080ff; font-weight: bold; cursor: pointer;" data-url="' + $scope.paging + '">Xem thêm kết quả cho '+$scope.group_name+'</span></li>')($scope);
                    $('#lstSearchUIDGroupByName').append(cHtml);
                    $('.login-spinner').hide();
                },
                error: function(a) {}
            })
        }
        $scope.viewMoreGroup = function(){
            $('.load-more-group').html('Đang tìm, vui lòng chờ trong giây lát....');
            $.ajax({
                type: 'GET',
                url: $scope.paging,
                success: function(a) {
                    $('.view-more-group').remove();
                    for (var b = 0; b < a['data']['length']; b++) {
                        var c = a['data'][b];
                        $scope.paging = a['paging']['next'];
                        var cHtml = $compile('<li class="list-group-item"><span>' + c['name'] + '</span><a data-id="' + c['id'] + '" style="float:right;color: #FFF; background-color: #3598dc; border-color: #3598dc;" href="javascript:;" class="btn btn-xs blue"><i class="material-icons">search</i></a></li>')($scope);
                        $('#lstSearchUIDGroupByName').append(cHtml)
                    };
                    var cHtml = $compile('<li class="list-group-item view-more-group"><span style="color: #e5025e; font-weight: bold; cursor: pointer;" >Cuối kết quả tìm kiếm</span></li>')($scope);
                    if(c !== undefined){
                        var cHtml = $compile('<li class="list-group-item view-more-group" ng-click="viewMoreGroup()" ><span class="load-more-group" style="color: #4080ff; font-weight: bold; cursor: pointer;" data-url="' + $scope.paging + '">Xem thêm kết quả cho '+$scope.group_name+'</span></li>')($scope);
                    }
                    $('#lstSearchUIDGroupByName').append(cHtml);

                },
                error: function(a) {}
            })
        }
    });
</script>
<?php
use luya\web\Svg;
$spinner = Svg::widget([
    'folder' => Yii::getAlias("@admin/resources/svg"),
    'cssClass' => 'svg-spinner',
    'file' => 'login/spinner.svg'
]);
?>
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
                                            <div class="input-group-addon" style="background: #e5015f;" ng-click="findByGroupName()">
                                                <span style="border-color: #e5015f; background: #e5015f;" class="login-btn" type="submit"  tabindex="3">
                                                    <span style="background: #e5015f;color: white;" class="login-spinner"><?= $spinner; ?></span>
                                                    <i style="background: #e5015f;color: white;    font-size: 19px;" class="material-icons">search</i>
                                                </span>

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