/*!function(a){a.fn.datepicker.dates.id={days:["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"],daysShort:["Mgu","Sen","Sel","Rab","Kam","Jum","Sab"],daysMin:["Mg","Sn","Sl","Ra","Ka","Ju","Sa"],months:["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"],monthsShort:["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"],today:"Hari Ini",clear:"Kosongkan"}}(jQuery);*/

(function ($) {
    'use strict';

    $(function () {

        // Submenu Dropdown
        $('.submenu-dropdown').click(function () {
            $(this).attr('aria-expanded', function (i, attr) {
                return attr == 'true' ? 'false' : 'true';
            });
            $(this).next().toggleClass('show');
        });
        // End Submenu Dropdown

        initAllElement();

        // Submit Button
        $('button[type="submit"]').click(function () {
            var form = $(this).parents('form');

            if (typeof form.attr('no-validator') != 'undefined') {

            } else if ($(form).valid()) {
                var btn = $(this).ladda();
                btn.ladda('start');
                form.submit();
            } else {

            }
        });
        // End Submit Button


        // Form Validate
        $("#form").validate({
            success: 'valid',
            ignore: '.ignore',
            errorPlacement: function (label, element) {
                var formGroup = $(element).parents('.form-group');

                label.addClass('mt-2 text-danger');
                formGroup.append(label);
            },
            highlight: function (element, errorClass) {
                $(element).parents('.form-group').addClass('has-danger');
                $(element).addClass('form-control-danger');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-danger');
                $(element).removeClass('form-control-danger');
            }
        });
        // End Form Validate


        // Data Table
        /*$('.datatable').DataTable({
            "order": [],
            stateLoadParams: function(settings, data ) {
                if (data.order) delete data.order;
            },
            "aLengthMenu": [
                [5, 10, 15, -1],
                [5, 10, 15, "All"]
            ],
            "iDisplayLength": 10,
            "language": {
                search: ""
            },
            stateSave: true
        });

        $('.datatable').each(function () {
            var datatable = $(this);
            // SEARCH - Add the placeholder for Search and Turn this into in-line form control
            var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
            search_input.attr('placeholder', 'Search');
            search_input.removeClass('form-control-sm');
            // LENGTH - Inline-Form control
            var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
            length_sel.removeClass('form-control-sm');
        });*/
        // End Data Table


        if ($('.btn-add-row').length > 0) {
            initPanelSection();

            $('.btn-add-row').click(function () {
                var parent = $(this).parents('.panel-section-wrapper');
                var sectionTemplateHTML = parent.find('.panel-section-template').first().html();
                var sectionContent = parent.find('.panel-list-wrapper').first();

                sectionContent.append(sectionTemplateHTML);
                var listSize = parent.find('.panel-list-wrapper .panel-section').length;
                sectionContent.find('.panel-section .panel-title h4').last().html(listSize);

                parent.find('.panel-list-wrapper .panel-section').last().find('[name]').each(function () {
                    var formName = $(this).attr('name');
                    formName = formName.replace("-1",(listSize-1));
                    $(this).attr('name', formName);
                });
                initPanelSection();
                initAllElement();
            });
        }



        function initPanelSection() {
            $('.remove-panel').click(function () {
                var parent = $(this).parents('.panel-section-wrapper');
                var panel = $(this).parents('.panel-section');
                var deletedIndex = Number(panel.find('h4').html()) - 1;
                var count = 1;

                parent.find('.panel-list-wrapper .panel-section').each(function (index) {
                    if (index == deletedIndex) return;

                    $(this).find('h4').html(count);
                    $(this).find('[name]').each(function(){
                        var formName = $(this).attr('name');
                        formName = formName.replace("["+index+"]","["+(count-1)+"]");
                        $(this).attr('name', formName);
                    });

                    count++;
                });


                panel.remove();
            })
        }
    });
})(jQuery);

function showImage(src, target) {
    if (src == null || target == null) return;
    var fr = new FileReader();
    // when image is loaded, set the src of the image where you want to display it
    fr.onload = function (e) {
        target.src = this.result;
    };
    src.addEventListener("change", function () {
        // fill fr with image data

        if (src.files.length > 0){
            if (src.files[0].size > $(src).data('image-limit')){
                $(src).val('');
                iziModalError('The file size exceeds the limit allowed of '+$(src).data('image-limit'));
                return;
            }
        };
        if ($(src).attr('name') == '' || !$(src).attr('name')) {
            var hidden = $(src).parent().find('input[type=hidden]').first();
            $(src).attr('name', hidden.attr('name'));
            hidden.remove();
        }
        triggerImageMultipleWrapper(src);

        fr.readAsDataURL(src.files[0]);
    });
}
function triggerImageMultipleWrapper(src) {
    var imageMultipleWrapper = $(src).parent().parent();
    if (imageMultipleWrapper.hasClass('image-multiple-wrapper')) {
        var imageSelectorWrapperLast = imageMultipleWrapper.find('.image-selector-wrapper').last();

        if ($(src).parent().index() == ( imageMultipleWrapper.find('.image-selector-wrapper').length - 1)) {
            imageMultipleWrapper.append(imageSelectorWrapperLast.clone(true));
        }

        var imageSelectorWrapperFirst = imageMultipleWrapper.find('.image-selector-wrapper').first();
        imageSelectorWrapperLast = imageMultipleWrapper.find('.image-selector-wrapper').last();
        imageSelectorWrapperLast.html(imageSelectorWrapperLast.html());
        imageSelectorWrapperLast.find('> .image-delete-icon').first().hide();

        addUploadDeleteFunction(imageSelectorWrapperLast.find('.btn-upload').first());
        var btnUploadFile = $(imageMultipleWrapper.find('.image-selector-wrapper').get(imageMultipleWrapper.find('.image-selector-wrapper').length - 1)).find('.btn-upload-file').first();

        var name = imageSelectorWrapperFirst.find('[name]').first().attr('name');
        var newIndex = (imageMultipleWrapper.find('.image-selector-wrapper').length - 1);

        if (name.slice(-1) == ']'){
            name = name.substring(0, name.length - 2) + newIndex + ']';
        } else {
            name = name.substring(0, name.length - 1) + newIndex;
        }
        btnUploadFile.attr('name', name);
    }
    $(src).parent().find('> button > .upload-section').hide();
    $(src).parent().find('> .image-delete-icon').show();
}
function addUploadDeleteFunction(uploadButton) {
    uploadButton.click(function () {
        $(this).parent().find('input.btn-upload-file').first().click();
    });
    var fileElement = uploadButton.parent().find('input.btn-upload-file').first()[0];
    var imageElement = uploadButton.find('img').first()[0];
    var uploadInstructionElement = uploadButton.find('div').first();
    showImage(fileElement, imageElement);

    //delete function
    uploadButton.parent().find(' > .image-delete-icon').first().on('click', function () {
        $(fileElement).val('');
        $(imageElement).attr('src', '');
        uploadInstructionElement.show();
        $(this).hide();

        var name = $(fileElement).attr('name');
        if (name) {
            uploadButton.parent().append('<input type="hidden" name="' + $(fileElement).attr('name') + '" value="DELETE_IMAGE">');
            $(fileElement).attr('name', '');
        } else {
            uploadButton.parent().find('input[type=hidden]').first().val('DELETE_IMAGE');
        }
    });

}

function initAllElement() {

    // Text Editor
    if ($(".text-editor").length) {
        tinymce.init({
            selector: '.text-editor',
            height: 500,
            resize: true,
            theme: 'modern',
            menubar:false,
            branding: false,
            plugins: [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc help'
            ],
            toolbar1: 'undo redo | insert | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | help',
            image_advtab: true,
            content_css: []
        });
    }
    // End Text Editor

    // Select 2
    if ($('.select-custom').length) {
        $('.select-custom').select2();
    }
    // End Select 2


    // Timepicker
    if ($(".timepicker").length) {
        $('.timepicker').datetimepicker({
            format: 'HH:mm',
        });
    }
    // End Timepicker


    // Color Picker
    if ($(".color-picker").length) {
        $('.color-picker').asColorPicker();
    }
    // End Color Picker


    // Rating Picker
    $('.rating-picker').barrating('show', {
        theme: 'bars-1to10'
    });
    // End Rating Picker

    // Datepicker
    if ($('.datepicker').length) {
        $('.datepicker').datepicker({
            todayBtn: "linked",
            enableOnReadonly: true,
            todayHighlight: true,
            toggleActive: true,
            format: "yyyy-mm-dd",
        });
    }
    // End Datepicker

    // Image Upload
    if ($('.btn-upload').length) {
        $('.btn-upload').each(function () {
            $(this).off();
            addUploadDeleteFunction($(this));
        });
    }
    // End Image Upload

    // File Input
    $('.file-change').click(function (e) {
        e.preventDefault();

        $(this).parent().find('[type=hidden]').val('DELETE_FILE');

        $(this).parent().toggleClass('hide');
        $(this).parent().next().toggleClass('hide');
    })
    // File Input

    // AutoNumeric
    if ($('.autonumeric').length) {
        $('.autonumeric').off();

        var autonumericOptions = {
            vMax: '9999999999999',
            digitGroupSeparator: '.',
            decimalPlacesOverride: 2,
            decimalCharacter  : ',',
            unformatOnSubmit : true
        };

        $('.autonumeric').each(function () {
            $(this).autoNumeric('init', autonumericOptions);
        })

        $('.autonumeric').change(function(){
            $(this).parent().find('.autonumericvalue').first().val( $(this).autoNumeric('get') );
        })
    }
    // AutoNumeric

}

// default modal confirmation
$(document).on('click', '.clickable-row', function(e) {
    'use strict';
    if ($(this).data('href')) {
        window.location = $(this).data('href');
    }
});

$(document).on('click','.btn-default-confirmation', function(e){
    'use strict';
    e.stopPropagation();
    var modalConfirmation = $('#modal-default-confirmation');
    var dataHref = $(this).data('href');

    modalConfirmation.find('.btn-submit').attr('href', dataHref);

    modalConfirmation.modal({
        backdrop: 'static'
    });
});
// end default modal confirmation

showNotification = function(type, title, message, position) {
    'use strict';
    resetToastPosition();

    $.toast({
        heading: String(title),
        text: String(message),
        position: String(position),
        icon: String(type),
        showHideTransition: 'slide',
        stack: false,
        loaderBg: '#f96868'
    })
}

resetToastPosition = function() {
    $('.jq-toast-wrap').removeClass('bottom-left bottom-right top-left top-right mid-center'); // to remove previous position class
    $(".jq-toast-wrap").css({
        "top": "",
        "left": "",
        "bottom": "",
        "right": ""
    }); //to remove previous position style
}
