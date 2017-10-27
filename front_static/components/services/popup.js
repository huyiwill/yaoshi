angular.module('ohapp').factory('$popup', function ($window) {

    return function openPopup($scope, url, width, height, onClose) {

        var left = (screen.width - width) / 2;
        var top = (screen.height - height) / 2;

        var popup = $window.open(url, null, [
            'toolbar=no',
            'location=no',
            'directories=no',
            'status=no',
            'menubar=no',
            'scrollbars=no',
            'resizable=no',
            'copyhistory=no',
            'width=' + width,
            'height=' + height,
            'top=' + top,
            'left=' + left
        ].join(','));

        var closedCheckInterval = setInterval(function () {
            if (false === popup.closed) {
                return;
            }
            clearInterval(closedCheckInterval);
            onClose && $scope.$apply(onClose);

        }, 500);

        return popup;

    };

});
