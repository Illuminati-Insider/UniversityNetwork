// Подключение AngularJS с библиотеками
var app = angular.module("websiteApp", ['ui.router', 'ngMaterial', 'ngMessages']);

// Путь к моделям маршрутизатора.
var viewFolder = 'ui.router/views/';
// Настройка маршрутизатора
app.config(['$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/main');

    $stateProvider
    // Корень
    .state('app',{
        abstract: true,
        url: '/',
        views: {
            'header' : {
                templateUrl: viewFolder + 'header.html',
                controller: 'headerController'
            },
            'body' : {},
            'footer' : {
                templateUrl: viewFolder + 'footer.html'
            }
        }
    })
    .state('app.main', {
        url: 'main',
        views: {
            'main' : {
                templateUrl: viewFolder + 'main.html'
            }
        }
    })
    // Состояние отображения страницы входа. Внутри ui-view="body".
    .state('app.login',{
        url: 'login',
        views: {
            'login' : {
                templateUrl: viewFolder + 'login.html',
                controller: 'loginController'
            }
        }
    })
    // Состояние отображения страницы регистрации. Внутри ui-view="body".
    .state('app.register',{
        url: 'register',
        views: {
            'register' : {
                templateUrl: viewFolder + 'register.html',
                controller: 'registerController'
            }
        }
    })
    // Кабинеты
    .state('app.admin',{
        url: 'admin',
        views: {
            'adminPC' : {
                templateUrl: viewFolder + 'admin.html',
                controller: 'adminController'
            }
        }
    })
    .state('app.manager',{
        url: 'manager',
        views: {
            'managerPC' : {
                templateUrl: viewFolder + 'manager.html',
                controller: 'managerController'
            }
        }
    })
    .state('app.student',{
        url: 'student',
        views: {
            'studentPC' : {
                templateUrl: viewFolder + 'student.html'
            }
        }
    })
}]);