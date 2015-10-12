var fileControllers = angular.module( 'FileControllers', [] );

fileControllers.controller( 'ListController', [ '$scope', '$http' ,  function ( $scope, $http ){
    $http.get('/api/get/files' ).success(function(files){
        $scope.files = files;
    });
}]);

fileControllers.controller( 'FileController', [ '$scope', '$http', '$stateParams', '$sce', '$timeout',
    function ( $scope, $http, $stateParams, $sce, $timeout ){
        var path = $stateParams.relativePath;

        // get a file from the server, prepare it, and bring it into scope
        function getFile( file ){
            if ( file.data.content.parsed ){
                file.data.content.parsed = $sce.trustAsHtml(file.data.content.parsed);
            }

            // copy the content so we can edit it easily
            file.data.content.edited = file.data.content.full;
            $scope.file = file;

            l('file', file );
        }

        //
        $scope.resetFlags = function(){
            $scope.is_saved = false;
            $scope.is_editing = false;
            $scope.is_previewing = false;
        };

        // first run
        $scope.init = function(){
            // init: get the file data from the server
            $http.get('/api/get/file' + path )
                .success( getFile );

            $scope.resetFlags();
        };


        // submit the edited content to the server
        $scope.submit = function(){
            if ($scope.file) {
                $http.post( '/api/update/file', $scope.file )
                    .success( getFile )
                    .success( function(){
                        $scope.is_saved = true;

                        $timeout(function() {
                            $scope.resetFlags();
                        }, 800);
                    } );
            }
        };

        // reset the edited content
        $scope.editCancel = function(){
            if ( $scope.file ){
                $scope.file.data.content.edited = $scope.file.data.content.full;
                $scope.resetFlags();
            }
        };

        // preview certain types of files
        $scope.preview = function(){
            if ( $scope.file ) {
                $http.post( '/api/util/preview', $scope.file )
                    .success(function( response ){
                        $scope.file.data.content.preview = $sce.trustAsHtml( response );
                    });
            }
        };


        // do it
        $scope.init();
    }
]);

fileControllers.controller( 'DirController', [ '$scope', '$http', '$stateParams',
    function ( $scope, $http, $stateParams ){
        $http.get('/api/get/files' ).success(function(data){

            var path = $stateParams.relativePath;

            $scope.files = _.filter( data, function( file ){
                return ( file.relativePath !== path &&
                         file.relativePath.indexOf( path ) !== -1 );
            });

            $scope.dir = _.find( data, _.matchesProperty( 'relativePath', path ) );
        });
    }
]);