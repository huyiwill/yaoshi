angular
    .module( 'ohapp' )
    .controller( 'meCtrl', function meCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');

        $scope.arr_auth = ['未知','待审核','审核通过','审核未通过'];








    });
