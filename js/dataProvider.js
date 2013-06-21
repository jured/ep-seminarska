(function () {
    DataProvider.$inject = ['$http'];

    function DataProvider($http) {
        var baseURI = '/be';

        var dataProvider = {};

        // Anonymus
        dataProvider.getAllItems = function () {
            return $http.get(baseURI + '/products/all');
        };

        // Costumer
        dataProvider.register = function (user) {
            return $http.post(baseURI + '/register', user, {});
        };

        dataProvider.login = function (user) {
            return $http.post(baseURI + '/user/login', user, {});
        };

        dataProvider.getUser = function (token) {
            return $http.get(baseURI + '/user/' + token);
        };

        dataProvider.logout = function (token) {
            return $http.get(baseURI + '/user/' + token + '/logout');
        };

        dataProvider.getOrders = function (token) {
            return $http.get(baseURI + '/user/' + token + '/orders', {});
        };

        dataProvider.placeOrder = function (token, order) {
            return $http.post(baseURI + '/user/' + token + '/create/order', order, {});
        };


        // Seller
        dataProvider.deleteItem = function (token, itemId) {
            return $http.delete(baseURI + '/seller/' + token + '/product/' + itemId, {});
        };

        dataProvider.updateItem = function (token, item) {
            return $http.post(baseURI + '/seller/' + token + '/create-update/product', item, {});
        };

        dataProvider.changeOrderStatus = function (token, orderId, status) {
            return $http.get(baseURI + '/seller/' + token + '/order/' + orderId + '/status/' + status, {});
        };

        dataProvider.getCostumers = function (token) {
            return $http.get(baseURI + '/seller/' + token  + '/costumers', {});
        };

        dataProvider.changeCostumerActiveStatus = function (token, userId, status) {
            return $http.get(baseURI + '/seller/' + token + '/status/costumer/' + userId + '/' + status, {});
        };

        dataProvider.updateCostumer = function (token, user) {
            return $http.post(baseURI + '/seller/' + token + '/edit/costumer', user, {});
        };

        // Admin
        dataProvider.getSellers = function (token) {
            return $http.get(baseURI + '/admin/' + token  + '/sellers', {});
        };

        dataProvider.changeSellerActiveStatus = function (token, userId, status) {
            return $http.get(baseURI + '/admin/' + token + '/status/seller/' + userId + '/' + status, {});
        };

        dataProvider.updateSeller = function (token, user) {
            return $http.post(baseURI + '/admin/' + token + '/edit/seller', user, {});
        };

        // Common
        dataProvider.updateSelf = function (token, user) {
            return $http.post(baseURI + '/self/' + token + '/edit', user, {});
        };

        return dataProvider;
    }

    angular.module('app').factory('DataProvider', DataProvider);
}());