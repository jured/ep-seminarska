// Admin
angular.module('app').controller('AdminController',
    ['$scope', '$rootScope', '$routeParams', '$location', 'DataProvider',
        function ($scope, $rootScope, $routeParams, $location, DataProvider) {
            // Get users
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

            $scope.editSeller = function (user) {
                if (user['password']) {
                    user.password = '';
                }
                $rootScope.editedUser = user;
                $location.path('/admin/user/edit/seller');
            };

            $rootScope.updateSeller = function (user) {
                DataProvider.updateSeller($rootScope.token, user).success(function () {
                    // Get users
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
                    $location.path('/admin/users');
                });
            };

            $rootScope.changeSellerActiveStatus = function (user, status) {
                DataProvider.changeSellerActiveStatus($rootScope.token, user.user_id, status).success(function () {
                    // Get users
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
                });
            };

        }]);

// Seller
angular.module('app').controller('SellerController',
    ['$scope', '$rootScope', '$routeParams', '$location', 'DataProvider',
        function ($scope, $rootScope, $routeParams, $location, DataProvider) {

            // Get all seller data
            // Get all campaigns
            DataProvider.getAllItems().success(function (data) {
                $rootScope.items = data;
            });
            // Get all orders
            DataProvider.getOrders($rootScope.token).success(function (orders) {
                $rootScope.orders = orders;
            }).error(function (data) {
                if (data && data['token']) {
                    $cookies.put('token', data['token']);
                    $location.path('/');
                    window.location.reload()
                }
            });
            // Get users
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
                DataProvider.deleteItem($rootScope.token, item.product_id).success(function () {
                    // Get all seller data
                    // Get all campaigns
                    DataProvider.getAllItems().success(function (data) {
                        $rootScope.items = data;
                    });
                    // Get all orders
                    DataProvider.getOrders($rootScope.token).success(function (orders) {
                        $rootScope.orders = orders;
                    }).error(function (data) {
                        if (data && data['token']) {
                            $cookies.put('token', data['token']);
                            $location.path('/');
                            window.location.reload()
                        }
                    });
                    // Get users
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
                });
            };

            $rootScope.updateItem = function (item) {
                DataProvider.updateItem($rootScope.token, item).success(function () {
                    // Get all seller data
                    // Get all campaigns
                    DataProvider.getAllItems().success(function (data) {
                        $rootScope.items = data;
                    });
                    // Get all orders
                    DataProvider.getOrders($rootScope.token).success(function (orders) {
                        $rootScope.orders = orders;
                    }).error(function (data) {
                        if (data && data['token']) {
                            $cookies.put('token', data['token']);
                            $location.path('/');
                            window.location.reload()
                        }
                    });
                    // Get users
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
                    $rootScope.item = {};
                    $location.path('/seller/products');
                });
            };

            $rootScope.changeOrderStatus = function (order, status) {
                DataProvider.changeOrderStatus($rootScope.token, order.order_id, status).success(function () {
                    // Get all seller data
                    // Get all campaigns
                    DataProvider.getAllItems().success(function (data) {
                        $rootScope.items = data;
                    });
                    // Get all orders
                    DataProvider.getOrders($rootScope.token).success(function (orders) {
                        $rootScope.orders = orders;
                    }).error(function (data) {
                        if (data && data['token']) {
                            $cookies.put('token', data['token']);
                            $location.path('/');
                            window.location.reload()
                        }
                    });
                    // Get users
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
                DataProvider.updateCostumer($rootScope.token, user).success(function () {
                    // Get users
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
                    $location.path('/seller/users/');
                });
            };

            $rootScope.changeCostumerActiveStatus = function (user, status) {
                DataProvider.changeCostumerActiveStatus($rootScope.token, user.user_id, status).success(function () {
                    // Get users
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
                });
            };

        }]);

// Item
angular.module('app').controller('ItemController',
    ['$scope', '$rootScope', '$routeParams',
        function ($scope, $rootScope, $routeParams) {
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

            $scope.register = function (user) {
                DataProvider.register(user).success(function () {
                    $location.path('/login');
                }).error(function () {
                    alert('Could not register this user!');
                });
            };

            $scope.login = function (user) {
                DataProvider.login(user).success(function (user) {
                    $rootScope.user = user;
                    $cookies.put('token', user.token);
                    $location.path('/');
                    window.location.reload()
                }).error(function () {
                    alert('Unknown username and mail combinations');
                });
            };

            $scope.logout = function () {
                DataProvider.logout($rootScope.token).success(function () {
                    $cookies.remove('token');
                    $location.path('/');
                    window.location.reload()
                });
            };

            $rootScope.go = function ( path ) {
                $location.path( path );
            };
            
            $scope.refreshCart = function () {
                // refresh cart
                $rootScope.cart.price = 0;
                for (index = 0; index < $rootScope.cart.items.length; index++)
                    $rootScope.cart.price += parseInt($rootScope.cart.items[index].item.price) * $rootScope.cart.items[index].number;
                $cookies.putObject('cart', $rootScope.cart);
            };

            $scope.itemToCart = function (item, numberOfProducts) {

                if (!$rootScope.token) {
                    $location.path('/login/');
                } else {

                    for (index = 0; index < $rootScope.cart.items.length; index++) {
                        if ($rootScope.cart.items[index].item.product_id == item.product_id) {
                            $rootScope.cart.items[index].number += numberOfProducts == undefined ? 1 : numberOfProducts;

                            // refresh cart
                            $rootScope.cart.price = 0;
                            for (index = 0; index < $rootScope.cart.items.length; index++)
                                $rootScope.cart.price += parseInt($rootScope.cart.items[index].item.price) * $rootScope.cart.items[index].number;
                            $cookies.putObject('cart', $rootScope.cart);
                            return;
                        }
                    }
                    $rootScope.cart.items.push({'item': item, 'number': numberOfProducts});
                    // refresh cart
                    $rootScope.cart.price = 0;
                    for (index = 0; index < $rootScope.cart.items.length; index++)
                        $rootScope.cart.price += parseInt($rootScope.cart.items[index].item.price) * $rootScope.cart.items[index].number;
                    $cookies.putObject('cart', $rootScope.cart);
                }
            };

            $scope.removeCartItem = function (item) {
                $rootScope.cart.items.splice($rootScope.cart.items.indexOf(item), 1);
                // refresh cart
                $rootScope.cart.price = 0;
                for (index = 0; index < $rootScope.cart.items.length; index++)
                    $rootScope.cart.price += parseInt($rootScope.cart.items[index].item.price) * $rootScope.cart.items[index].number;
                $cookies.putObject('cart', $rootScope.cart);
            };

            $scope.finishOrder = function () {
                if ($rootScope.cart.totalPrice == 0) {
                    alert("Cart is empty!!!!");
                } else {
                    DataProvider.finishOrder($rootScope.token, $rootScope.cart.items).success(function () {
                        // Get all campaigns
                        DataProvider.getAllItems().success(function (data) {
                            $rootScope.items = data;
                        });
                        // Get all orders
                        DataProvider.getOrders($rootScope.token).success(function (orders) {
                            $rootScope.orders = orders;
                        }).error(function (data) {
                            if (data && data['token']) {
                                $cookies.put('token', data['token']);
                                $location.path('/');
                                window.location.reload()
                            }
                        });
                        $rootScope.cart = {"price": 0.0, 'items': []};
                        // refresh cart
                        $rootScope.cart.price = 0;
                        for (index = 0; index < $rootScope.cart.items.length; index++)
                            $rootScope.cart.price += parseInt($rootScope.cart.items[index].item.price) * $rootScope.cart.items[index].number;
                        $cookies.putObject('cart', $rootScope.cart);
                        $location.path('/orders/');
                    });
                }
            };

            $rootScope.updateSelf = function (user) {
                DataProvider.updateSelf($rootScope.token, user).success(function () {
                    // Get users
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
                    $location.path('/');
                });
            };

            // Initialize store
            var init = function () {

                // Store
                // Get all campaigns
                DataProvider.getAllItems().success(function (data) {
                    $rootScope.items = data;
                });
                // Get all orders
                DataProvider.getOrders($rootScope.token).success(function (orders) {
                    $rootScope.orders = orders;
                }).error(function (data) {
                    if (data && data['token']) {
                        $cookies.put('token', data['token']);
                        $location.path('/');
                        window.location.reload()
                    }
                });

                // Cart
                if (!$cookies.get('cart')) {
                    $cookies.putObject('cart', {'items': [], "price": 0.0});
                }
                $rootScope.cart = $cookies.getObject('cart');
                $rootScope.cart.price = parseInt($rootScope.cart.price);
            };

            init();
}]);
