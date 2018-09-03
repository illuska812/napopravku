
var mainApp = angular.module('mainApp', []);

mainApp.controller('doctorsListController', [ "$scope", "$http",
function ($scope, $http) {
    $scope.doctorsData = [];
    $scope.currentDoctor = null;
    $scope.step = 0;

    $scope.init = function() {
        $scope.getDoctorsData();
    };
    
    $scope.getDoctorsData = function() {
        var params = {
        };
        $http.get('/doctor/get-doctors-list', {
            params: params
        }).success(function(data) {
            if (data.success) {
                $scope.step = 1;
                $scope.doctorsData = data.list;
            } else {
//                
            }
        }).
        error(function() {
//                

        });
        
    };
    
    $scope.goToList = function(){
        $scope.currentDoctor = null;
        $scope.step = 1;
    };
    
    $scope.startTicketing = function(doctor){
        $scope.currentDoctor = doctor;
        $scope.step = 2;
    };
    $scope.init();
}]);