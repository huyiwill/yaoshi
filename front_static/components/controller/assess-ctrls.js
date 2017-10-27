angular
    .module( 'ohapp' )
    .controller( 'assessCtrl', function assessCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');

        $scope.list_state = ['未知','未考核','批阅中','已批阅','已发布(成绩)','已结束']

        $scope.lead = function (p) {
            $http
                .get('/front/assess/list.json',{params:{page:p}})
                .success(function (data) {
                    if(data.status){
                        $scope.assess_list = data.data;
                        $scope.total_page = Number(data.total)
                        $(".tcdPageCode").createPage({
                            pageCount: Number(data.total),
                            current: Number(data.current),
                            backFn: function (current) {
                                next(current)
                            }
                        });
                    }else{
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
