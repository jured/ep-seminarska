var app = angular.module('app', ['ngRoute', 'ui.bootstrap', 'ngCookies', 'vcRecaptcha']);

app.config(['$routeProvider', '$httpProvider', '$locationProvider', function ($routeProvider, $httpProvider, $locationProvider) {
    $routeProvider.when('/', {
            templateUrl: 'pages/storefront.html'
        })

        .when('/item/:itemId/', {
            templateUrl: 'pages/product.html',
            controller: 'ItemController'
        }).when('/login', {
            templateUrl: 'pages/login.html'
        }).when('/register', {
            templateUrl: 'pages/register.html'
        }).when('/cart', {
            templateUrl: 'pages/cart.html'
        }).when('/orders', {
            templateUrl: 'pages/orders.html'
        }).when('/edit', {
            controller: 'MainController',
            templateUrl: 'pages/edit-self.html'
        }).when('/admin/sellers', {
            controller: 'AdminController',
            templateUrl: 'pages/sellers.html'
        }).when('/admin/user/edit/seller', {
            controller: 'AdminController',
            templateUrl: 'pages/edit-seller.html'
        }).when('/seller/orders', {
            controller: 'SellerController',
            templateUrl: 'pages/seller-orders.html'
        }).when('/seller/products', {
            controller: 'SellerController',
            templateUrl: 'pages/products.html'
        }).when('/seller/costumers', {
            controller: 'SellerController',
            templateUrl: 'pages/costumers.html'
        }).when('/seller/user/edit/costumer', {
            controller: 'SellerController',
            templateUrl: 'pages/edit-costumer.html'
        }).when('/seller/products/edit', {
            controller: 'SellerController',
            templateUrl: 'pages/edit-product.html'
        })

        .otherwise({
            redirectTo: '/'
        });

    $locationProvider.html5Mode(true);
}]);

app.run(function ($rootScope, $location, $cookies, $window) {
    // register listener to watch route changes
    $rootScope.$on('$routeChangeStart', function (event, next, current) {
        console.log($location.path());

        var path = $location.path();

        // user is logged in;
        if ($cookies.get('token')) {

            // Disable follwing pages
            if ((path.search('register') > -1 || path.search('login') > -1)) {
                $location.path('/');
            }

        }

        if ($cookies.get('token') || path.search('register') > -1 || path.search('login') > -1) {
            // Redirect if not ssl
            if ($location.absUrl().search('http://') != -1) {
                $window.location.href = $location.absUrl().replace('http', 'https');
            }
        }

        // user is not loged in,
        if (!$cookies.get('token')) {

            //  redirect restricted pages to login
            if (path.search('register') == -1
                && path.search('login') == -1 && path.search('item') == -1 && path !== '/') {
                console.log('Must login to visit this page!');
                $location.path('/login/');
            }
        }


    });
});