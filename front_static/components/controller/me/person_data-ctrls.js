angular
    .module('ohapp')
    .controller('person_dataCtrl', function person_dataCtrl($scope, $injector, $rootScope) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');
        $rootScope.load_active = 1;

        //获取用户信息
        $scope.userinfo = function () {
            $http
                .get('/my/info.json')
                .success(function (data) {
                    if (data.status) {
                        if (data.data.length != 0) {
                            $scope.$emit('signMsg', data.data);
                        }
                        $scope.sign_msg = data.data;
                        if($scope.sign_msg.birthday!=0){
                            $scope.birthdays = $scope.sign_msg.birthday;
                        }
                    }
                });
        }
        $scope.userinfo();


        //获取医院信息
        $scope.chose_hosp = function (i) {
            $scope.is_his = i;
            $scope.shose_show = 1;
            $http
                .get('/region/province/drop.json')
                .success(function (data) {
                    $scope.provinces = data.data;
                });
        }
        //获取城市信息
        $scope.chose_this = function (i) {
            $scope.province_id = i;
            $scope.city_id = undefined;
            $http
                .get('/region/city/drop.json', {params: {province_id: i}})
                .success(function (data) {
                    $scope.cities = data.data;
                });
            $scope.his();
        }
        $scope.chose_city = function (i) {
            $scope.city_id = i;
            $scope.his();
        }

        $scope.chose_level = function (i) {
            $scope.level = i;
            $scope.his();
        }

        //获取城市信息
        $scope.his = function () {
            if ($scope.province_id != undefined && $scope.city_id != undefined && $scope.level != undefined) {
                $http
                    .get('/hospital/drop.json', {
                        params: {
                            province_id: $scope.province_id,
                            city_id: $scope.city_id,
                            level: $scope.level
                        }
                    })
                    .success(function (data) {

                        if ($scope.is_his == 1) {
                            $scope.sign_msg.perfect.hospital = data.data;
                        } else if ($scope.is_his == 2) {
                            $scope.sign_msg.perfect.teaching_hospital = data.data;
                        }
                    });
            }
        }

        //关闭弹窗
        $scope.close_proup = function () {
            $scope.shose_show = 0;
        }
        //提交选中医院
        $scope.ti_score = function (i) {
            $scope.shose_show = 0;
            $scope.hospital_id = i;
            angular.forEach($scope.hispo, function (data, index) {
                if (data.id == i) {
                    $scope.hospital = data.name;
                }
            })

        }


        //保存信息
        $scope.save = function () {
            $http
                .post('/user/perfect.json', {
                    role: $scope.sign_msg.role,
                    nickname: $scope.sign_msg.nickname,
                    name: $scope.sign_msg.name,
                    birthday: $(".birthday").val(),
                    post: $scope.sign_msg.perfect.post,
                    title: $scope.sign_msg.perfect.title,
                    hospital: $scope.sign_msg.perfect.hospital,
                    teaching_hospital: $scope.sign_msg.perfect.teaching_hospital
                })
                .success(function (data) {
                    if (data.status) {
                        $scope.userinfo();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }


    });
