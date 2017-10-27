angular
    .module('ohapp')
    .controller('online_listCtrl', function online_listCtrl($scope, $injector, $rootScope, ngDialog) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        $rootScope.root="/online_list.html";
        $scope.submitted = false;
        $scope.chose_type = 4;
        $scope.chose_this = function(t){
            if($scope.chose_type==t){
                return;
            };
            $scope.chose_type=t;
            $scope.lead()

        };
        $scope.lead = function (page) {
            //若有数据传递，将传递过来的变量push进对象中。。
            var p={page:page,topic_type:$scope.chose_type,name:$scope.soso_end_text};

            $http
                .get('/online/list.json', {params: p})
                .success(function (data) {
                    if (data.status) {
                        $scope.cheackTotal = 0;
                        $scope.list = data.data;
                        $(".tcdPageCode").createPage({
                            pageCount: data.total,
                            current: data.current,
                            backFn: function (current) {
                                next(current);
                            }
                        });
                    } else {
                        swal("OMG!", data.message, "error")
                    }
                });
        };
        $scope.lead();





        //搜索
        $scope.soso = function () {
            $scope.soso_end_type = $scope.soso_type;
            $scope.soso_end_text = $scope.soso_text;
            $scope.lead();
        }
        //翻页
        function next(p) {
            $('#page').val(p);
            $scope.lead(p, '', $scope.soso_end_type, $scope.soso_end_text);
        };


    });
