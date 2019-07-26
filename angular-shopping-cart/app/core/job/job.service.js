'use strict';

angular.
module('core.job').
factory('Job', ['$resource',
    function($resource) {
        return $resource('http://training.local/job', {}, {
            query: {
                method: 'GET'
            },
        });
    }
]);
