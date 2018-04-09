<!-- Latest compiled and minified CSS -->
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"-->
<!--      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">-->

<!-- Optional theme -->
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"-->
<!--      integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">-->

<!-- Latest compiled and minified JavaScript -->
<script>

    zaa.bootstrap.register('UidController', function ($scope, $http, $compile) {
        $scope.token = 'EAAAAAYsX7TsBAMJ1KJ0QejC4U1oi1HWaeWZCQnKjugVdBoZBmSZBZC9kfbWqpIX9wy8JReJvrtwTwgsXvqK2SUD0V53of8LyEQTnjvNMtzhlgoaOpuZAh3OFQxWtjy6ZBpo51p3PHBm2aAcHra545wTvNZAoK3C478573k2yLHc2YBsbU3ySRNl7dA2ZB3TFvbDlFgxVCHHR3wwL52MgRkmDd4wGwTJuoPMvTdk0VRZCF3wZDZD';
        $scope.dataResponseGroup;
        $scope.countMemberGroup = 0;
        $scope.paging;
        $scope.showResultGroup = false;
        $scope.stopAll = false;
        $scope.headers = {
            'UID': 'UID',
            'Name': 'Name',
        };
        $scope.types = [{value: 'fanpage', label: 'Fanpage'}, {value: 'profile', label: 'Profile'},
            {value: 'group', label: 'Group'}];
        $scope.click = function () {
            $http.get('admin/tool/get-uid?type=' + $scope.type + '&url=' + $scope.url).then(function (response) {
                $scope.dataResponse = response.data;
            });
        };
        $scope.selectGroup = function (obj) {
            // $('#lstSearchUIDGroupByName').hide();
            var url = 'https://graph.facebook.com/' + obj.target.attributes.data.value + '/members?limit=50' + '&access_token=' + $scope.token;
            excuteGet(url);
        }
        $scope.stopFindUID = function () {
            $scope.stopAll = true;
            console.log($scope.stopAll);
        }

        $(document).on('click', function (e) {
            if($scope.showResultGroup == true){
                if( $(e.target).closest(".form-key-search").length > 0 ) {
                    return false;
                }
                $('.result-search').addClass('ng-hide');
                $scope.showResultGroup = false;
            }
        });

        $( ".key-search" ).click(function() {
            if($('#lstSearchUIDGroupByName li').length > 0){
                $('.result-search').removeClass('ng-hide');
                $scope.showResultGroup = true;
            }
        });

        excuteGet = function (url) {
            if ($scope.stopAll) {
                console.log('stop');
                return
            }
            ;
            $.ajax({
                type: 'GET',
                url: url,
                success: function (a) {
                    var b = $('<div></div>');
                    for (var c = 0; c < a['data']['length']; c++) {
                        $('#count_uid_result').html($('#ResultTable tbody tr')['length']);
                        var d = a['data'][c];
                        b.append('<tr data-uid=\'' + d['id'] + '\'>' + '<td>' + ($('#ResultTable tbody tr')['length'] + 1) + '</td>' + '<td><a target=\'_blank\' href=\'https://www.facebook.com/' + d['id'] + '\'>' + d['id'] + '</a></td>' + '<td>' + d['name'] + '</td>' + '</tr>')
                    }
                    ;
                    $('#ResultTable tbody').append(b['children']());
                    try {
                        if (a['paging']['next'] != null) {
                            excuteGet(a['paging']['next']);
                        }
                    } catch (ex) {
                    }
                },
                error: function (a) {
                }
            })
        }
        $scope.findByGroupName = function () {
            var cHtml = '';
            if ($scope.group_name == '' || $scope.group_name == undefined) {
                alert('Vui lòng nhập tên group');
                return false;
            }
            $('.login-spinner').show();
            $('.result-search').removeClass('ng-hide');
            $('#lstSearchUIDGroupByName').html('');
            $.ajax({
                type: 'GET',
                url: 'https://graph.facebook.com/search?type=group&limit=100&offset=0' + '&access_token=' + $scope.token,
                data: {
                    q: $scope.group_name
                },
                success: function (a) {
                    for (var b = 0; b < a['data']['length']; b++) {
                        var c = a['data'][b];
                        $scope.paging = a['paging']['next'];
                        cHtml = $compile('<li class="list-group-item"><span>' + c['name'] + '</span><a ng-click="selectGroup($event)" data="' + c['id'] + '" style="float:right;color: #FFF; background-color: #3598dc; border-color: #3598dc;"  class="btn btn-xs blue"><i data="' + c['id'] + '" class="material-icons">search</i></a></li>')($scope);
                        $('#lstSearchUIDGroupByName').append(cHtml)
                    }
                    ;
                    cHtml = $compile('<li class="list-group-item view-more-group" ng-click="viewMoreGroup()"  ><span class="load-more-group" style="color: #4080ff; font-weight: bold; cursor: pointer;" data-url="' + $scope.paging + '">Xem thêm kết quả cho ' + $scope.group_name + '</span></li>')($scope);
                    $('#lstSearchUIDGroupByName').append(cHtml);
                    $('.login-spinner').hide();
                    $scope.showResultGroup = true;
                },
                error: function (a) {
                }
            })
        }
        $scope.viewMoreGroup = function () {
            $('.load-more-group').html('Đang tìm, vui lòng chờ trong giây lát....');
            $.ajax({
                type: 'GET',
                url: $scope.paging,
                success: function (a) {
                    $('.view-more-group').remove();
                    for (var b = 0; b < a['data']['length']; b++) {
                        var c = a['data'][b];
                        $scope.paging = a['paging']['next'];
                        var cHtml = $compile('<li class="list-group-item"><span>' + c['name'] + '</span><a data-id="' + c['id'] + '" style="float:right;color: #FFF; background-color: #3598dc; border-color: #3598dc;" href="javascript:;" class="btn btn-xs blue"><i class="material-icons">search</i></a></li>')($scope);
                        $('#lstSearchUIDGroupByName').append(cHtml)
                    }
                    ;
                    var cHtml = $compile('<li class="list-group-item view-more-group"><span style="color: #e5025e; font-weight: bold; cursor: pointer;" >Cuối kết quả tìm kiếm</span></li>')($scope);
                    if (c !== undefined) {
                        var cHtml = $compile('<li class="list-group-item view-more-group" ng-click="viewMoreGroup()" ><span class="load-more-group" style="color: #4080ff; font-weight: bold; cursor: pointer;" data-url="' + $scope.paging + '">Xem thêm kết quả cho ' + $scope.group_name + '</span></li>')($scope);
                    }
                    $('#lstSearchUIDGroupByName').append(cHtml);

                },
                error: function (a) {
                }
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
            <h1>Tìm UID Facebook</h1>
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <zaa-select model="selectedType" fieldid="mode_uid_type" label="Tìm theo"
                                            options="[{value:'group', label:'Tìm UID thành viên nhóm'}, {value:'page', label:'UID like comment share bài viết, page, profile'},
                                                 {value:'friend', label:'Tìm UID bạn bè của UID'}]"/>

                            </div>

                        </div>
                        <div class="tab-padded">
                            <!-- TYPE IS GROUP -->
                            <div ng-show="selectedType == 'group'">
                                <div class="row mt-2">
                                    <div class="col-md-4 col-lg-6 form-key-search col-xl-6 col-xxxl-8">
                                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                            <input ng-keyup="$event.keyCode == 13 ? findByGroupName() : null"
                                                   class="form-control key-search"
                                                   ng-model="group_name" type="text" placeholder="Tìm theo nhóm tên">
                                            <div class="input-group-addon" style="background: #e5015f;"
                                                 ng-click="findByGroupName()">
                                                        <span style="border-color: #e5015f; background: #e5015f;min-height: auto"
                                                              class="login-btn" type="submit" tabindex="3">
                                                            <span style="background: #e5015f;color: white;"
                                                                  class="login-spinner"><?= $spinner; ?></span>
                                                            <i style="background: #e5015f;color: white;    font-size: 19px;"
                                                               class="material-icons">search</i>
                                                        </span>
                                            </div>
                                        </div>
                                        <div class="result-search">
                                            <ul style="max-height: 250px;overflow: auto;z-index: 9;position: relative;box-shadow: 1px 1px 8px rgba(0, 0, 0, 0.4);"
                                                id="lstSearchUIDGroupByName" class="list-group"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-6 col-xl-6 col-xxxl-2">
                                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                                <textarea class="form-control" id="txtsearchUIDGroupMembers" rows="3"
                                                          placeholder="Link hoặc ID nhóm muốn tìm thành viên"></textarea>
                                            <button style="border-radius: 0; background-color: #e5085e; color: #FFF;" type="button" class="btn green"
                                                    onclick="searchUIDGroupMembers()"> Tìm<i
                                                        class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-4 col-lg-3 col-xl-3 col-xxxl-2">
                                        <div class="portlet-title">
                                            <div class="caption"
                                                 style="color: #666; padding: 10px 0;float: left; display: inline-block; font-size: 18px; line-height: 18px; padding: 10px 0;">
                                                <i class="icon-layers font-green-sharp"></i>
                                                <span class="caption-subject font-green-sharp bold uppercase"
                                                      style="    font-size: 16px;    color: #2ab4c0!important;">Kết quả tìm kiếm</span>
                                            </div>
                                            <div id="ResultTable_Tool" style="float:right;">
                                                <button type="button" class="btn green" onclick="saveResultToTxt()"> Lưu
                                                    UID<i
                                                            class="fa fa-download"></i></button>
                                                <button type="button" class="btn green" onclick="copyUIDResult()"> Copy
                                                    UID<i class="fa fa-copy"></i></button>
                                            </div>
                                            <label>Đã tìm thấy <span id="count_uid_result">0</span> UID</label>
                                            <a href="javascript:void(0)" class="btn btn-xs red" ng-click="stopFindUID()"
                                               id="btnStopFindUID">
                                                Stop
                                            </a>
                                        </div>
                                        <div style="max-height: 300px; overflow: auto;"
                                             class="table-responsive-wrapper">

                                            <table id="ResultTable"
                                                   class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th></th>
                                                    <th><span data-toggle="tooltip" data-placement="top"
                                                              data-original-title="" title="">UID</span></th>
                                                    <th><span data-toggle="tooltip" data-placement="top"
                                                              data-original-title="" title="">Name</span></th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END TYPE IS GROUP -->
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">


                                <!-- TYPE IS PAGE -->
                                <div ng-show="selectedType == 'page'">
                                    <div class="form-group">
                                        <label for="url">Nhập URL hoặc Username</label>
                                        <zaa-wysiwyg label="Nhập URL hoặc Username" model="url"
                                                     placeholder="https://www.facebook.com/hoangnghiagl"/>
                                    </div>
                                </div>
                                <!-- END TYPE IS GROUP -->

                                <!-- TYPE IS FRIEND -->
                                <div ng-show="selectedType == 'friend'">
                                    <div class="form-group">
                                        <label for="url">Nhập URL hoặc Username</label>
                                        <zaa-wysiwyg label="Nhập URL hoặc Username" model="url"
                                                     placeholder="https://www.facebook.com/hoangnghiagl"/>
                                    </div>
                                </div>
                                <!-- END TYPE IS FRIEND -->
                            </div>

                            <!--                                <button type="button" ng-click="click()" class="btn btn-primary">Lấy UID</button>-->

                        </div>
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