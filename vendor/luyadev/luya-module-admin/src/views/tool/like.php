<script src="https://cdn.rawgit.com/eligrey/FileSaver.js/5ed507ef8aa53d8ecfea96d96bc7214cd2476fd2/FileSaver.min.js" type="text/javascript"></script>
<script>
    zaa.bootstrap.register('LikeController', function ($scope, $http) {

        $scope.dataResponse;
        $scope.headers = {
            'UID': 'UID'
        };
        $scope.click = function () {
            $http.get('admin/tool/get-like?uid=' + $scope.uid).then(function (response) {
                $scope.dataResponse = response.data;
                // var textarea = document.getElementById("your_textarea");
                // // textarea.value = response.data.join("\n");
                // console.log(textarea.value);
                $('#your_textarea').val(response.data.join("\n"));
            });
        };

        $scope.exportData = function () {
            // var blob = new Blob([document.getElementById('exportable').innerHTML], {
            //     type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
            // });
            // saveAs(blob, "data.xls");
            var csvData = ConvertToCSV($scope.dataResponse);
            var a = document.createElement("a");
            a.setAttribute('style', 'display:none;');
            document.body.appendChild(a);
            var blob = new Blob([csvData], { type: 'text/csv;charset=UTF-8' });
            var url= window.URL.createObjectURL(blob);
            a.href = url;
            a.download = 'User_Results.csv';/* your file name*/
            a.click();
            return 'success';
        };

        $scope.createObject = function(){
            $scope.newAr = []

            for(var i = 0; i < $scope.dataResponse.length; i++){
                var str = $scope.dataResponse[i]

                var splitStr = str.split(";");
                var obj = {};

                for(var j = 0; j < splitStr.length; j++){
                    obj['field'+(j+1)] = splitStr[j];
                }
                $scope.newAr.push(obj);
            }
            console.log($scope.newAr);
        }

    });

    function ConvertToCSV(objArray)
    {
        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = '';
        var row = "";

        for (var index in objArray[0]) {
            //Now convert each value to string and comma-separated
            row += index + ',';
        }
        row = row.slice(0, -1);
        //append Label row with line break
        str += row + '\r\n';

        for (var i = 0; i < array.length; i++) {
            var line = '';
            for (var index in array[i]) {
                if (line != '') line += ','

                line += array[i][index];
            }
            str += line + '\r\n';
        }
        return "\ufeff". str;
    }

</script>
<div class="luya-content" ng-controller="LikeController">
    <div class="row">
        <div class="col-lg-12 like">
            <h1>Get People Likes Page</h1>
            <div class="page-body">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <zaa-text label="Nhập UID or URL Fanpage" placeholder="https://www.facebook.com/hoangnghiagl" model="uid"/>
                                </div>
                                <button type="button" ng-click="click()" class="btn btn-primary">Get NOW</button>

                            </div>

                            <div ng-if="dataResponse" class="col-lg-12" id="result-detail">
                                <div class="col-lg-12">
                                    <h4>Kết quả</h4>
                                    <button ng-click="exportData()">Export</button>
                                </div>
<!--                                <zaa-table model="{{ dataResponse.data }}"/>-->
                                <div id="exportable">
<!--                                <table id ="myTable" >-->
<!--                                    <thead>-->
<!--                                    <tr >-->
<!--                                        <th width="30%"  ng-repeat="header in headers " >{{header}}</th>-->
<!--                                    </tr>-->
<!--                                    </thead>-->
<!--                                    <tbody>-->
<!--                                    <tr ng-repeat="x in dataResponse">-->
<!--                                        <td>{{ x.id }}</td>-->
<!--                                    </tr>-->
<!--                                    </tbody>-->
<!--                                </table>-->
                                    <textarea style="width: 100%; min-height: 200px;" id="your_textarea"></textarea>
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
    .like .label-class{
        display: contents;
    }
    .like .label-class label{
        margin-bottom: 10px;
    }
</style>

