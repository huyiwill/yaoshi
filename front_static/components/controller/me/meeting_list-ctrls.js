angular
    .module( 'ohapp' )
    .controller( 'meeting_listCtrl', function meeting_listCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');
        $rootScope.load_active = 7;
        $scope.lead = function (current, p, time) {

            $http
                .get('/my/meeting.json', {
                    params: {
                        page: current
                    }
                })
                .success(function (data) {
                    if (data.status) {
                        $scope.meeting = data.data;
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
