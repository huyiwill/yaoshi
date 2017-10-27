angular
    .module('ohapp')
    .controller('userCtrl', function userCtrl($scope, $injector, $rootScope, ngDialog) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');
        $rootScope.root = "/user.html";
        $scope.chose_type = 1;
        $scope.chose_this = function(t){
            if($scope.chose_type==t){
                    return;
            };
            $scope.chose_type=t;

        };


    });
