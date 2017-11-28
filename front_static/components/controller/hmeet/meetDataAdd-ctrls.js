angular
    .module( 'ohapp' )
    .controller( 'meetAddCtrl', function meetAddCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');

                $scope.submitted = false;
        $scope.updates = {};
        $scope.updates.is_credit=2;

        $scope.next = function (form) {
            if (form.$valid) {
                if($scope.updates.is_credit==2){
                    $scope.updates.credis = 0;
                }
                $http
                    .post('/meeting/add.json', {
                        name: $scope.updates.name,
                        name_english: $scope.updates.name_english,
                        contacts: $scope.updates.contacts,
                        mobile: $scope.updates.mobile,
                        banner: $scope.updates.banner,
                        icon: $scope.updates.icon,
                        enroll_start: $scope.updates.enroll_start,
                        enroll_end: $scope.updates.enroll_end,
                        time_start: $scope.updates.time_start,
                        time_end: $scope.updates.time_end,
                        attend_time: $scope.updates.attend_time,
                        province_id: $scope.updates.province_id,
                        city_id: $scope.updates.city_id,
                        address: $scope.updates.address,
                        venue_name: $scope.updates.venue_name,
                        is_credit: $scope.updates.is_credit,
                        credis: $scope.updates.credis,
                        type: $scope.updates.type,
                        subject: $scope.updates.subject
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "新增成功！", "success");
                            $state.go('main.bs.meetMore',{id:data.data})
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            } else {
                $scope.submitted = true;
            }
        }
        $timeout(function () {
            $('#enroll_start').jHsDate({
                maxDate: '2025-12-31',
                format: 'yyyy-MM-dd hh时mm分',
                callBack: function (data) {
                    $scope.updates.enroll_start = $('#enroll_start').val();
                    $scope.$apply();
                }
            });
            $('#enroll_end').jHsDate({
                maxDate: '2025-12-31',
                format: 'yyyy-MM-dd hh时mm分',
                callBack: function () {
                    $scope.updates.enroll_end = $('#enroll_end').val();
                    $scope.$apply();
                }
            });
            $('#attend_time').jHsDate({
                maxDate: '2025-12-31',
                format: 'yyyy-MM-dd hh时mm分',
                callBack: function () {
                    $scope.updates.attend_time = $('#attend_time').val();
                    $scope.$apply();
                }
            });
            $('#time_start').jHsDate({
                maxDate: '2025-12-31',
                format: 'yyyy-MM-dd hh时mm分',
                callBack: function () {
                    $scope.updates.time_start = $('#time_start').val();
                    $scope.$apply();
                }
            });
            $('#time_end').jHsDate({
                maxDate: '2025-12-31',
                format: 'yyyy-MM-dd hh时mm分',
                callBack: function () {
                    $scope.updates.time_end = $('#time_end').val();
                    $scope.$apply();
                }
            });
        }, 1000)

        //上传图片
        function ajaxupload_banner(data) {
            photoMsg = data;
            $scope.updates.banner = data;
            $scope.$apply();
        }
        function ajaxupload_icon(data) {
            photoMsg = data;
            $scope.updates.icon = data;
            $scope.$apply();
        }

        $scope.banner = function () {
            $('#banner').localResizeIMG({
                width: 200,
                height: 200,
                quality: 1,
                success: function (result) {
                    img = new Image;
                    img.src = result.base64;
                    ajaxupload_banner(img.src);
                }
            });
        }
        $scope.icon = function () {
            $('#icon').localResizeIMG({
                width: 200,
                height: 200,
                quality: 1,
                success: function (result) {
                    img = new Image;
                    img.src = result.base64;
                    ajaxupload_icon(img.src);
                }
            });
        }

        //取消
        $scope.back = function () {
            history.back();
        }
        //获取下拉省份信息
        $http
            .get('/region/province/drop.json')
            .success(function (data) {
                $scope.provinces = data.data;
            });

        $scope.chose_this = function (i) {
            $http
                .get('/region/city/drop.json',{params:{province_id:i}})
                .success(function (data) {
                    $scope.cities = data.data;
                });
        }










    });
