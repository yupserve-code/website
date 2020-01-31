/**
 * JQuery Form Validation
 *
 * Author: Prabhas Khamari
 * Copyright (C)2016 Nexus Technoware Solutions Pvt. Ltd.
 */

/* classes to use for validation checking */

/*
 .chk_blank : for blank field validation
 .chk_email : for email field validation
 .chk_phone : for phone number field validation
 .chk_alphanumeric  :  for alphanumeric only field validation [a-z] [1-9]
 .chk_alphanumeric_with_dash  :  for alphanumeric values with dash only field validation  [a-z] [1-9] [-] 
 */

$.fn.pkvalidateform = function (options) {
    debugger;
    var settings = $.extend({
        formId: 'add_data', // id of the form for validation
        errorClass: 'val-error',
        containerClass: 'input-cont', // class of the input field container
        errorMsgClass: 'error-msg', // class of error message container
        blankValidationMsg: 'Please fill this field.', // error message on blank field validation
        emailValidationMsg: 'Please enter a valid email address.', // error message on email validation
        phoneValidationMsg: 'Please enter a valid phone number.', // error phone number validation
        postcodeValidationMsg: 'Please enter a six digit valid postcode number.', // error phone number validation
        toTimeValidationMsg: 'To Time must be greater from From Time.', // error phone number validation
        imagesizeValidationMsg: 'Height and Width must not exceed 100px.',
        imagetypeValidationMsg: 'Please check image type.',
        radioButtonValidationMsg: 'Please check the radio button.',
        maxDayValidationMsg: 'Day should not greater than seven days.',
        maxPeriodValidationMsg: 'Periods should not greater than twelve days.',
        facultyAgeValidationMsg: 'Age schould not be less than eighteen.',
        toDateValidationMsg: 'To date should not less than or same as from date.',
        sectionFormatValidationMsg: 'Section must be Section- format.',
        studentsAgeValidationMsg: 'Age schould  be greater than five.',
        alphanumericValidationMsg: 'Please enter alphanumeric values only.', // error alphanumeric validation
        numericValidationMsg: 'Please enter numbers only.',
        alphanumericWithDashValidationMsg: 'Please enter the following values only. (a-z) (1-9) (-)'
    }, options);

    /* define an array */
    var validation = [];

    //var chosenval = $('.chosen_select').val();


    /* blank field validation starts */
    /* check if no_blank class found inside the form */
    if ($('.chk_blank').length) {

        /* check the value of all inputs elements with no_blank class and return false if input field is blank else return true */
        /* then insert the return value in an array */
        $('#' + settings.formId).find('.chk_blank').each(function () {

            /* go through all inputs having class .no_blank and show error if input value is blank */
            if ($.trim($(this).val()) === '') {
                validation.push('false');
                $(this).closest('.' + settings.containerClass).addClass(settings.errorClass);
                $(this).closest('.' + settings.containerClass).find('.' + settings.errorMsgClass).html(settings.blankValidationMsg);
            } else {
                validation.push('true');
                if ($(this).closest('.' + settings.containerClass).hasClass(settings.errorClass)) {
                    $(this).closest('.' + settings.containerClass).removeClass(settings.errorClass);
                } else {
                    /* do nothing */
                }

            }
        });
    }
    ;
    /* blank field validation ends */


    /* email validation starts */
    if ($('.chk_email').length) {

        var email_pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;

        $('#' + settings.formId).find('.chk_email').each(function () {
            if (!email_pattern.test($.trim($(this).val()))) {
                validation.push('false');
                $(this).closest('.' + settings.containerClass).addClass(settings.errorClass);
                $(this).closest('.' + settings.containerClass).find('.' + settings.errorMsgClass).html(settings.emailValidationMsg);
            } else {
                validation.push('true');
                if ($(this).closest('.' + settings.containerClass).hasClass(settings.errorClass)) {
                    $(this).closest('.' + settings.containerClass).removeClass(settings.errorClass);
                } else {
                    /* do nothing */
                }
            }
        });
    }
    ;
    /* email validation ends */
    /* phone number validation starts */

    if ($('.chk_phone').length) {
        var phone_number_pattern = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
        $('#' + settings.formId).find('.chk_phone').each(function () {
            if (!phone_number_pattern.test($.trim($(this).val()))) {
                validation.push('false');
                $(this).closest('.' + settings.containerClass).addClass(settings.errorClass);
                $(this).closest('.' + settings.containerClass).find('.' + settings.errorMsgClass).html(settings.phoneValidationMsg);
            } else {
                validation.push('true');
                if ($(this).closest('.' + settings.containerClass).hasClass(settings.errorClass)) {
                    $(this).closest('.' + settings.containerClass).removeClass(settings.errorClass);
                } else {
                    /* do nothing */
                }
            }
        });
    }
    ;
    /* phone number validation starts */


    /* phone number validation if filled starts */

    if ($('.chk_phone_filled').length) {
        var phone_number_pattern = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
        $('#' + settings.formId).find('.chk_phone_filled').each(function () {
            if ($.trim($(this).val()) !== '') {
                if (!phone_number_pattern.test($.trim($(this).val()))) {
                    validation.push('false');
                    $(this).closest('.' + settings.containerClass).addClass(settings.errorClass);
                    $(this).closest('.' + settings.containerClass).find('.' + settings.errorMsgClass).html(settings.phoneValidationMsg);
                } else {
                    validation.push('true');
                    if ($(this).closest('.' + settings.containerClass).hasClass(settings.errorClass)) {
                        $(this).closest('.' + settings.containerClass).removeClass(settings.errorClass);
                    } else {
                        /* do nothing */
                    }
                }
            } else {
                validation.push('true');
                if ($(this).closest('.' + settings.containerClass).hasClass(settings.errorClass)) {
                    $(this).closest('.' + settings.containerClass).removeClass(settings.errorClass);
                } else {
                    /* do nothing */
                }
            }


        });
    }
    ;
    /* phone number validation if filled starts */



    if ($('.chk_postcode').length) {
        //var lenth=$('.chk_postcode').text(input.val().length);
        var lenth = $('.chk_postcode').val().length;
        var postcode_number_pattern = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
        $('#' + settings.formId).find('.chk_postcode').each(function () {
            if (!postcode_number_pattern.test($.trim($(this).val()))) {
                validation.push('false');
                $(this).closest('.' + settings.containerClass).addClass(settings.errorClass);
                $(this).closest('.' + settings.containerClass).find('.' + settings.errorMsgClass).html(settings.postcodeValidationMsg);
            } else if (lenth > 6) {
                validation.push('false');
                $(this).closest('.' + settings.containerClass).addClass(settings.errorClass);
                $(this).closest('.' + settings.containerClass).find('.' + settings.errorMsgClass).html(settings.postcodeValidationMsg);
            } else {
                validation.push('true');
                if ($(this).closest('.' + settings.containerClass).hasClass(settings.errorClass)) {
                    $(this).closest('.' + settings.containerClass).removeClass(settings.errorClass);
                } else {
                    /* do nothing */
                }
            }
        });
    }
    ;
    /* radio button validation starts */

    if ($('.chk_radio').length) {
        $('#' + settings.formId).find('.chk_radio').each(function () {
            if ($('.chk_radio:checked').length == 0) {
                validation.push('false');
                $(this).closest('.' + settings.containerClass).addClass(settings.errorClass);
                $(this).closest('.' + settings.containerClass).find('.' + settings.errorMsgClass).html(settings.radioButtonValidationMsg);
            }
            else {
                validation.push('true');
                if ($(this).closest('.' + settings.containerClass).hasClass(settings.errorClass)) {
                    $(this).closest('.' + settings.containerClass).removeClass(settings.errorClass);
                } else {
                    /* do nothing */
                }
            }
        });
    }
    ;
    /* teachers age validation starts */

    if ($('.chk_thrsage').length) {
        var dob = '';
        $('#' + settings.formId).find('.chk_thrsage').each(function () {
            dob = $('.chk_thrsage').val();
            var now = new Date();
            var past = new Date(dob);
            var nowYear = now.getFullYear();
            var pastYear = past.getFullYear();
            var age = nowYear - pastYear;
            if (dob == '') {
                validation.push('false');
                $(this).closest('.' + settings.containerClass).addClass(settings.errorClass);
                $(this).closest('.' + settings.containerClass).find('.' + settings.errorMsgClass).html(settings.blankValidationMsg);
            }
            else if (age <= 18) {
                validation.push('false');
                $(this).closest('.' + settings.containerClass).addClass(settings.errorClass);
                $(this).closest('.' + settings.containerClass).find('.' + settings.errorMsgClass).html(settings.facultyAgeValidationMsg);
            } else {
                validation.push('true');
                if ($(this).closest('.' + settings.containerClass).hasClass(settings.errorClass)) {
                    $(this).closest('.' + settings.containerClass).removeClass(settings.errorClass);
                } else {
                    /* do nothing */
                }
            }
        });
    }
    ;




    /* alpha numeric validation starts */

    if ($('.chk_alphanumeric').length) {

        // regular expression for alphanumeric pattern
        var alphanumeric_pattern = /^[A-Za-z0-9]+$/;

        $('#' + settings.formId).find('.chk_alphanumeric').each(function () {
            if (!alphanumeric_pattern.test($.trim($(this).val()))) {
                validation.push('false');
                $(this).closest('.' + settings.containerClass).addClass(settings.errorClass);
                $(this).closest('.' + settings.containerClass).find('.' + settings.errorMsgClass).html(settings.alphanumericValidationMsg);
            } else {
                validation.push('true');
                if ($(this).closest('.' + settings.containerClass).hasClass(settings.errorClass)) {
                    $(this).closest('.' + settings.containerClass).removeClass(settings.errorClass);
                } else {
                    /* do nothing */
                }
            }
        });
    }
    ;



    /* alpha numeric validation ends */


    /* alpha numeric with dash validation starts */

    var alphanumeric_with_dash_pattern = /^[A-Za-z0-9-]+$/;

    if ($('.chk_alphanumeric_with_dash').length) {
        $('#' + settings.formId).find('.chk_alphanumeric_with_dash').each(function () {
            if (!alphanumeric_with_dash_pattern.test($.trim($(this).val()))) {
                validation.push('false');
                $(this).closest('.' + settings.containerClass).addClass(settings.errorClass);
                $(this).closest('.' + settings.containerClass).find('.' + settings.errorMsgClass).html(settings.alphanumericWithDashValidationMsg);
            } else {
                validation.push('true');
                if ($(this).closest('.' + settings.containerClass).hasClass(settings.errorClass)) {
                    $(this).closest('.' + settings.containerClass).removeClass(settings.errorClass);
                } else {
                    /* do nothing */
                }
            }
        });
    }
    ;
    /* numeric validation starts */

    var numeric_pattern = /^[0-9]$/;

    if ($('.chk_numeric').length) {
        $('#' + settings.formId).find('.chk_numeric').each(function () {
            if (!$.isNumeric($.trim($(this).val()))) {
                validation.push('false');
                $(this).closest('.' + settings.containerClass).addClass(settings.errorClass);
                $(this).closest('.' + settings.containerClass).find('.' + settings.errorMsgClass).html(settings.numericValidationMsg);
            } else {
                validation.push('true');
                if ($(this).closest('.' + settings.containerClass).hasClass(settings.errorClass)) {
                    $(this).closest('.' + settings.containerClass).removeClass(settings.errorClass);
                } else {
                    /* do nothing */
                }
            }
        });
    }
    ;

    /* alpha numeric with dash validation ends */


    console.log(validation);

    return validation;

};

function check_validation() {
    var chk_validation = $('#add_data').pkvalidateform();
}
;

