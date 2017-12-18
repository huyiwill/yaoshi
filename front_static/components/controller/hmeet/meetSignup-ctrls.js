angular
  .module('ohapp')
  .controller('meetSignupCtrl', function meetSignupCtrl($scope, $injector, $rootScope){
    var $http = $injector.get('$http');
    var $location = $injector.get('$location');
    var $state = $injector.get('$state');
    var $timeout = $injector.get('$timeout');
    var $config = $injector.get('$config');
    var $session = $injector.get('$session');

    $rootScope.root = "/meeting.html";
    $scope.submitted = false;
    $scope.chose_type = 1;
    $scope.chose_this = function(t){
      if($scope.chose_type == t){
        return;
      }
      $scope.chose_type = t;

      //persion  mem reg list
      if($scope.chose_type == 1){

        $scope.lead = function(page, order, filter_type, id){

          //若有数据传递，将传递过来的变量push进对象中。。
          var p = {
            page      : page,
            order     : order,
            topic_type: 1
          };
          var filter = filter_type;
          p[filter] = id;

          $http
            .get('/meetingdata/getMemRegList.json', {params: p})
            .success(function(data){
              if(data.status){
                $scope.cheackTotal = 0;
                $scope.list = data.data;
                $(".tcdPageCode").createPage({
                  pageCount: data.total,
                  current  : data.current,
                  backFn   : function(current){
                    next(current);
                  }
                });
              }else{
                swal("OMG!", data.message, "error")
              }
            });
        };
        $scope.lead();

      }

      //organization reg list
      if($scope.chose_type == 2){

      }
    };
  });
