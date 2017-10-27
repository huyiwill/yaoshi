angular
  .module('ohapp')
  .factory('AuthInterceptor', function($rootScope, $q, $session, $injector) {
    return {
      request: function (response) {
        var token = $session.get('auth').token
        var ignoreUrl = [
              '//maps.googleapis.com/maps/api/geocode/json'
        ]

        if (undefined !== token && jQuery.inArray(response.url, ignoreUrl) < 0) {
          response.headers['Token'] = token
        }

        if (/\/1\//.test(response.url)) {
          response.headers['x-referer'] = document.location.origin + '/' + $rootScope.$state.href($rootScope.$state.current)
        }

        return response
      },
      responseError: function (rejection) {
        var $state = $injector.get('$state');
        if (401 === rejection.status) {
          $session.purge('auth');
          $rootScope.$isLogin = false;
          $state.go('signin')
        }

        return $q.reject(rejection)
      }
    }
  })