angular
    .module('ohapp')
    .controller('chose_tianCtrl', function chose_tianCtrl($scope, $injector, $rootScope, ngDialog) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        $scope.chose_type = 1;
        $scope.chose_this = function(t){
            if($scope.chose_type==t){
                return;
            };
            $scope.chose_type=t;

        };


    });
