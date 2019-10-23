(function($){
	$.fn.attributev2 = function(method) {
		if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jquery.attributev2');
            return false;
        }
	};

	var events = {};

	var defaultOptions = {
        id: null,
        modelClass: null,
        modalId: null,
        formId: null,
        addButton: null,

        url: null,
        urlAction: null,

        defaultFieldtype: null,
    };

	var methods = {
        init: function(options) {
            if (typeof options !== 'object') {
                console.error('Options must be an object');
                return;
            }

            var settings = $.extend(true, {}, defaultOptions, options || {});
            var $wrapper = $('#' + settings.id);
            var $form = $wrapper.find('#' + settings.formId);

            $wrapper.data('attributev2', {
                settings: settings
            });

            initIU($wrapper);

            // $.each($wrapper.find('[data-field]'), function(i, field) {
            //     initField($(field));
            // });

            $wrapper.on('click.attributev2', 'button[attributev2-field-delete]', function(e) {
                e.stopPropagation();
                deleteField($(this));
            });

            $wrapper.on('click.attributev2', 'button[attributev2-field-update]', function(e) {
                e.stopPropagation();
                updateField($(this));
            });

            $wrapper.on('click.attributev2', '[data-toggle=modal]', function (e) {
                e.stopPropagation();
                showSettingFormModal($('#' + settings.modalId), $.extend({}, $(this).data('setting-form'), {
                    triggerButton: $(this)
                }));
            });

            $wrapper.on('change.attributev2', '#' + settings.modalId + ' [data-fieldtypes]', function (e) {
                e.stopPropagation();
                showSettingFormModal($('#' + settings.modalId), {
                    url: $(this).val(),
                    triggerButton: $('#' + settings.modalId).data('triggerButton'),
                    replaceTarget: $('#' + settings.modalId).data('replaceTarget'),
                    rememberFieldType: false
                });
            });

            $wrapper.on('beforeSubmit.attributev2', '#' + settings.modalId + ' form', function (e) {
                e.stopPropagation();
                createField($(this));
                return false;
            });

            $wrapper.on('submit.attributev2', '#' + settings.formId, function(e) {
                e.stopPropagation();

                if (!($form.find('[field-structure]').length > 0)) $form.prepend('<div field-structure></div>');

                var $fieldStructure = $form.find('[field-structure]');
                $fieldStructure.html('');

                $.each($form.find('[data-field]'), function(index, element){
                    var data = $(element).data('field');
                    var extraInput = $('<input />').attr('type', 'hidden')
                        .attr('name', data.name)
                        .attr('value', JSON.stringify(data.value));
                    $fieldStructure.append(extraInput);
                });

                return true;
            });

            $form.find('fieldset').sortable();

        },
        showSettingFormModal: function(options) {
            var data = $(this).data('attributev2');
            showSettingFormModal($('#' + data.settings.modalId), options);
        },
        hideSettingForm: function() {},
        remove: function() {},
        add: function() {},
        save: function() {}
    };

    var showSettingFormModal = function($modal, options) {
        var options = $.extend({}, {
            title: 'Field Setting Form',
            url: false,
            data: {},
            triggerButton: false,
            replaceTarget: false,
            rememberFieldType: true
        }, options);

        var $wrapper = $modal.closest('[attributev2]').first();
        var data = $wrapper.data('attributev2');
        var settings = data.settings;

        $modal.data('triggerButton', options.triggerButton);
        $modal.data('replaceTarget', options.replaceTarget);

        if (options.rememberFieldType)
        {
            var $triggerButton = $(options.triggerButton);
            var $field = $triggerButton.parent().find('[data-field]');
            var fieldData = $field.data('field');
            var $fieldtypeDropdown = $modal.find('[data-fieldtypes]');

            var fieldtype = options.replaceTarget ? fieldData.value.fieldtype : settings.defaultFieldtype;
            $fieldtypeDropdown.val($fieldtypeDropdown.find('[fieldtype=' + fieldtype.replace(/([$%&()*+,./:;<=>?@\[\\\]^\{|}~])/g,'\\$1') +  ']').attr('value'));
        }

        var $modalTitle = $modal.find('[data-title]');
        var $modalContent = $modal.find('[data-body-content]');
        var $modalFieldtypes = $modal.find('[data-fieldtypes]');

        $modalTitle.text(options.title);
        
        $.ajax({
            url: options.url,
            type: 'POST',
            data: options.data,
            beforeSend: function(){
                $modalFieldtypes.prop('disabled', true);
                $modalContent.html('Loading ...');
            },
            success: function(html) {
                $modalContent.html($(html).hide().fadeIn());
                $modalFieldtypes.prop('disabled', false);
                $modal.modal('show');
            },
            complete: function(){
                // setTimeout(function() {
                //     $modalContent.wrapInner('<div/>');
                //     var newheight = $('div:first', $modalContent).height();
                //     $modalContent.animate( {height: newheight} );
                // }, $modal.hasClass('in') ? 100 : 1000);
            },
            error: function() {}
        });
    };

    var hideSettingForm = function($modal) {
        $modal.modal('hide');
    };

    var createField = function($settingForm) {  

        var $wrapper = $settingForm.closest('[attributev2]').first();
        var data = $wrapper.data('attributev2');
        var settings = data.settings;
        var $form = $('#' + settings.formId);
        var $modal = $('#' + settings.modalId);
        var $triggerButton = $modal.data('triggerButton') ? $($modal.data('triggerButton')) : false;
        var $replaceTarget = $modal.data('replaceTarget') ? $($modal.data('replaceTarget')) : false;

        var formData = $settingForm.serializeArray();
        // formData.push({name: settings.urlAction.postKey, value: settings.urlAction.on.create});

        $.ajax({
            url: $settingForm.attr('action') + '&' + settings.urlAction.postKey + '=' + settings.urlAction.on.create,
            data: formData,
            type: 'POST',
            beforeSend: function(){},
            success: function(json){
                if (json.success)
                {
                    var html = $(json.response.html);
                    var $input = $(html.find('.form-group'));

                    if ($replaceTarget) {
                        var value = $replaceTarget.find('[data-field]').val();
                        $replaceTarget.replaceWith($input);
                        var $field = $input.find('[data-field]');
                        $field.val(value);
                        console.log(value);
                    } else {
                        $form.find('fieldset').append($input);
                    }

                    $input.wrap('<div class="row ui-sortable-handle"><div class="col-sm-12"></div></div>');

                    initIU($wrapper);

                    hideSettingForm($('#' + settings.modalId));
                }
            },
            error: function(){},
            complete: function(){}
        });
    };

    var deleteField = function ($btn) {
        if (confirm('Are you sure?')) {
            $btn.closest('.row').fadeOut('500', function () { $(this).remove(); });
        }
    };

    var updateField = function ($btn) {
        var $wrapper = $btn.closest('[attributev2]').first();
        var data = $wrapper.data('attributev2');
        var settings = data.settings;

        var $modal = $('#' + settings.modalId);
        var $field = $btn.parent().find('[data-field]');
        var data = $field.data('field');
        var $fieldtypeDropdown = $modal.find('[data-fieldtypes]');

        showSettingFormModal($modal, {
            url: settings.url + '?modelClass=' + settings.modelClass + '&fieldtype=' + data.value.fieldtype + '&name=' + data.value.name + '&' + settings.urlAction.postKey + '=' + settings.urlAction.on.load,
            data: {
                Attributev2: data.value
            },
            replaceTarget: $field.closest('.row'),
            triggerButton: $btn
        });
    };

    var initField = function ($field) {
        $label = $field.parent().find('label');

        var $btns = [
            $('<button type="button" class="btn btn-xs" attributev2-field-delete><i class="fa fa-trash"></i></button>'),
            $('<button type="button" class="btn btn-xs" attributev2-field-update><i class="fa fa-cog"></i></button>')
        ];

        $.each($btns, function (i, $btn){ $label.after($btn); });
    };

    var initIU = function ($wrapper) {

        $wrapper.find('[attributev1-ui]').remove();

        $.each($wrapper.find('[data-field]'), function(i, field) {
            $label = $(field).parent().find('label');

            var $btns = [
                $('<button type="button" class="btn btn-xs" attributev2-field-delete attributev1-ui><i class="fa fa-trash"></i></button>'),
                $('<button type="button" class="btn btn-xs" attributev2-field-update attributev1-ui><i class="fa fa-cog"></i></button>')
            ];

            $.each($btns, function (i, $btn){ $label.after($btn); });
        });

        // var $addBtn = $wrapper.find('[data-toggle=modal]');

        // $.each($wrapper.find('fieldset .row'), function(i, row) {
        //     var $row = $(row);
        //     $row.before($addBtn.clone().attr('attributev1-ui', true));
        // });
    };
}(jQuery));