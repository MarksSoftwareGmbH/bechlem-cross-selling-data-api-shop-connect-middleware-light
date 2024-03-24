/* 
 * MIT License
 *
 * Copyright (c) 2018-present, Marks Software GmbH (https://www.marks-software.de/)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
var Users = function() {
	return {
		init: function() {
			/*
			 *  Jquery Validation, Check out more examples and documentation at https://github.com/jzaefferer/jquery-validation
			 */

			/* Register form - Initialize Validation */
            $('.form-register').validate({
                errorClass: 'help-block animation-slideDown', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-input > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-input').removeClass('valid error').addClass('error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    e.closest('.form-input').removeClass('valid error');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'data[username]': {
                        required: true,
                        minlength: 4
                    },
                    'data[password]': {
                        required: true,
                        minlength: 6
                    },
                    'data[verify_password]': {
                        required: true,
                        minlength: 6,
                        equalTo: '#UserPassword'
                    },
                    'data[email]': {
                        required: true
                    },
                    'data[name]': {
                        required: true,
                        minlength: 4
                    }
                }
            });

			/* Login form - Initialize Validation */
			$('.form-login').validate({
				errorClass: 'help-block animation-slideDown', // You can change the animation class for a different entrance animation - check animations page
				errorElement: 'div',
				errorPlacement: function(error, e) {
					e.parents('.form-input > div').append(error);
				},
				highlight: function(e) {
					$(e).closest('.form-input').removeClass('valid error').addClass('error');
					$(e).closest('.help-block').remove();
				},
				success: function(e) {
					e.closest('.form-input').removeClass('valid error');
					e.closest('.help-block').remove();
				},
				rules: {
					'data[username]': {
						required: true,
                        minlength: 4
					},
					'data[password]': {
						required: true,
						minlength: 6
					}
				}
			});

			/* Reminder form - Initialize Validation */
			$('.form-forgot').validate({
				errorClass: 'help-block animation-slideDown', // You can change the animation class for a different entrance animation - check animations page
				errorElement: 'div',
				errorPlacement: function(error, e) {
					e.parents('.form-input > div').append(error);
				},
				highlight: function(e) {
					$(e).closest('.form-input').removeClass('valid error').addClass('valid');
					$(e).closest('.help-block').remove();
				},
				success: function(e) {
					e.closest('.form-input').removeClass('valid error');
					e.closest('.help-block').remove();
				},
				rules: {
					'data[email]': {
						required: true
					}
				}
			});

			/* Reset form - Initialize Validation */
			$('.form-reset').validate({
				errorClass: 'help-block animation-slideDown', // You can change the animation class for a different entrance animation - check animations page
				errorElement: 'div',
				errorPlacement: function(error, e) {
					e.parents('.form-input > div').append(error);
				},
				highlight: function(e) {
					$(e).closest('.form-input').removeClass('valid error').addClass('error');
					$(e).closest('.help-block').remove();
				},
				success: function(e) {
					if (e.closest('.form-input').find('.help-block').length === 2) {
						e.closest('.help-block').remove();
					} else {
						e.closest('.form-input').removeClass('valid error');
						e.closest('.help-block').remove();
					}
				},
				rules: {
					'data[password]': {
						required: true,
						minlength: 6
					},
					'data[verify_password]': {
						required: true,
						minlength: 6,
						equalTo: '#UserPassword'
					}
				}
			});
		}
	};
}();
