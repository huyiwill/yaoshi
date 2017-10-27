angular
    .module('ohapp')
    .controller('testCtrl', function testCtrl($scope, $injector, $rootScope) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        //备注：1.专项练习

        $scope.choose_type = function (i) {
            if (i === 1) {
                $http
                    .post('/cate/special.json')
                    .success(function (data) {
                        if (data.status) {
                            $scope.exercise_all = data.data;
                            console.log($scope.exercise_all.length);
                            $scope.shose_show = 1;
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            }
        };
        
        //选择分类
        $scope.begin_ex = function (i) {
            $state.go("main.testPractice",{id:i});
        }

    });
