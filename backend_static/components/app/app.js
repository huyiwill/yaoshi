angular.module('ohapp',
    [
        'ngResource',
        'ui.router',
        'ohRoutes',
        'oc.lazyLoad',
        'ohConfig',
        'ngAnimate',
        'ngDialog',
        'ngMaterial',
        'ngMdIcons',
        'ngAria'
    ]
)
    .config(function config($injector, $locationProvider) {
        var $stateProvider = $injector.get('$stateProvider');
        var $urlRouterProvider = $injector.get('$urlRouterProvider');
        var $routesProvider = $injector.get('$routesProvider');
        var $httpProvider = $injector.get('$httpProvider');
        var $config = $injector.get('$configProvider').$get();


        $urlRouterProvider.otherwise('/404.html');

        $httpProvider.interceptors.push('AuthInterceptor')
        $httpProvider.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded;charset=utf-8";
        //$httpProvider.defaults.withCredentials = true;

        $locationProvider.html5Mode(true).hashPrefix('!');

        var routes = $routesProvider.routes;

        angular.forEach(routes, function (value, key) {
            $stateProvider.state(key, routes[key]);
        });

        /**
         * The workhorse; converts an object to x-www-form-urlencoded serialization.
         * @param {Object} obj
         * @return {String}
         */
        var param = function (obj) {
            var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

            for (name in obj) {
                value = obj[name];

                if (value instanceof Array) {
                    for (i = 0; i < value.length; ++i) {
                        subValue = value[i];
                        fullSubName = name + '[' + i + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                }
                else if (value instanceof Object) {
                    for (subName in value) {
                        subValue = value[subName];
                        fullSubName = name + '[' + subName + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                }
                else if (value !== undefined && value !== null)
                    query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
            }

            return query.length ? query.substr(0, query.length - 1) : query;
        };

        // Override $http service's default transformRequest
        $httpProvider.defaults.transformRequest = [function (data) {
            return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
        }];

    })
    .filter('to_trusted', ['$sce', function ($sce) {
        return function (text) {
            return $sce.trustAsHtml(text);
        };
    }])
    .directive("fileModel", ["$parse", function ($parse) {
        return {
            restrict: "A",
            link: function (scope, element, attrs) {
                var model = $parse(attrs.fileModel);
                var modelSetter = model.assign;

                element.bind("change", function () {
                    scope.$apply(function () {
                        modelSetter(scope, element[0].files[0]);
                    })
                })
            }
        }
    }])
    .filter('cut', function () {
        return function (value, wordwise, max, tail) {
            if (!value) return '';

            max = parseInt(max, 10);
            if (!max) return value;
            if (value.length <= max) return value;

            value = value.substr(0, max);
            if (wordwise) {
                var lastspace = value.lastIndexOf(' ');
                if (lastspace != -1) {
                    value = value.substr(0, lastspace);
                }
            }

            return value + (tail || ' …');
        };
    })
    .service( "fileUpload", ["$http", function( $http ){
        var fd = new FormData();
        this.uploadFileToUrl = function( file, uploadUrl,ids ){
            fd.append( "data", file );
            fd.append( "id", ids );
            $http.post( uploadUrl,fd, {
                transformRequest: angular.identity,
                headers: { "Content-Type": undefined },
            })
                .success(function(data){
                    if(data.status){

                    }else{
                        swal("OMG!", data.message, "error");
                    }
                })
                .error( function(){

                })
        }
    }])
