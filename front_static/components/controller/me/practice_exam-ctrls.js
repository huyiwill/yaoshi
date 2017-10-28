angular
    .module('ohapp')
    .controller('practice_examCtrl', function practice_examCtrl($scope, $injector, $rootScope) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');
        $rootScope.load_active = 4;


        //获取真题信息
        $scope.lead = function (p) {
            $http
                .get('/my/examine/do.json', {params: {page: p}})
                .success(function (data) {
                    if (data.status) {
                        $scope.examine_list = data.data;
                        $scope.total_page = Number(data.total);
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
