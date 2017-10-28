angular
    .module('ohapp')
    .controller('practice_collectCtrl', function practice_collectCtrl($scope, $injector, $rootScope) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');
        $rootScope.load_active = 4;


//获取列表
        $scope.purpose_list = [];
        $scope.lead = function (p) {
            $http
                .post('/purpose/list/one.json',{page: p})
                .success(function (data) {
                    if (data.status) {
                        angular.forEash(data.data.one_result,function (data,index) {
                            $scope.purpose_list.push(data);
                        })
                        angular.forEash(data.data.two_result,function (data,index) {
                            $scope.purpose_list.push(data);
                        })
                        $scope.total_page = Number(data.total)
                        $(".tcdPageCode").createPage({
                            pageCount: Number(data.total),
                            current: Number(data.current),
                            backFn: function (current) {
                                next(current)
                            }
                        });
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        $scope.lead();
        //翻页
        function next(p) {
            $('#page').val(p);
            $scope.lead(p);
        };


    });
