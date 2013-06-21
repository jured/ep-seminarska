// Admin
angular.module('app').controller('AdminController',
    ['$scope', '$rootScope', '$routeParams', '$location', 'DataProvider',
        function ($scope, $rootScope, $routeParams, $location, DataProvider) {
            $rootScope.getUsers();

            $rootScope.editSeller = function (user) {
                if (user['password']) {
                    user.password = '';
                }
                $rootScope.editedUser = user;
                $location.path('/admin/user/edit/seller');
            };

            $rootScope.updateSeller = function (user) {
                console.log('Updating/creating seller: ' + user);
                DataProvider.updateSeller($rootScope.token, user).success(function (data) {
                    $rootScope.getUsers();
                    $location.path('/admin/users');
                });
            };

            $rootScope.changeSellerActiveStatus = function (user, status) {
                console.log('Changing costumer active status costumer: ' + JSON.stringify(user));
                DataProvider.changeSellerActiveStatus($rootScope.token, user.user_id, status).success(function (data) {
                    $rootScope.getUsers();
                });
            };


        }]);

// Seller
angular.module('app').controller('SellerController',
    ['$scope', '$rootScope', '$routeParams', '$location', 'DataProvider',
        function ($scope, $rootScope, $routeParams, $location, DataProvider) {

            $rootScope.getAllSellerData = function () {
                $rootScope.getAllItems();
                $rootScope.getUsers();
            };

            $rootScope.getAllSellerData();

            $rootScope.editItem = function (item) {
                for (index = 0; index < $rootScope.items.length; index++) {
                    if ($rootScope.items[index].product_id == item.product_id) {
                        $rootScope.item = $rootScope.items[index];
                        break;
                    }
                }
                $location.path('/seller/products/edit');
            };

            $rootScope.deleteItem = function (item) {
                console.log('Delete item: ' + item);
                DataProvider.deleteItem($rootScope.token, item.product_id).success(function (data) {
                    $rootScope.getAllSellerData();
                });
            };

            $rootScope.updateItem = function (item) {
                console.log('Updating item: ' + item);
                DataProvider.updateItem($rootScope.token, item).success(function (data) {
                    $rootScope.getAllSellerData();
                    $rootScope.item = {};
                    $location.path('/seller/products');
                });
            };

            $rootScope.changeOrderStatus = function (order, status) {
                console.log('Confirm: ' + order);
                DataProvider.changeOrderStatus($rootScope.token, order.order_id, status).success(function (data) {
                    $rootScope.getAllSellerData();
                });
            };

            $rootScope.editCostumer = function (user) {
                if (user['password']) {
                    user.password = '';
                }
                $rootScope.editedUser = user;
                $location.path('/seller/user/edit/costumer');
            };

            $rootScope.updateCostumer = function (user) {
                console.log('Updating/creating costumer: ' + user);
                DataProvider.updateCostumer($rootScope.token, user).success(function (data) {
                    $rootScope.getUsers();
                    $location.path('/seller/users/');
                });
            };

            $rootScope.changeCostumerActiveStatus = function (user, status) {
                console.log('Changing costumer active status costumer: ' + JSON.stringify(user));
                DataProvider.changeCostumerActiveStatus($rootScope.token, user.user_id, status).success(function (data) {
                    $rootScope.getUsers();
                });
            };

        }]);

// Item
angular.module('app').controller('ItemController',
    ['$scope', '$rootScope', '$routeParams', 'DataProvider',
        function ($scope, $rootScope, $routeParams, DataProvider) {
            // Wait for items to load in main controller
            $scope.$watch('items', function (newValue, oldValue) {
                if ($rootScope.items && $rootScope.items.length != 0) {
                    for (index = 0; index < $rootScope.items.length; index++) {
                        if ($rootScope.items[index].product_id == $routeParams.itemId) {
                            $scope.item = $rootScope.items[index];
                            break;
                        }
                    }
                }
            });
        }]);

// Main
angular.module('app').controller('MainController',
    ['$scope', '$rootScope', '$cookies', '$location', 'DataProvider',
        function ($scope, $rootScope, $cookies, $location, DataProvider) {
            $rootScope.token = $cookies.get('token');
            $rootScope.notARobot = false;

            if ($rootScope.token) {
                DataProvider.getUser($rootScope.token).success(function (user) {
                    $rootScope.user = user;
                });
            }

            // Get all campaigns
            $rootScope.getAllItems = function () {
                DataProvider.getAllItems().success(function (data) {
                    $rootScope.items = data;
                });
                // Get all orders
                DataProvider.getOrders($rootScope.token).success(function (orders) {
                    $rootScope.orders = orders;
                }).error(function (data) {
                    if (data && data['token']) {
                        console.log("User logged in with certificate.");
                        $cookies.put('token', data['token']);
                        $location.path('/');
                        window.location.reload()
                    }
                });
            };

            $rootScope.register = function (user) {
                DataProvider.register(user).success(function (data) {
                    $location.path('/login');
                }).error(function (data) {
                    console.log(data);
                    alert('Could not register this user!');
                });
            };

            $rootScope.login = function (user) {
                DataProvider.login(user).success(function (user) {
                    console.log('Login: ' + JSON.stringify(user));
                    $rootScope.user = user;
                    $cookies.put('token', user.token);
                    $location.path('/');
                    window.location.reload()
                }).error(function (data) {
                    alert('Unknown username and mail combinations');
                });
            };

            $rootScope.logout = function () {
                DataProvider.logout($rootScope.token).success(function (data) {
                    $cookies.remove('token');
                    $location.path('/');
                    window.location.reload()
                });
            };

            $rootScope.go = function ( path ) {
                $location.path( path );
            };

            $rootScope.getUsers = function () {
                DataProvider.getUser($rootScope.token).success(function (user) {
                    if (user && user.kind == 'seller') {
                        DataProvider.getCostumers($rootScope.token).success(function (users) {
                            $rootScope.users = users;
                        });
                    } else if (user && user.kind == 'admin') {
                        DataProvider.getSellers($rootScope.token).success(function (users) {
                            $rootScope.users = users;
                        });
                    }
                });
            };

            $rootScope.addToCart = function (item, numberOfProducts) {

                if (!$rootScope.token) {
                    $location.path('/login/');
                } else {

                    for (index = 0; index < $rootScope.cart.items.length; index++) {
                        if ($rootScope.cart.items[index].item.product_id == item.product_id) {
                            $rootScope.cart.items[index].number += numberOfProducts == undefined ? 1 : numberOfProducts;
                            $rootScope.refresCart();
                            return;
                        }
                    }
                    $rootScope.cart.items.push({'item': item, 'number': numberOfProducts});
                    $rootScope.refresCart();
                }
            };

            $rootScope.removeFromCart = function (item) {
                $rootScope.cart.items.splice($rootScope.cart.items.indexOf(item), 1);
                $rootScope.refresCart();
            };

            $rootScope.placeOrder = function () {
                if ($rootScope.cart.totalPrice == 0) {
                    alert("Cart is empty!!!!");
                } else {
                    DataProvider.placeOrder($rootScope.token, $rootScope.cart.items).success(function (data) {
                        $rootScope.getAllItems();
                        $rootScope.cart = {"price": 0.0, 'items': []};
                        $rootScope.refresCart();
                        $location.path('/orders/');
                    });
                }
            };


            $rootScope.refresCart = function () {
                $rootScope.cart.price = 0;
                for (index = 0; index < $rootScope.cart.items.length; index++)
                    $rootScope.cart.price += parseInt($rootScope.cart.items[index].item.price) * $rootScope.cart.items[index].number;
                $cookies.putObject('cart', $rootScope.cart);
            };

            $rootScope.updateSelf = function (user) {
                console.log('Updating/creating user: ' + user);
                DataProvider.updateSelf($rootScope.token, user).success(function (data) {
                    $rootScope.getUsers();
                    $location.path('/');
                });
            };

            // Initialize store
            var init = function () {

                // Store
                $rootScope.getAllItems();

                // Cart
                if (!$cookies.get('cart')) {
                    $cookies.putObject('cart', {'items': [], "price": 0.0,});
                }
                $rootScope.cart = $cookies.getObject('cart');
                $rootScope.cart.price = parseInt($rootScope.cart.price);
            };

            init();
        }]);



