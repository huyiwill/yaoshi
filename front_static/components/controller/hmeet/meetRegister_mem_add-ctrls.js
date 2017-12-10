angular
  .module('ohapp')
  .controller('meetRegister_mem_addCtrl', function meetRegister_mem_addCtrl($scope, $injector, $rootScope){
    var $http = $injector.get('$http');
    var $location = $injector.get('$location');
    var $state = $injector.get('$state');
    var $timeout = $injector.get('$timeout');
    var $config = $injector.get('$config');
    var $session = $injector.get('$session');

    $scope.submitted = false;
    $scope.updates = {};
    $scope.updates.is_credit = 2;

    $scope.next = function(form){
      if(form.$valid){
        $http
          .post('/meetingdata/memReg.json', {
            phone           : $scope.phone,
            name            : $scope.name_english,
            certificate_type: $scope.certificate_type,
            sex             : $scope.sex,
            province_id     : $scope.province_id,
            city_id         : $scope.city_id,
            address         : $scope.address,
            venue_name      : $scope.venue_name,
          })
          .success(function(data){
            if(data.status){
              swal("干得漂亮！", "新增成功！", "success");
              //$state.go('main.bs.meetMore', {id: data.data})
            }else{
              swal("OMG!", data.message, "error");
            }
          });
      }else{
        $scope.submitted = true;
      }
    };

    $timeout(function(){
      $('#enroll_start').jHsDate({
        maxDate : '2025-12-31',
        format  : 'yyyy-MM-dd hh时mm分',
        callBack: function(data){
          $scope.updates.enroll_start = $('#enroll_start').val();
          $scope.$apply();
        }
      });
      $('#enroll_end').jHsDate({
        maxDate : '2025-12-31',
        format  : 'yyyy-MM-dd hh时mm分',
        callBack: function(){
          $scope.updates.enroll_end = $('#enroll_end').val();
          $scope.$apply();
        }
      });

      $('#attend_time').jHsDate({
        maxDate : '2025-12-31',
        format  : 'yyyy-MM-dd hh时mm分',
        callBack: function(){
          $scope.updates.attend_time = $('#attend_time').val();
          $scope.$apply();
        }
      });

      $('#time_start').jHsDate({
        maxDate : '2025-12-31',
        format  : 'yyyy-MM-dd hh时mm分',
        callBack: function(){
          $scope.updates.time_start = $('#time_start').val();
          $scope.$apply();
        }
      });

      $('#time_end').jHsDate({
        maxDate : '2025-12-31',
        format  : 'yyyy-MM-dd hh时mm分',
        callBack: function(){
          $scope.updates.time_end = $('#time_end').val();
          $scope.$apply();
        }
      });
    }, 1000);

    //上传图片
    function ajaxupload_banner(data){
      photoMsg = data;
      $scope.updates.banner = data;
      $scope.$apply();
    }

    function ajaxupload_icon(data){
      photoMsg = data;
      $scope.updates.icon = data;
      $scope.$apply();
    }

    $scope.banner = function(){
      $('#banner').localResizeIMG({
        width  : 200,
        height : 200,
        quality: 1,
        success: function(result){
          img = new Image;
          img.src = result.base64;
          ajaxupload_banner(img.src);
        }
      });
    };

    $scope.icon = function(){
      $('#icon').localResizeIMG({
        width  : 200,
        height : 200,
        quality: 1,
        success: function(result){
          img = new Image;
          img.src = result.base64;
          ajaxupload_icon(img.src);
        }
      });
    };

    //取消
    $scope.back = function(){
      history.back();
    };

    //获取下拉省份信息
    $http
      .get('/region/province/drop.json')
      .success(function(data){
        $scope.provinces = data.data;
      });

    $scope.chose_this = function(i){
      $http
        .get('/region/city/drop.json', {params: {province_id: i}})
        .success(function(data){
          $scope.cities = data.data;
        });
    }

  });
