angular
    .module( 'ohapp' )
    .controller( 'assessExamCtrl', function assessExamCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');

        $scope.do = function () {
            $http
                .get('/front/assess/start.json', {id: $stateParams.id})
                .success(function (data) {
                    if (data.status) {
                        $scope.assess_data = data.data;
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }








    });
