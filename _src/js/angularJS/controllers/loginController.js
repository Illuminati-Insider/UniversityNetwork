app.controller('loginController', ['$scope', '$http', '$state', function($scope, $http, $state){
    $scope.confirm = function(){
        $http({
            method: 'POST',
            url : '/op/_rc?rtype=auth_req&type=login',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data : "json=" + JSON.stringify({
                login: $scope.login,
                psw: $scope.psw
            })
        })
        .then(function(response){
            console.debug(response);
            switch(response.data.accountType){
                case 'admin':
                    $state.go('app.admin');
                    break;
                case 'manager':
                    $state.go('app.manager');
                    break;
            }
        }, function(response){
            console.debug(response);
            alert('failed');
        });
    }
}]);