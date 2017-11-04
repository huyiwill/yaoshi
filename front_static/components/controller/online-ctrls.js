angular
    .module( 'ohapp' )
    .controller( 'onlineCtrl', function onlineCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');

        //初始化数据
        // 显示加载列表
        $scope.chose_this = 1;

        $scope.order = 'create_time desc'

        $scope.lead = function (page) {
            $scope.chose_this = 1;
            $scope.user_answer = undefined;
            //若有数据传递，将传递过来的变量push进对象中。。
            var p={page:page,topic_type:$scope.topic_type,order:$scope.order,therapeutic:$scope.therapeutic};

            $http
                .get('/online/list.json', {params: p})
                .success(function (data) {
                    if (data.status) {
                        $scope.list = data.data;
                        $scope.total_page = Number(data.total)
                        $(".tcdPageCode").createPage({
                            pageCount: Number(data.total),
                            current: Number(data.current),
                            backFn: function (current) {
                                next(current)
                            }
                        });
                    } else {
                        swal("OMG!", data.message, "error")
                    }
                });
        };
        $scope.lead();

        //搜索
        $scope.chose_topic = function (i) {
            $scope.chose_this = 1;
            $scope.topic_type = i;
            $scope.lead();
        }

        //治疗领域
        $scope.to_field = function (i) {
            $scope.therapeutic = i;
            $scope.lead();
        }
        //热度
        $scope.check_hot = function (i) {
            if(i==1){
                $scope.order = 'create_time desc';
            }else{
                $scope.order = 'weight';
            }
            $scope.lead();
        }
        //翻页
        function next(p) {
            $('#page').val(p);
            $scope.lead(p);
        };

        //病例详情
        $scope.par_detail = function (i) {
            $scope.chose_this = 2;
            $scope.chose_id = i;
            $scope.synchro = 1;
            $scope.synchro2 = 1;
            $http
                .get('/online/info.json', {params: {id:i}})
                .success(function (data) {
                    if (data.status) {
                        $scope.user_info = data.data;
                    } else {
                        swal("OMG!", data.message, "error")
                    }
                });
        }

        //提交答案
        $scope.end_ti = function (user_answer) {
            $scope.user_answer = user_answer;
            if(user_answer==undefined||user_answer==''||user_answer.length<10){
                swal("OMG!",'请继续输入内容，不得少于10字', "error");
                return;
            }
            $http
                .post('/online/add.json',{subject_id:$scope.chose_id,user_answer:user_answer,synchro:$scope.synchro})
                .success(function (data) {
                    if (data.status) {
                        swal("药学工具网提醒您", "答案提交成功","success");
                        $scope.chose_this = 3;
                        $scope.leads();
                        $http
                            .get('/online/info.json', {params: {id:$scope.chose_id}})
                            .success(function (data) {
                                if (data.status) {
                                    $scope.user_info = data.data;
                                } else {
                                    swal("OMG!", data.message, "error")
                                }
                            });
                    } else {
                        swal("OMG!", data.message, "error")
                    }
                });
        }

        //查看解析
        $scope.see_analysis = function(){

            $scope.agree = 1;
            //同意查看解析，并且积分余额充足
            if ($scope.agree) {
                $http
                    .post('/purpose/check/three.json', {subject_id: $scope.chose_id})
                    .success(function (data) {
                        if (data.status) {
                            $scope.analy = data.data.analysis.analysis;
                            $scope.see_analysis = 1;
                        } else {
                            swal({
                                title: "药学工具网提醒您",
                                text: data.message,
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "确定购买！",
                                cancelButtonText: "取消购买！",
                                closeOnConfirm: false,
                                closeOnCancel: false
                            }, function (isConfirm) {
                                if (isConfirm) {
                                    $state.go('main.me.my_points');
                                    swal.close();
                                } else {
                                    swal.close();
                                }
                            });
                        }
                    });
            }
        }

        $scope.leads = function (page) {
            //若有数据传递，将传递过来的变量push进对象中。。
            $http
                .get(' /online/answer/list.json', {params: {subject_id: $scope.chose_id,page:page}})
                .success(function (data) {
                    if (data.status) {
                        $scope.lists = data.data;
                        $scope.total_pages = Number(data.total)
                        $(".tcdPageCodes").createPage({
                            pageCount: Number(data.total),
                            current: Number(data.current),
                            backFn: function (current) {
                                nexts(current)
                            }
                        });
                    } else {
                        swal("OMG!", data.message, "error")
                    }
                });
        };
        //翻页
        function nexts(p) {
            $('#page').val(p);
            $scope.leads(p);
        };


        $scope.chsoe_synchro = function () {
            if($scope.synchro==1){
                $scope.synchro = 2;
            }else{
                $scope.synchro = 1;
            }
        }
        $scope.chsoe_synchro2 = function () {
            if($scope.synchro2==1){
                $scope.synchro2 = 2;
            }else{
                $scope.synchro2 = 1;
            }
        }


        //关闭弹窗
        $scope.close_proup = function () {
            $scope.ping_tan = 0;
        }
        //评论
        $scope.pings = function(pid){
            $scope.pid = pid;
            $scope.ping_tan = 1;
            $scope.ping_text={};
            $scope.synchro2 = 1;

        }

        //评论提交
        $scope.end_chose_group = function (content) {
            $http
                .post('/online/comment/add.json', {subject_id: $scope.chose_id,pid:0,answer_id:$scope.pid,content:content,synchro:$scope.synchro2})
                .success(function (data) {
                    if (data.status) {
                        swal("药学工具网提醒您", "您的评论已提交","success");
                        $scope.close_proup();
                        $scope.leads();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        
        //点赞
        $scope.add_zan = function (id,type) {
            $http
                .post('/online/thumbs/up.json', {subject_id: $scope.chose_id,relation_id:id,type:type})
                .success(function (data) {
                    if (data.status) {
                        $scope.close_proup();
                        $scope.leads();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }





    });
