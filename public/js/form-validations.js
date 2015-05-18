/**
 * Created by piyush sharma on 13-05-2015.
 */
/** Javascript file for validating input fields on the front end*/
/*
 * Below code will work for all the form pages in the site, will validate the fields as passed in the page.
 *
 * After validating, it will fire an custom event "form-error" on the current Form element,
 * based on which other javascript file can work accordingly.
 * */



var number =/^\d+$/;
var numberDecimal =/^\d+(\.?\d+)?$/;
var phoneNumber=/^\d{9,12}$/;
var text=/^\s+$/;
var date = /^\d{2}(-:,\/)?\d{2}(-:,\/)?\d{4}$/;
var email = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
var url = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
var selectType = /^\s+$/;
var whitespace = /^\s*$/;
var zip = /^\d{4,5}$/;
var year = /^\d{4}$/;

var errorClassName="error_form_validation";

$(document).ready(function(){

    var forms = $('form');

    $(forms).each(function(i,form){
        $(form).submit(function(event){

            var inputs  =   $(this).find('input');
            var selects =   $(this).find('select');
            var textareas   =   $(this).find('textarea');

            var formErrorStatus=false;

            $(this).find("."+errorClassName).removeClass(errorClassName);

            formErrorStatus = validateInputAndActiveToolTip(inputs) || validateInputAndActiveToolTip(selects)
            || validateInputAndActiveToolTip(textareas);

            if(formErrorStatus)
            {
                event.preventDefault();
                $(this).trigger('form-error');
                return false;
            }
        });
    });

    function validateInputAndActiveToolTip(inputArray)
    {
        var formErrorStatus=false;
        $(inputArray).each(function(index,element){

            var errorStatus=false;

            var validationType;
            var required=false;

            if($(element).data('validate') != undefined)
            {
                validationType=($(element).data('validate')).split('|');
            }
            else
            {
                return;
            }


            for(var i = 0;i<validationType.length;i++)
            {

                //When input is required, checks whether the input is having any data or not
                if(validationType[i]==="require")
                {
                    required=true;
                    if(whitespace.test($(element).val()))
                    {
                        $(element).addClass(errorClassName);
                        $(element).tooltip();
                        errorStatus=true;
                    }
                    if($(element).data('invalidValue')!=undefined && $(element).val()==($(element).data('invalidValue')))
                    {
                        $(element).addClass(errorClassName);
                        $(element).tooltip();
                        errorStatus=true;
                    }
                }

                //When input is required, then checking data values
                //Also, when input is not required and the value is not blank, then checking corresponding data
                if(required || (!required && !whitespace.test($(element).val())))
                {
                    switch(validationType[i])
                    {
                        case "date":
                            if($(element).val()=="dd-mm-yyyy")
                            {
                                break;
                            }
                            if(!date.test($(element).val()))
                            {
                                errorStatus=true;
                            }

                            break;

                        case "select":


                            if (selectType.test($(element).val())) {
                                errorStatus = true;
                            }
                            if($(element).data('invalidValue')!=undefined && $(element).val()==($(element).data('invalidValue')))
                            {
                                errorStatus=true;
                            }
                            break;

                        case "number":

                            if(!number.test($(element).val()))
                            {
                                errorStatus=true;
                            }

                            break;

                        case "email":

                            if(!email.test($(element).val()))
                            {
                                errorStatus=true;
                            }

                            break;

                        case "url":

                            if(!url.test($(element).val()))
                            {
                                errorStatus=true;
                            }

                            break;

                        case "phoneNumber":

                            if(!phoneNumber.test($(element).val()))
                            {
                                errorStatus=true;
                            }

                            break;

                        case "zip":

                            if(!zip.test($(element).val()))
                            {
                                errorStatus=true;
                            }

                            break;

                        case "year":

                            if(!year.test($(element).val()))
                            {
                                errorStatus=true;
                            }

                            break;

                        case "numberDecimal":

                            if(!numberDecimal.test($(element).val()))
                            {
                                errorStatus=true;
                            }

                            break;
                        case "age":

                            if(!number.test($(element).val()) || parseInt($(element).val()) > 100)
                            {
                                errorStatus=true;
                            }
                    }

                    if(errorStatus)
                    {
                        $(element).addClass(errorClassName);
                        $(element).tooltip();
                        formErrorStatus=true;
                    }
                }

            }

        });


        return formErrorStatus;
    }
});

//# sourceMappingURL=form-validations.js.map