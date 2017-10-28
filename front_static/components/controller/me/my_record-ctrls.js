angular
    .module( 'ohapp' )
    .controller( 'my_recordCtrl', function my_recordCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');
        $rootScope.load_active = 2;

        $scope.buy_type = ['未知','线上购买','会员卡支付'];
        $scope.time_types = ['未知','月','年'];

        //获取会员信息
        $scope.lead = function (p) {
            $http
                .get('/member/list.json',{params:{page:p}})
                .success(function (data) {
                    if(data.status){
                        $scope.member_list = data.data;
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
