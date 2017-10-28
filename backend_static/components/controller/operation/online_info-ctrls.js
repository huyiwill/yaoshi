angular
    .module('ohapp')
    .controller('online_infoCtrl', function online_infoCtrl($scope, $injector, $rootScope, ngDialog,$stateParams) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        $rootScope.root="/online_info.html";
        $http
            .get('/online/info.json', {params: {id: $stateParams.id}})
            .success(function (data) {
                if (data.status) {
                    $scope.updates = data.data;
                } else {
                    swal("OMG!", data.message, "error");
                }
            });


        $scope.lead = function (page) {
            //若有数据传递，将传递过来的变量push进对象中。。
            $http
                .get(' /online/answer/list.json', {params: {subject_id: $stateParams.id,page:page}})
                .success(function (data) {
                    if (data.status) {
                        $scope.list = data.data;
                        $(".tcdPageCode").createPage({
                            pageCount: data.total,
                            current: data.current,
                            backFn: function (current) {
                                nexts(current);
                            }
                        });
                    } else {
                        swal("OMG!", data.message, "error")
                    }
                });
        };
        $scope.lead();
        //翻页
        function nexts(p) {
            $('#page').val(p);
            $scope.lead(p);
        };
        
        
        //回复
        $scope.admin_ans = function (id,pid) {
            $scope.submitted = false;
            $scope.updates = {};
            $scope.answer_id = id;
            $scope.pid = pid;


            ngDialog.open({template: 'backend_static/views/popup/p-online-info.html', scope: $scope});

        }
        $scope.chose = function (form) {
            if(form.$valid){
                $http
                    .post(' /online/comment/add.json',{subject_id: $stateParams.id,answer_id:$scope.answer_id,pid:$scope.pid,content:$scope.updates.content})
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "回复成功！", "success");
                            ngDialog.close();
                            $scope.lead();
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            }else{
                $scope.submitted = true;
            }
        }
        
        //答案禁用
        $scope.admin_jin = function (id,s) {
            $http
                .post('/online/answer/status.json', {id: id,status:s})
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "禁用状态已更改！", "success");
                        $scope.lead();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }

        //评论禁用
        $scope.ping_jin = function (id,s) {
            $http
                .post('/online/comment/status.json', {id: id,status:s})
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "禁用状态已更改！", "success");
                        $scope.lead();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        
        //答案删除
        $scope.admin_del = function (id) {
            $http
                .post('/online/answer/del.json',{id:id})
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "删除成功！", "success");
                        $scope.lead();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }

        //评论删除
        $scope.ping_del = function (id) {
            $http
                .post('/online/comment/del.json',{id:id})
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "删除成功！", "success");
                        $scope.lead();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        


    });
