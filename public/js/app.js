
var mainApp = angular.module('mainApp', ['ui.rCalendar']);

mainApp.controller('doctorsListController', [ "$scope", "$http",
function ($scope, $http) {
    $scope.doctorsData = [];
    $scope.currentDoctor = null;
    $scope.step = 0;
    $scope.loading = false;

    $scope.init = function() {
        $scope.getDoctorsData();
    };

    $scope.getDoctorsData = function() {
        var params = {
        };
        $scope.loading = true;
        $http.get('/doctor/get-doctors-list', {
            params: params
        }).success(function(data) {
            if (data.success) {
                $scope.step = 1;
                $scope.doctorsData = data.list;
            } else {
// добавить диалоговое окно
            }
            $scope.loading = false;
        }).
        error(function() {
// добавить диалоговое окно
            $scope.loading = false;
        });
    };

    $scope.goToList = function(){
        $scope.currentDoctor = null;
        $scope.step = 1;
    };

    $scope.goToCalendar = function(){
        $scope.step = 2;
    };

    $scope.getFormatedDate = function(){
        if($scope.step !== 3){
            return;
        }
        var date = new Date($scope.currentDoctor.selectedTicked.startTime);
        var options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            weekday: 'long',
            timezone: 'UTC',
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric'
        };
        return date.toLocaleString("ru", options);
    };

    $scope.onEventSelected = function(ticket){
        $scope.currentDoctor.selectedTicked = ticket;
        $scope.step = 3;
        return;
    };

    $scope.rangeChanged = function (startTime, endTime) {
console.log('rangeChanged');
console.log(startTime);
console.log(endTime);
  };
  
    $scope.getTicket = function(){
        var params = {
            'ticket_id': $scope.currentDoctor.selectedTicked.id
        };
        $scope.loading = true;
        $http.get('/doctor/get-ticket', {
            params: params
        }).success(function(data) {
            if (data.success) {
                for (var i in $scope.currentDoctor.tickets) {
                    if($scope.currentDoctor.tickets[i].id === data.ticket.id){
                        data.ticket.startTime = new Date(data.ticket.startTime);
                        data.ticket.endTime = new Date(data.ticket.endTime);
                        $scope.currentDoctor.tickets[i] = data.ticket;
                    }
                }
                $scope.$broadcast('eventSourceChanged',$scope.currentDoctor.tickets);
            } else {
// добавить диалоговое окно
            }
            $scope.loading = false;
        }).
        error(function() {
// добавить диалоговое окно
            $scope.loading = false;
        });
    };

    $scope.getDoctorTickets = function(doctor, month){
        var params = {
            'doctor_id': doctor.id,
            'month': month
        };
        $scope.loading = true;
        $http.get('/doctor/get-doctor-tickets', {
            params: params
        }).success(function(data) {
            if (data.success) {
                for (var i in data.tickets) {
                    $scope.currentDoctor.tickets.push({
                        'id': data.tickets[i].id,
                        'title': data.tickets[i].title,
                        'startTime': new Date(data.tickets[i].startTime),
                        'endTime': new Date(data.tickets[i].endTime),
                        'allDay': data.tickets[i].allDay
                    });
                }
                $scope.$broadcast('eventSourceChanged',$scope.currentDoctor.tickets);
            } else {
// добавить диалоговое окно
            }
            $scope.loading = false;
        }).
        error(function() {
// добавить диалоговое окно
            $scope.loading = false;
        });
    };

    $scope.startTicketing = function(doctor){
        $scope.currentDoctor = doctor;
        $scope.currentDoctor.tickets = [];
        $scope.getDoctorTickets(doctor);
        $scope.step = 2;
    };
    $scope.init();
}]);