angular
    .module('ohapp')
    .controller('person_accountCtrl', function person_accountCtrl($scope, $injector,$location, $rootScope,$interval) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');
        $rootScope.load_active = 1;

        $scope.time = "获取验证码";
        //获取验证码
        $scope.getCode = function () {
            $http
                .post('/verification.json', {mobile: $scope.mobile_new})
                .success(function (data) {
                    if (data.status) {
                        swal("药学工具网提醒您", "验证码已发送", "success");
                        $scope.time = 60;
                        $scope.timmer = $interval(toDo, 1000);

                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        function toDo() {
            $scope.time = $scope.time - 1;
            if ($scope.time == 0) {
                $interval.cancel($scope.timmer);
                $scope.time = "获取验证码";
            }
        }


        //提交
        $scope.submitForm = function (form) {
            if (form) {
                $http
                    .post('/my/mobile.json', {mobile: $scope.mobile_new, verification: $scope.verification})
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "手机修改成功！", "success");
                            $http
                                .post('/logout.json')
                                .success(function (data) {
                                    if (data.status) {
                                        $state.go("main.logo",{'signin':1});
                                    }
                                });
                        } else {
                            swal("OMG!", data.message, "error")
                        }
                    });
            } else {
                $scope.submitted = true;
            }
        }


    });
