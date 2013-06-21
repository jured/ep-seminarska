var app = angular.module('app', ['ngRoute', 'ui.bootstrap', 'ngCookies', 'vcRecaptcha'])

app.config(['$routeProvider', '$httpProvider', '$locationProvider', function ($routeProvider, $httpProvider, $locationProvider) {
    $routeProvider.when('/', {
            templateUrl: 'templates/storefront.html'
        })

        .when('/item/:itemId/', {
            templateUrl: 'templates/product.html',
            controller: 'ItemController'
        }).when('/login', {
            templateUrl: 'templates/login.html'
        }).when('/register', {
            templateUrl: 'templates/register.html'
        }).when('/cart', {
            templateUrl: 'templates/cart.html'
        }).when('/orders', {
            templateUrl: 'templates/orders.html'
        }).when('/edit', {
            controller: 'MainController',
            templateUrl: 'templates/common/edit-self.html'
        }).when('/admin/sellers', {
            controller: 'AdminController',
            templateUrl: 'templates/admin/sellers.html'
        }).when('/admin/user/edit/seller', {
            controller: 'AdminController',
            templateUrl: 'templates/admin/edit-seller.html'
        }).when('/seller/orders', {
            controller: 'SellerController',
            templateUrl: 'templates/seller/orders.html'
        }).when('/seller/products', {
            controller: 'SellerController',
            templateUrl: 'templates/seller/products.html'
        }).when('/seller/costumers', {
            controller: 'SellerController',
            templateUrl: 'templates/seller/costumers.html'
        }).when('/seller/user/edit/costumer', {
            controller: 'SellerController',
            templateUrl: 'templates/seller/edit-costumer.html'
        }).when('/seller/products/edit', {
            controller: 'SellerController',
            templateUrl: 'templates/seller/edit-product.html'
        })


        .otherwise({
            redirectTo: '/'
        });

    $locationProvider.html5Mode(true);
}])

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