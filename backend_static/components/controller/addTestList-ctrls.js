angular
    .module('ohapp')
    .controller('addTestListCtrl', function addTestListCtrl($scope, $injector, $rootScope, ngDialog, $stateParams) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        $scope.chose_type = $stateParams.chose_type;
        $scope.types = "add";
        $scope.updates = {};
        $scope.submitted = false;
        $scope.show_therapeutic = 0;
        $scope.arr_chose_type = ['', '单选题', '多选题', '填空题', '处方审核题', '用药交代', '问答', '材料题'];

        //获取药学分类
        $http
            .get('/category/options.json')
            .success(function (data) {
                if (data.status) {
                    $scope.category = '<option value="">请选择</option>'+data.data;
                    $scope.changed();
                } else {
                    swal("OMG!", data.message, "error");
                }
            });


        //测试数据
        $scope.id_an = true;
        $scope.id_key = true;
        $('div[name="analysis"]').on('editable.contentChanged', function (e, editor) {

            if ($(this).find($(".froala-element")).eq(0)[0].innerHTML != '<p><br></p>') {
                $scope.id_an = false;
            } else {
                $scope.id_an = true;
            }
            $scope.$apply();
        });
        $('div[name="right_key"]').on('editable.contentChanged', function (e, editor) {
            if ($(this).find($(".froala-element")).eq(0)[0].innerHTML != '<p><br></p>') {
                $scope.id_key = false;
            } else {
                $scope.id_key = true;
            }
            $scope.$apply();
        });

        //提交表单操作、判断是否为新增和修改
        $scope.chose = function (form) {
            if ($scope.chose_type == 1) {
                if (form && !$scope.id_an) {
                    if ($scope.types == "edit") {
                        $http
                            .post('/ts/update.json', {
                                id: $scope.updates.id,

                            })
                            .success(function (data) {
                                if (data.status) {
                                    swal("干得漂亮！", "修改成功！", "success");
                                    ngDialog.close();
                                    history.go(-2);
                                } else {
                                    swal("OMG!", data.message, "error");
                                }
                            });
                    } else if ($scope.types == "add") {
                        $scope.updates.choice = [];
                        //拼接chose
                        $scope.updates.choice.push({key: "A", value: $scope.updates.a_ans}, {
                            key: "B",
                            value: $scope.updates.b_ans
                        }, {key: "C", value: $scope.updates.c_ans}, {key: "D", value: $scope.updates.d_ans});
                        $http
                            .post('/ts/add.json', {
                                topic_type: $scope.chose_type,
                                test_id: $stateParams.id,
                                name: $scope.updates.name,
                                choice: $scope.updates.choice,
                                right_key: $scope.updates.right_key,
                                score: $scope.updates.score,
                                role: $scope.updates.role,
                                degree: $scope.updates.degree,
                                pharmacy_id: $scope.updates.pharmacy_id,
                                analysis: $("#analysis1 .froala-element").eq(0)[0].innerHTML,
                                price: $scope.updates.price,
                                is_price: $scope.updates.is_price,
                                post: $scope.updates.post
                            })
                            .success(function (data) {
                                if (data.status) {
                                    swal("干得漂亮！", "新增成功！", "success");
                                    ngDialog.close();
                                    history.go(-2);
                                } else {
                                    swal("OMG!", data.message, "error");
                                }
                            });
                    }
                } else {
                    $scope.submitted = true;
                }
            } else if ($scope.chose_type == 2) {
                if (form && !$scope.id_an && ($scope.updates.right_key4 || $scope.updates.right_key3 || $scope.updates.right_key2 || $scope.updates.right_key1)) {
                    $scope.updates.right_key = '';
                    var arr = ["", "A", "B", "C", "D"];
                    var j = 1;
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
                                    history.go(-2);
                                } else {
                                    swal("OMG!", data.message, "error");
                                }
                            });
                    } else if ($scope.types == "add") {
                        $scope.updates.choice = [];
                        //拼接chose
                        $scope.updates.choice.push({key: "A", value: $scope.updates.a_ans}, {
                            key: "B",
                            value: $scope.updates.b_ans
                        }, {key: "C", value: $scope.updates.c_ans}, {key: "D", value: $scope.updates.d_ans});
                        $http
                            .post('/ts/add.json', {
                                topic_type: $scope.chose_type,
                                test_id: $stateParams.id,
                                name: $scope.updates.name,
                                choice: $scope.updates.choice,
                                right_key: $scope.updates.right_key,
                                score: $scope.updates.score,
                                role: $scope.updates.role,
                                degree: $scope.updates.degree,
                                pharmacy_id: $scope.updates.pharmacy_id,
                                analysis: $("#analysis2 .froala-element").eq(0)[0].innerHTML,
                                price: $scope.updates.price,
                                is_price: $scope.updates.is_price,
                                post: $scope.updates.post
                            })
                            .success(function (data) {
                                if (data.status) {
                                    swal("干得漂亮！", "新增成功！", "success");
                                    ngDialog.close();
                                    history.go(-2);
                                } else {
                                    swal("OMG!", data.message, "error");
                                }
                            });
                    }
                } else {
                    $scope.submitted = true;
                }
            } else if ($scope.chose_type == 4 || $scope.chose_type == 5) {
                if (form && $scope.updates.photo != undefined && !$scope.id_an && !$scope.id_key) {
                    if ($scope.types == "edit") {

                        $http
                            .post('/ts/update.json', {
                                id: $scope.updates.id,

                            })
                            .success(function (data) {
                                if (data.status) {
                                    swal("干得漂亮！", "修改成功！", "success");
                                    ngDialog.close();
                                    history.go(-2);
                                } else {
                                    swal("OMG!", data.message, "error");
                                }
                            });
                    } else if ($scope.types == "add") {
                        $http
                            .post('/ts/add.json', {
                                topic_type: $scope.chose_type,
                                test_id: $stateParams.id,
                                name: $scope.updates.name,
                                right_key: $("#right_key4 .froala-element").eq(0)[0].innerHTML,
                                score: $scope.updates.score,
                                role: $scope.updates.role,
                                degree: $scope.updates.degree,
                                pharmacy_id: $scope.updates.pharmacy_id,
                                analysis: $("#analysis4 .froala-element").eq(0)[0].innerHTML,
                                price: $scope.updates.price,
                                is_price: $scope.updates.is_price,
                                post: $scope.updates.post,
                                photo: $scope.updates.photo,
                                sex: $scope.updates.sex,
                                age: $scope.updates.age,
                                department: $scope.updates.department,
                                clinical_diagnosis: $("#clinical_diagnosis").val()

                            })
                            .success(function (data) {
                                if (data.status) {
                                    swal("干得漂亮！", "新增成功！", "success");
                                    ngDialog.close();
                                    history.go(-2);

                                } else {
                                    swal("OMG!", data.message, "error");
                                }
                            });
                    }
                } else {
                    $scope.submitted = true;
                }
            } else if ($scope.chose_type == 6) {
                if (form && !$scope.id_an && !$scope.id_key) {
                    if ($scope.types == "edit") {

                        $http
                            .post('/ts/update.json', {
                                id: $scope.updates.id,

                            })
                            .success(function (data) {
                                if (data.status) {
                                    swal("干得漂亮！", "修改成功！", "success");
                                    ngDialog.close();
                                    history.go(-2);

                                } else {
                                    swal("OMG!", data.message, "error");
                                }
                            });
                    } else if ($scope.types == "add") {
                        $http
                            .post('/ts/add.json', {
                                topic_type: $scope.chose_type,
                                test_id: $stateParams.id,
                                name: $scope.updates.name,
                                right_key: $("#right_key6 .froala-element").eq(0)[0].innerHTML,
                                score: $scope.updates.score,
                                role: $scope.updates.role,
                                degree: $scope.updates.degree,
                                pharmacy_id: $scope.updates.pharmacy_id,
                                analysis: $("#analysis6 .froala-element").eq(0)[0].innerHTML,
                                price: $scope.updates.price,
                                is_price: $scope.updates.is_price

                            })
                            .success(function (data) {
                                if (data.status) {
                                    swal("干得漂亮！", "新增成功！", "success");
                                    ngDialog.close();
                                    history.go(-2);

                                } else {
                                    swal("OMG!", data.message, "error");
                                }
                            });
                    }
                } else {
                    $scope.submitted = true;
                }
            }


        };


        function ajaxupload_photo(data) {
            photoMsg = data;
            $scope.updates.photo = data;
            $("#head").attr('src', data);
            $scope.$apply();
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
        //判断是否为药理
        $scope.changed = function () {
            if($("#pharmacy_id").find("option:selected").attr("data-name")=='药理'){
                $scope.show_therapeutic = 1;
            }else{
                $scope.show_therapeutic = 0;
            }
        }



    });
