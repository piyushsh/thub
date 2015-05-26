
var talentProfile = angular.module('talentProfile',[]);

talentProfile.constant("BROADCAST_UPDATE_PROFILE","updateProfile_event")
    .constant("BROADCAST_UPDATE_PROFILE_SUMMARY","updateSummary_event")
    .constant("BROADCAST_UPDATE_AWARDS","updateAwards_event");

talentProfile.config(['$httpProvider','CSRF_TOKEN', function($httpProvider,CSRF_TOKEN) {
    $httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = CSRF_TOKEN;
}]);

talentProfile.factory('DataSharingService',['$rootScope',function($rootScope){
    var sharedData={};
    sharedData.data = "";

    sharedData.prepareForBroadCast = function(message,type){
        this.data = message;
        this.broadcastData(type);
    };

    sharedData.broadcastData = function(type)
    {
        $rootScope.$broadcast(type);
    };
    return sharedData;
}]);

talentProfile.factory('ProfileOperationService',['$http','CSRF_TOKEN',function($http,$csrf){

    return function(userData,url)
    {
        return $http.put(url, userData)
            .success(function(data){

            })
            .error(function(data){

            });
    };

}]);

talentProfile.factory('safeApply', [function($rootScope) {
    return function($scope, fn) {
        var phase = $scope.$root.$$phase;
        if(phase == '$apply' || phase == '$digest') {
            if (fn) {
                $scope.$eval(fn);
            }
        } else {
            if (fn) {
                $scope.$apply(fn);
            } else {
                $scope.$apply();
            }
        }
    }
}]);

//For Updating User's Profile data i.e. First name, last name, about,etc.
talentProfile.controller('ProfilePresented',['$scope','DataSharingService','BROADCAST_UPDATE_PROFILE','$window',
    function($scope,DataSharingService,BROADCAST_UPDATE_PROFILE,$window){

        $scope.sending_Data_to_server = false;

        $scope.showLoading = function(){
            $scope.sending_Data_to_server = true;
        };

        $scope.$on(BROADCAST_UPDATE_PROFILE,function(){
            console.log("Data Updating" + DataSharingService.data.first_name);
            $scope.first_name = DataSharingService.data.first_name;
            $scope.last_name = DataSharingService.data.last_name;
            $scope.country = $window.countryArray[DataSharingService.data.country];
            $scope.about = DataSharingService.data.about;
    });
}]);


//Profile Data i.e. Summary, Awards,
talentProfile.controller('ProfileDataController',['$scope','DataSharingService','BROADCAST_UPDATE_PROFILE_SUMMARY','BROADCAST_UPDATE_AWARDS',
    function($scope,DataSharingService,BROADCAST_UPDATE_PROFILE_SUMMARY,BROADCAST_UPDATE_AWARDS){

        $scope.$on(BROADCAST_UPDATE_PROFILE_SUMMARY,function(){
            console.log("Summary Updated" + DataSharingService.data.summary);
            $scope.summary = DataSharingService.data.summary;
        });

        $scope.$on(BROADCAST_UPDATE_AWARDS,function(){
            $scope.awards = DataSharingService.data.award_details;
        });

}]);



//For Updating User's Profile data i.e. First name, last name, about,etc.
talentProfile.controller('UserProfileUpdate',['$scope','ProfileOperationService','$controller','$window','$timeout','safeApply','DataSharingService','BROADCAST_UPDATE_PROFILE','BROADCAST_UPDATE_PROFILE_SUMMARY','BROADCAST_UPDATE_AWARDS',
    function($scope,ProfileOperationService,$controller,$window,$timeout,safeApply,DataSharingService,BROADCAST_UPDATE_PROFILE,BROADCAST_UPDATE_PROFILE_SUMMARY,BROADCAST_UPDATE_AWARDS){

        $scope.data_saved = false;
        $scope.sending_Data_to_server = false;

        $scopeProfilePresented = $scope.$new();
        $controller("ProfilePresented",{$scope : $scopeProfilePresented});

        $scope.updateProfileData = function(){
            $scope.sending_Data_to_server = true;
            $scope.server_message = "";

            var userData = {
                user_id     :   $scope.user_id,
                first_name   :   $scope.first_name,
                last_name   :   $scope.last_name,
                country :   $scope.country,
                about   :   $scope.about
            };

            ProfileOperationService(userData,'profile/updateProfileData').success(function(data){
                if(data.request_status=="successful")
                {
                    console.log("Broadcasting data");
                    DataSharingService.prepareForBroadCast(userData,BROADCAST_UPDATE_PROFILE);
                    $scope.data_saved = true;
                    $scope.sending_Data_to_server = false;
                }
            })
            .error(function(data){
                    $scope.server_message = data;
                    $scope.sending_Data_to_server = false;
            });
        };

        $scope.updateProfileSummary = function(){
            $scope.sending_Data_to_server = true;
            $scope.server_message = "";

            var userData = {
                user_id     :   $scope.user_id,
                summary     :   $scope.summary
            };
            if($scope.summary == "")
            {
                return false;
            }
            ProfileOperationService(userData,'profile/profileSummary').success(function(data){
                if(data.status=="successful")
                {
                    console.log("Broadcasting data");
                    DataSharingService.prepareForBroadCast(userData,BROADCAST_UPDATE_PROFILE_SUMMARY);
                    $scope.data_saved = true;
                    $scope.sending_Data_to_server = false;
                }
            })
                .error(function(data){
                    $scope.server_message = data;
                    $scope.sending_Data_to_server = false;
                });
        };



        $scope.updateAwards = function(){
            $scope.sending_Data_to_server = true;
            $scope.server_message = "";

            var userData = {
                user_id     :   $scope.user_id,
                award_details     :   $scope.awards
            };
            if($scope.summary == "")
            {
                return false;
            }
            ProfileOperationService(userData,'profile/profileAwards').success(function(data){
                console.log("Data Result : " + data.status);
                if(data.status=="successful")
                {
                    console.log("Broadcasting data");
                    DataSharingService.prepareForBroadCast(userData,BROADCAST_UPDATE_AWARDS);
                    $scope.data_saved = true;
                    $scope.sending_Data_to_server = false;
                }
            })
                .error(function(data){
                    $scope.server_message = data;
                    $scope.sending_Data_to_server = false;
                });
        };





        $scope.showLoading = function(){
            $scope.sending_Data_to_server=true;
        };

        $scope.hideLoading = function(){
            $scope.sending_Data_to_server=false;
        };
}]);

