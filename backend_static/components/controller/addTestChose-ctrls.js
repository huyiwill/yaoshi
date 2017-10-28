angular
    .module('ohapp')
    .controller('addTestChoseCtrl', function addTestChoseCtrl($scope, $injector, $rootScope, ngDialog, $stateParams) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');


        $scope.price_arr=['未知','禁用','启用'];
        $scope.role_arr = ['未知','其他','药师','医生','护士','学生'];
        $scope.state_id = $stateParams.id;
        $scope.state_type = $stateParams.chose_type;


        $scope.lead = function (page, order, filter_type, id) {

            //若有数据传递，将传递过来的变量push进对象中。。
            var p = {page: page, order: order, topic_type: $stateParams.chose_type};
            var filter = filter_type;
            p[filter] = id;

            $http
                .get('/subject/list.json', {params: p})
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


        //搜索
        $scope.soso = function () {
            $scope.soso_end_type = $("#filter_type").val();
            $scope.soso_end_text = $("#filter_text").val();
            $scope.lead(1, '', $scope.soso_end_type, $scope.soso_end_text);
        }

        //翻页
        function next(p) {
            $('#page').val(p);
            $scope.lead(p, '', $scope.soso_end_type, $scope.soso_end_text);
        };
        //排序
        $(".tr_order").on('click', ".fa-sort-amount-asc", function () {
            $(this).attr('class', 'fa fa-fw fa-sort-amount-desc');
            var order = $(this).data('order') + ' asc';
            $scope.lead('', order, $scope.soso_end_type, $scope.soso_end_text);
        });
        $(".tr_order").on('click', ".fa-sort-amount-desc", function () {
            $(this).attr('class', 'fa fa-fw fa-sort-amount-asc');
            var order = $(this).data('order') + ' desc';
            $scope.lead('', order, $scope.soso_end_type, $scope.soso_end_text);
        });


        //选中逻辑判定
        $scope.cheackTotal = 0;
        $scope.selectAll = false;
        $scope.all = function (m) {
            for (var i = 0; i < $scope.list.length; i++) {
                if (m === true) {
                    $scope.list[i].state = true;
                } else {
                    $scope.list[i].state = false;
                }
            }
            $scope.total();
        };
        //计算选中的题数
        $scope.total = function () {
            $scope.cheackTotal = 0;
            for (var i = 0; i < $scope.list.length; i++) {
                if ($scope.list[i].state == true) {
                    $scope.cheackTotal += 1;
                }
            }
        }
        //提交选中
        $scope.chose_ti = function () {
            //获取选中的id
            $scope.subjects = [];
            for (var i = 0; i < $scope.list.length; i++) {
                if ($scope.list[i].state == true) {
                    $scope.subjects.push($scope.list[i].id);
                }
            }
            $http
                .post('/ts/choice.json', {subjects:$scope.subjects,test_id:$scope.state_id,score:$scope.selectScole})
                .success(function (data) {
                    if (data.status) {
                        console.log(data);
                        history.back(-1);
                    } else {
                        swal("OMG!", data.message, "error")
                    }
                });

        }


    });
