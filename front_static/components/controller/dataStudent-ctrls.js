angular
    .module( 'ohapp' )
    .controller( 'dataStudentCtrl', function dataStudentCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');


        $http
            .post('/account.json')
            .success(function (data) {
                if(data.status){
                    if(data.data.length==0){
                        $state.go("main.logo",{"signin":1});
                    }
                    $scope.id = data.data.id;
                }else{
                    $state.go("main.logo",{"signin":1});
                }
            });

        $scope.step = 1;
        $scope.education = 1;
        $scope.isOtner = function (i) {
            if($scope.otnerhostipal != i){
                $scope.education = i;
            };
        };

        // next
        $scope.next = function(i){
            $scope.step = i;
        };
        $scope.prev = function(i){
            $scope.step = i;
        };
        //上传图片
        function ajaxupload_photo(data) {
            photoMsg = data;
            $scope.head = data;
            $scope.$apply();
        }

        $('.photo').localResizeIMG({
            width: 200,
            height: 200,
            quality: 1,
            success: function (result) {
                img = new Image;
                img.src = result.base64;
                ajaxupload_photo(img.src);
            }
        });
        //上传认证资料
        $('.path').localResizeIMG({
            width: 200,
            height: 200,
            quality: 1,
            success: function (result) {
                img = new Image;
                img.src = result.base64;
                ajaxupload_path(img.src);
            }
        });
        function ajaxupload_path(data) {
            photoMsg = data;
            $scope.path = data;
            $scope.$apply();
        }

        //确定提交更新
        $scope.send_end = function(){
            $http
                .post('/user/perfect.json',{id:$scope.id,role:5,name:$scope.name,entrance_time:$(".birthday").val(),head:$scope.head,photo:$scope.path,school:$scope.school,major:$scope.major,education:$scope.education})
                .success(function (data) {
                    if(data.status){
                        swal("药学工具网提醒您", "您的资料已完善","success");
                        $state.go("main.home");
                    }else{
                        swal("OMG!", data.message,"error");
                    }
                });
        }












    });
