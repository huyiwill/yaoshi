angular
    .module('ohapp')
    .controller('test_yearCtrl', function test_yearCtrl($scope, $injector, $rootScope, ngDialog,$stateParams) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');


        //定义变量数组
        $scope.arr_is_discount = ['','打折','不打折'];
        $scope.arr_is_show = ['','不显示','显示'];
        $scope.text_names = $stateParams.name;
        $scope.text_id = $stateParams.id;

        $scope.lead = function (page, order, filter_type, id) {

            //若有数据传递，将传递过来的变量push进对象中。。
            var p={page:page,order: order};
            var filter = filter_type;
            p[filter] = id;

            $http
                .get('/exercises/preview.json',{params: {id: $stateParams.id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.list = data.data;
                        $(".tcdPageCode").createPage({
                            pageCount: data.total,
                            current: data.current,
                            backFn: function (current) {
                                next(current);
                            }
                        });
                    } else {
                        swal("OMG!", data.message, "error")
                    }
                });
        };
        $scope.lead();
        $scope.edit = function(id,chose){
            $scope.updates={};
            ngDialog.open({template: 'backend_static/views/popup/p-test_year.html', scope: $scope});
            $http
                .get('/es/info.json', {params: {id: id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.topic_type = chose;
                        $scope.updates = data.data;
                        console.log($scope.updates);
                        $scope.chose_this = chose;

                        if($scope.topic_type==2){
                            for(var i=0;i<$scope.updates.choice.length;i++){
                                if($scope.updates.right_key.indexOf($scope.updates.choice[i].key)!=-1){
                                    $scope.updates.choice[i].c = true;
                                }else{
                                    $scope.updates.choice[i].c = false;
                                }
                            }
                        }
                        if($scope.topic_type==4){
                            $("#right_key4 .froala-element").eq(0)[0].innerHTML = $scope.updates.right_key;
                        }else if($scope.topic_type==5){
                            $("#right_key5 .froala-element").eq(0)[0].innerHTML = $scope.updates.right_key;
                        }else if($scope.topic_type==6){
                            $("#right_key6 .froala-element").eq(0)[0].innerHTML = $scope.updates.right_key;
                        }
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });

        }


        $rootScope.$on('ngDialog.opened', function (e, $dialog) {
            $('#right_key4,#right_key5,#right_key6').editable({
                inlineMode: false,
                alwaysBlank: true,
                language: "zh_cn",
                direction: "ltr",
                autosave: true,
                autosaveInterval: 2500,
                saveParams: {postId: "123"},
                spellcheck: true,
                plainPaste: true,
                enableScript: false
            });
            // if($scope.topic_type==4){
            //     $("#right_key4 .froala-element").eq(0)[0].innerHTML = $scope.updates.right_key;
            // }else if($scope.topic_type==5){
            //     $("#right_key5 .froala-element").eq(0)[0].innerHTML = $scope.updates.right_key;
            // }else if($scope.topic_type==6){
            //     $("#right_key6 .froala-element").eq(0)[0].innerHTML = $scope.updates.right_key;
            // }
        });


        //提交表单操作、判断是否为新增和修改
        $scope.chose = function (form) {
            if($scope.topic_type==2){
                $scope.updates.right_key='';

                var j=0;
                for(var i = 0;i<$scope.updates.choice.length;i++){
                    console.log(1)
                    if($scope.updates.choice[i].c&&j==0){
                        $scope.updates.right_key = $scope.updates.choice[i].key;
                        j++;
                    }else if($scope.updates.choice[i].c){
                        $scope.updates.right_key =$scope.updates.right_key+","+$scope.updates.choice[i].key;
                    }
                    delete $scope.updates.choice[i].c;
                }
            }else if($scope.topic_type==4){
                $scope.updates.right_key = $("#right_key4 .froala-element").eq(0)[0].innerHTML;
            }else if($scope.topic_type==5){
                $scope.updates.right_key = $("#right_key5 .froala-element").eq(0)[0].innerHTML;
            }else if($scope.topic_type==6){
                $scope.updates.right_key = $("#right_key6 .froala-element").eq(0)[0].innerHTML;
            }
                $http
                    .post('/es/update.json', {
                        topic_type:$scope.topic_type,
                        id:$scope.updates.id,
                        name:$scope.updates.name,
                        choice:$scope.updates.choice,
                        right_key:$scope.updates.right_key,
                        score:$scope.updates.score,
                        pharmacy_id:$scope.updates.pharmacy_id,
                        photo:$scope.updates.heads

                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "修改成功！", "success");
                            ngDialog.close();
                            $scope.lead();
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });

        };

        //删除
        $scope.delete = function (id) {
            swal({
                title: "确定删除吗？",
                text: "删除后不可恢复该操作！",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确定删除！",
                cancelButtonText: "取消删除！",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function (isConfirm) {
                if (isConfirm) {
                    $http
                        .post('/es/del.json', {id: id})
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "删除成功！", "success");
                                $scope.lead();
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                }else{
                    swal.close();
                }
            });
        }



        //非公共部分
        function ajaxupload_photo(data) {
            photoMsg = data;
            $scope.updates.heads = data;
            $("#head").attr('src', data);
        }

        $scope.photo = function () {
            $('#photo').localResizeIMG({
                width: 200,
                height: 200,
                quality: 1,
                success: function (result) {
                    img = new Image;
                    img.src = result.base64;
                    ajaxupload_photo(img.src);
                }
            });
        }

        //交换位置
        $scope.chose_change = function (i,index,type) {
            if(type==1&&index+1<$scope.list.subject_one.length){
                $scope.change_two = $scope.list.subject_one[index+1].id;
            }else if(type==2&&index+1<$scope.list.subject_two.length){
                $scope.change_two = $scope.list.subject_two[index+1].id;
            }else if(type==4&&index+1<$scope.list.subject_.length){
                $scope.change_two = $scope.list.subject_four[index+1].id;
            }else if(type==5&&index+1<$scope.list.subject_two.length){
                $scope.change_two = $scope.list.subject_five[index+1].id;
            }else if(type==6&&index+1<$scope.list.subject_two.length){
                $scope.change_two = $scope.list.subject_six[index+1].id;
            }else{
                return;
            }
            $http
                .post('/es/order.json', {change_one: i,change_two:$scope.change_two})
                .success(function (data) {
                    if (data.status) {
                        $scope.lead();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }

        // 获取相关下拉菜单信息
        // $http
        //     .get('/tg/drop.json')
        //     .success(function (data) {
        //         if (data.status) {
        //             $scope.roles = data.data;
        //         } else {
        //             swal("OMG!", data.message, "error");
        //         }
        //     });


    });
