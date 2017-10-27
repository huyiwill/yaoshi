angular
    .module('ohapp')
    .controller('addTestCtrl', function addTestCtrl($scope, $injector, $rootScope, ngDialog,$stateParams) {
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
            $scope.updates={};

        };

        $scope.types = "add";
        $scope.updates={};

        //获取药学分类
        $http
            .get('/category/options.json')
            .success(function (data) {
                if (data.status) {
                    $scope.category = data.data;
                } else {
                    swal("OMG!", data.message, "error");
                }
            });

        //提交表单操作、判断是否为新增和修改
        $scope.chose = function () {
            if($scope.chose_type == 1){
                if ($scope.types == "edit") {
                    $http
                        .post('/ts/update.json', {
                            id: $scope.updates.id,

                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "修改成功！", "success");
                                ngDialog.close();
                                history.back(-1);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                } else if ($scope.types == "add") {
                    $scope.updates.choice=[];
                    //拼接chose
                    $scope.updates.choice.push({key:"A",value:$scope.updates.a_ans},{key:"B",value:$scope.updates.b_ans},{key:"C",value:$scope.updates.c_ans},{key:"D",value:$scope.updates.d_ans});
                    $http
                        .post('/ts/add.json', {
                            topic_type:$scope.chose_type,
                            test_id: $stateParams.id,
                            name:$scope.updates.name,
                            choice:$scope.updates.choice,
                            right_key: $scope.updates.right_key,
                            score: $scope.updates.score,
                            role: $scope.updates.role,
                            degree: $scope.updates.degree,
                            pharmacy_id: $("#pharmacy_id").val(),
                            analysis: $("#analysis").val(),
                            price: $scope.updates.price,
                            is_price: $scope.updates.is_price,
                            post: $scope.updates.post
                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "新增成功！", "success");
                                ngDialog.close();
                                history.back(-1);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                }
            }else if($scope.chose_type == 2){
                $scope.updates.right_key='';
                var arr=["","A","B","C","D"];
                var j=1;
                for (var i = 1; i < 5; i++) {
                    if ($(".right_key" + i).is(':checked')) {
                        if (j == 1) {
                            $scope.updates.right_key = arr[i];
                        } else {
                            $scope.updates.right_key = $scope.updates.right_key + "," + arr[i];
                        }
                        j++;
                    }
                }
                if ($scope.types == "edit") {

                    $http
                        .post('/ts/update.json', {
                            id: $scope.updates.id,

                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "修改成功！", "success");
                                ngDialog.close();
                                history.back(-1);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                } else if ($scope.types == "add") {
                    $scope.updates.choice=[];
                    //拼接chose
                    $scope.updates.choice.push({key:"A",value:$scope.updates.a_ans},{key:"B",value:$scope.updates.b_ans},{key:"C",value:$scope.updates.c_ans},{key:"D",value:$scope.updates.d_ans});
                    $http
                        .post('/ts/add.json', {
                            topic_type:$scope.chose_type,
                            test_id: $stateParams.id,
                            name:$scope.updates.name,
                            choice:$scope.updates.choice,
                            right_key: $scope.updates.right_key,
                            score: $scope.updates.score,
                            role: $scope.updates.role,
                            degree: $scope.updates.degree,
                            pharmacy_id: $("#pharmacy_id").val(),
                            analysis: $("#analysis").val(),
                            price: $scope.updates.price,
                            is_price: $scope.updates.is_price,
                            post: $scope.updates.post
                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "新增成功！", "success");
                                ngDialog.close();
                                history.back(-1);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                }
            }else if($scope.chose_type == 4||$scope.chose_type == 5){
                if ($scope.types == "edit") {

                    $http
                        .post('/ts/update.json', {
                            id: $scope.updates.id,

                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "修改成功！", "success");
                                ngDialog.close();
                                history.back(-1);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                } else if ($scope.types == "add") {
                    $http
                        .post('/ts/add.json', {
                            topic_type:$scope.chose_type,
                            questions_id: $stateParams.id,
                            name:$scope.updates.name,
                            right_key: $("#right_key").val(),
                            score: $scope.updates.score,
                            role: $scope.updates.role,
                            degree: $scope.updates.degree,
                            pharmacy_id: $("#pharmacy_id").val(),
                            analysis: $("#analysis").val(),
                            price: $scope.updates.price,
                            is_price: $scope.updates.is_price,
                            post: $scope.updates.post,
                            photo:$scope.updates.heads,
                            sex:$scope.updates.sex,
                            age:$scope.updates.age,
                            department:$scope.updates.department,
                            clinical_diagnosis:$("#clinical_diagnosis").val()

                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "新增成功！", "success");
                                ngDialog.close();
                                history.back(-1);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                }
            }else if($scope.chose_type == 6){
                if ($scope.types == "edit") {

                    $http
                        .post('/ts/update.json', {
                            id: $scope.updates.id,

                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "修改成功！", "success");
                                ngDialog.close();
                                history.back(-1);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                } else if ($scope.types == "add") {
                    $http
                        .post('/ts/add.json', {
                            topic_type:$scope.chose_type,
                            questions_id: $stateParams.id,
                            name:$scope.updates.name,
                            right_key: $("#right_key").val(),
                            score: $scope.updates.score,
                            role: $scope.updates.role,
                            degree: $scope.updates.degree,
                            pharmacy_id: $("#pharmacy_id").val(),
                            analysis: $("#analysis").val(),
                            price: $scope.updates.price,
                            is_price: $scope.updates.is_price

                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "新增成功！", "success");
                                ngDialog.close();
                                history.back(-1);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                }
            }


        };




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

    });
