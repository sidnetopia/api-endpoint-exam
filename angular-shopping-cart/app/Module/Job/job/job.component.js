'use strict';

// Register `productList` component, along with its associated controller and template
angular
    .module('job')
    .component('job', {
        templateUrl: 'Module/Job/job/job.template.html',
        controller: ['Job',
            function JobController(Job) {
                var self = this;
                Job.query(function (job) {
                    self.jobItems = job.jobItems;
                    self.jobOrderDetails = job.jobOrderDetails;
                });
            }
        ]
    });
