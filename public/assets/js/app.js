var myApp = angular.module( 'myApp', [
    'ui.router',
    'FileControllers'
]);

myApp.filter('handleFilePath', function () {
    return function ( filePath, file, dir ) {
        var base = filePath.replace( dir.relativePath + '/', '' );
        if ( file.isDir ){
            base += '/';
        }

        return base;
    };
});

// https://gist.github.com/thomseddon/3511330
myApp.filter('bytes', function() {
    return function(bytes, precision) {
        if (isNaN(parseFloat(bytes)) || !isFinite(bytes)) return '-';
        if (typeof precision === 'undefined') precision = 1;
        var units = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'],
            number = Math.floor(Math.log(bytes) / Math.log(1024));
        return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
    }
});


myApp.config([ '$stateProvider', '$urlRouterProvider', '$urlMatcherFactoryProvider',
    function( $stateProvider, $urlRouterProvider, $urlMatcherFactoryProvider ){

        function valToString(val) {
            return val != null ? val.toString() : val;
        }

        $urlMatcherFactoryProvider.type('nonURIEncoded', {
            encode: valToString,
            decode: valToString,
            is: function () { return true; }
        });

        //
        // For any unmatched url, redirect to /state1
        $urlRouterProvider.otherwise("/list");

        //
        // Now set up the states
        $stateProvider

            .state('list', {
                url: "/list",
                templateUrl: "partials/list.html",
                controller: 'ListController'
            })
            .state('fileView', {
                url: "/file/view{relativePath:nonURIEncoded}",
                templateUrl: "partials/file.html",
                controller: 'FileController'
            })
            .state('dirView', {
                url: "/dir/view{relativePath:nonURIEncoded}",
                templateUrl: "partials/dir.html",
                controller: 'DirController'
            })
        ;
}]);