angular
    .module('ohapp')
    .controller('logoCtrl', function logoCtrl($scope, $injector, $rootScope, $stateParams, $interval) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        $scope.invitation = $stateParams.vercode;

        $scope.time = "获取验证码";
        $scope.u_msg = {};
        $scope.stateSignin = $stateParams.signin;
        //判断是否为微信注册
        if (GetRequest().code && !$stateParams.signin) {
            $scope.stateSignin = undefined;
            $http
                .post('/check.json', {code: GetRequest().code})
                .success(function (data) {
                    if (data.status) {
                        if (data.code == 0) {
                            $state.go("main.home");
                        } else if (data.code == 1) {
                            $scope.other = 1;
                            $scope.u_msg = data.data.user_info;
                        } else if (data.code == 2) {
                            swal("OMG!", "您已被禁用，请联系管理员解除", "error");
                        }
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        if ($scope.stateSignin == 1) {
            $scope.signins = 1;
        } else if ($scope.stateSignin == 0) {
            $scope.signup = 1;
        } else if ($scope.stateSignin == "reset") {
            $scope.reset = 1;
        }
        $scope.chose_sign = function (s) {
            if (s == 1) {
                $scope.signin = 1;
                $scope.signup = 0;
            }
        };

        //绑定信息
        //绑定登录信息
        $scope.u_signin = function () {
            $scope.resign = 1;
            $scope.other = 0;
        }
        //绑定注册信息
        $scope.u_signup = function () {
            $scope.signup = 1;
            $scope.other = 0;
        }
        $scope.bd_back = function () {
            $scope.other = 1;
            $scope.resign = 0;
        }

        $scope.chose_yzm = function () {
            $http
                .post('/verification.json', {mobile: $scope.signup_phone})
                .success(function (data) {
                    if (data.status) {
                        swal("易百加提醒您", "验证码已发送", "success");
                        $scope.time = 60;
                        $scope.timmer = $interval(toDo, 1000);

                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        };
        function toDo() {
            $scope.time = $scope.time - 1;
            if ($scope.time == 0) {
                $interval.cancel($scope.timmer);
                $scope.time = "获取验证码";
            }
        }

        //注册
        $scope.signup_submit = function () {
            $http
                .post('/register.json', {
                    unionid: $scope.u_msg.unionid,
                    mobile: $scope.signup_phone,
                    nickname: $scope.signup_nick,
                    password: $scope.signup_pwd,
                    code: $scope.invitation,
                    verification: $scope.signup_yzm
                })
                .success(function (data) {
                    if (data.status) {
                        swal("易百加提醒您", "注册成功,请登录", "success");
                        $state.go("main.logo", {"signin": 1});
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        };
//登录
        $scope.bd_in = function () {
            $http
                .post('/bind.json', {
                    unionid: $scope.u_msg.unionid,
                    mobile: $scope.bd_phone,
                    password: $scope.bd_pwd
                })
                .success(function (data) {
                    if (data.status) {
                        swal("易百加提醒您", "绑定成功", "success");
                        $state.go("main.home");
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        $scope.sign_in = function () {
            $http
                .post('/login.json', {mobile: $scope.signin_phone, password: $scope.signin_pwd})
                .success(function (data) {
                    if (data.status) {
                        swal("易百加提醒您", "登录成功", "success");
                        $state.go("main.home");
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        };

//重置密码
// 1.获取验证码
        $scope.reset_yzm = function () {
            $http
                .post('/verification.json', {mobile: $scope.reset_phone, reset: 1})
                .success(function (data) {
                    if (data.status) {
                        swal("易百加提醒您", "验证码已发送", "success");
                        $scope.time = 60;
                        $scope.timmer = $interval(toDo, 1000);

                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        };
// 2.重置操作
        $scope.reset_submit = function () {
            $http
                .post('/reset.json ', {
                    mobile: $scope.reset_phone,
                    password: $scope.reset_pwd,
                    verification: $scope.reset_verification
                })
                .success(function (data) {
                    if (data.status) {
                        swal("易百加提醒您", "密码重置成功", "success");
                        $state.go("main.logo", {"signin": 1});
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        };

//绑定微信账号
        $scope.choseChart = function () {
            if (!GetRequest().code) {
                var redirect_url = window.location.href.substr(0, window.location.href.indexOf('?'));
                var obj = new WxLogin({
                    id: "login_container",
                    appid: "wx919b0ced8252ca0e",
                    scope: "snsapi_login",
                    redirect_uri: encodeURIComponent(redirect_url)
                });
                $scope.logn_wei_show = 1;
            } else {

            }

        }

        function GetRequest() {
            var url = location.search; //获取url中"?"符后的字串
            var theRequest = new Object();
            if (url.indexOf("?") != -1) {
                var str = url.substr(1);
                strs = str.split("&");
                for (var i = 0; i < strs.length; i++) {
                    theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
                }
            }
            return theRequest;
        }


    })
;
