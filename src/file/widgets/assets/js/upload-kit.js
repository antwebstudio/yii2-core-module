/*!
 * Yii2 File Kit library
 * http://github.com/yii2-starter-kit/yii2-file-kit
 *
 * Author: Eugine Terentev <eugine@terentev.net>
 *
 * Date: 2014-05-01T17:11Z
 */
(function( $ ) {
    jQuery.fn.yiiUploadKit = function(options) {
        var $input = this;
        var $container = $input.parent('div');
        var $files = $('<ul>', {"class":"files"}).insertBefore($input);
        var $emptyInput = $container.find('.empty-value');

        var methods = {
            init: function(){
                if (options.multiple) {
                    $input.attr('multiple', true);
                    $input.attr('name', $input.attr('name') + '[]');
                }
                $container.addClass('upload-kit');
                if (options.sortable) {
                    $files.sortable({
                        placeholder: "upload-kit-item sortable-placeholder",
                        tolerance: "pointer",
                        forcePlaceholderSize: true,
                        update: function () {
                            methods.updateOrder()
                        }
                    })
                }
                $input.wrapAll($('<li class="upload-kit-input"></div>'))
                    .after($('<span class="glyphicon glyphicon-plus-sign ' + options.plusIcon + ' add"></span>'))
                    .after($('<span class="glyphicon glyphicon-circle-arrow-down ' + options.downIcon + 'drag"></span>'))
                    .after($('<span/>', {"data-toggle":"popover", "class":"glyphicon glyphicon-exclamation-sign error-popover"}))
                    .after(
                    '<div class="progress">'+
                    '<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>'+
                    '</li>'
                );
                $files.on('click', '.upload-kit-item .remove', methods.removeItem);
                methods.checkInputVisibility();
                methods.fileuploadInit();
                methods.dragInit();
                if (options.acceptFileTypes && !(options.acceptFileTypes instanceof RegExp)) {
                    options.acceptFileTypes = new RegExp(eval(options.acceptFileTypes))
                }

            },
            fileuploadInit: function(){
                var $fileupload = $input.fileupload({
                    name: options.name || 'file',
                    url: options.url,
                    dropZone: $input.parents('.upload-kit-input'),
                    dataType: 'json',
                    singleFileUploads: false,
                    multiple: options.multiple,
                    maxNumberOfFiles: options.maxNumberOfFiles,
                    maxFileSize: options.maxFileSize, // 5 MB
                    acceptFileTypes: options.acceptFileTypes,
                    minFileSize: options.minFileSize,
                    messages: options.messages,
                    process: true,
                    getNumberOfFiles: methods.getNumberOfFiles,
                start: function (e, data) {
                        $container.find('.upload-kit-input')
                                .removeClass('error')
                                .addClass('in-progress');
                        $input.trigger('start');
                        if (options.start !== undefined) options.start(e, data);
                    },
                    processfail: function(e, data) {
                        if (data.files.error) {
                            methods.showError(data.files[0].error);
                        }
                    },
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $container.find('.progress-bar').attr('aria-valuenow', progress).css(
                            'width',
                            progress + '%'
                        ).text(progress + '%');
                    },
                    done: function (e, data) {
                        $.each(data.result.files, function (index, file) {
                            if (!file.error) {
                                var item = methods.createItem(file);
                                item.appendTo($files);
                            } else {
                                methods.showError(file.errors)
                            }

                        });
                        methods.handleEmptyValue();
                        methods.checkInputVisibility();
                        $input.trigger('done');
                        if (options.done !== undefined) options.done(e, data);
                    },
                    fail: function (e, data) {
                        methods.showError(data.errorThrown);
                        if (options.fail !== undefined) options.fail(e, data);
                    },
                    always: function (e, data) {
                        $container.find('.upload-kit-input').removeClass('in-progress');
                        $input.trigger('always');
                        if (options.always !== undefined) options.always(e, data);
                    }

                });
                if (options.files) {
                    options.files.sort(function(a, b){
                        return parseInt(a.order) - parseInt(b.order);
                    });
                    $fileupload.fileupload('option', 'done').call($fileupload, $.Event('done'), {result: {files: options.files}});
                    methods.handleEmptyValue();
                    methods.checkInputVisibility();
                }
            },
            dragInit: function(){
                $(document).on('dragover', function ()
                {
                    $('.upload-kit-input').addClass('drag-highlight');
                });
                $(document).on('dragleave drop', function ()
                {
                    $('.upload-kit-input').removeClass('drag-highlight');
                });
            },
            showError: function(error){
                if ($.fn.popover) {
                    $container.find('.error-popover').attr('data-content', error).popover({html:true,trigger:"hover"});
                }
                $container.find('.upload-kit-input').addClass('error');
            },
            removeItem: function(e){
                var $this = $(this);
                var url = $this.data('url');
                if (url) {
                    $.ajax({
                        url: url,
                        type: 'DELETE'
                    })
                }
                $this.parents('.upload-kit-item').remove();
                methods.handleEmptyValue();
                methods.checkInputVisibility();
            },
			getValue: function(json, name) {
				//console.log('getvalue: '+name);
				//console.log('value: '+jsonPath(json, '$.' + name));
				var value = jsonPath(json, '$.' + name);
				if (value !== false) return value;
			},
            createItem: function(file){
                var name = options.name;
                var index = methods.getNewItemIndex();
                if (options.multiple) {
                    name += '[' + index + ']';
                }

                // Init Item
                var item = $('<li>', {"class": "upload-kit-item done", "value": index});
				
				// Core fields
                item.append($('<input/>', {"name": name + '[' + options.pathAttributeName + ']', "value": file[options.pathAttribute], "type":"hidden"}))
                    .append($('<input/>', {"name": name + '[name]', "value": file.name, "type":"hidden"}))
                    .append($('<input/>', {"name": name + '[size]', "value": file.size, "type":"hidden"}))
                    .append($('<input/>', {"name": name + '[type]', "value": file.type, "type":"hidden"}))
                    .append($('<input/>', {"name": name + '[order]', "value": file.order, "type":"hidden", "data-role": "order"}))
                    .append($('<input/>', {"name": name + '[' + options.baseUrlAttributeName + ']', "value": file[options.baseUrlAttribute], "type":"hidden"}))
                    .append($('<span/>', {
                        "class": "name",
                        "title": file.name,
                        "text": options.showPreviewFilename ? file.name : null
                    }));
                    
                // Custom fields
                if (options.fields) {
                    var widgetId = $input.attr('id');
                    var customFields = $('<div/>', {"id": "widget-" + widgetId + "-" + index, "class": "upload-custom-field-group"});

                    for (var i in options.fields) {
                        var field = options.fields[i];
                        var fieldAttributes = Object.assign({}, field);
                        var tag = field.tag === undefined ? 'input' : field.tag;
                        var formField = $('<div/>', {"class": "form-group"});
                        
                        fieldAttributes.value = methods.getValue(file, fieldAttributes.name);
                        fieldAttributes.name = name + fieldAttributes.name;

                        formField.append($('<label/>').append(fieldAttributes.label));
                        
                        delete fieldAttributes.tag;
                        delete fieldAttributes.label;

                        formField.append($('<' + tag + '/>', fieldAttributes));
                        customFields.append(formField);
                    }
                    item.append(customFields);
                }
				
				// Buttons
				var buttons = $('<span/>', {"class": "btn-panel"});
				if (options.buttons) {
					for (var i in options.buttons) {
						var buttonOptions = options.buttons[i];
                        var button = $('<span/>', buttonOptions);
                        button.data('index', index)
						button.on(buttonOptions.events);
						buttons.append(button);
					}
				} else {
						buttons.append($('<span/>', {"class": "glyphicon glyphicon-remove-circle " + options.removeIcon + " remove", "data-url": file.delete_url}));
				}
                
                item.append(buttons);

                // Thumbnail
                if ((!file.type || file.type.search(/image\/.*/g) !== -1) && options.previewImage) {
                    item.removeClass('not-image').addClass('image');
                    item.prepend($('<img/>', {src: file[options.baseUrlAttribute] + '/' +file[options.pathAttribute]}));
                    item.find('span.type').text('');
                } else {
                    item.removeClass('image').addClass('not-image');
                    item.css('backgroundImage', '');
                    item.find('span.name').text(file.name);
                }
                return item;
            },
            checkInputVisibility: function(){
                var inputContainer = $container.find('.upload-kit-input');
                if (options.maxNumberOfFiles && (methods.getNumberOfFiles() >= options.maxNumberOfFiles)) {
                    inputContainer.hide();
                } else {
                    inputContainer.show();
                }
            },
            handleEmptyValue: function(){
                if (methods.getNumberOfFiles() > 0) {
                    $emptyInput.val(methods.getNumberOfFiles())
                } else {
                    $emptyInput.removeAttr('value');
                }
            },
            getNumberOfFiles: function() {
                return $container.find('.files .upload-kit-item').length;
            },
            getNewItemIndex: function () {
                var existingIndexes = []
                $container.find('.files .upload-kit-item').each(function () {
                existingIndexes.push($(this).val());
                })
                return existingIndexes.length ? (Math.max.apply(Math, existingIndexes)+1) : 0;
          },
            updateOrder: function () {
                $files.find('.upload-kit-item').each(function(index, item){
                    $(item).find('input[data-role=order]').val(index);
                })
            }
        };

        methods.init.apply(this);
        return this;
    };

})(jQuery);

/* JSONPath 0.8.0 - XPath for JSON
 *
 * Copyright (c) 2007 Stefan Goessner (goessner.net)
 * Licensed under the MIT (MIT-LICENSE.txt) licence.
 */
function jsonPath(obj, expr, arg) {
   var P = {
      resultType: arg && arg.resultType || "VALUE",
      result: [],
      normalize: function(expr) {
         var subx = [];
         return expr.replace(/[\['](\??\(.*?\))[\]']/g, function($0,$1){return "[#"+(subx.push($1)-1)+"]";})
                    .replace(/'?\.'?|\['?/g, ";")
                    .replace(/;;;|;;/g, ";..;")
                    .replace(/;$|'?\]|'$/g, "")
                    .replace(/#([0-9]+)/g, function($0,$1){return subx[$1];});
      },
      asPath: function(path) {
         var x = path.split(";"), p = "$";
         for (var i=1,n=x.length; i<n; i++)
            p += /^[0-9*]+$/.test(x[i]) ? ("["+x[i]+"]") : ("['"+x[i]+"']");
         return p;
      },
      store: function(p, v) {
         if (p) P.result[P.result.length] = P.resultType == "PATH" ? P.asPath(p) : v;
         return !!p;
      },
      trace: function(expr, val, path) {
         if (expr) {
            var x = expr.split(";"), loc = x.shift();
            x = x.join(";");
            if (val && val.hasOwnProperty(loc))
               P.trace(x, val[loc], path + ";" + loc);
            else if (loc === "*")
               P.walk(loc, x, val, path, function(m,l,x,v,p) { P.trace(m+";"+x,v,p); });
            else if (loc === "..") {
               P.trace(x, val, path);
               P.walk(loc, x, val, path, function(m,l,x,v,p) { typeof v[m] === "object" && P.trace("..;"+x,v[m],p+";"+m); });
            }
            else if (/,/.test(loc)) { // [name1,name2,...]
               for (var s=loc.split(/'?,'?/),i=0,n=s.length; i<n; i++)
                  P.trace(s[i]+";"+x, val, path);
            }
            else if (/^\(.*?\)$/.test(loc)) // [(expr)]
               P.trace(P.eval(loc, val, path.substr(path.lastIndexOf(";")+1))+";"+x, val, path);
            else if (/^\?\(.*?\)$/.test(loc)) // [?(expr)]
               P.walk(loc, x, val, path, function(m,l,x,v,p) { if (P.eval(l.replace(/^\?\((.*?)\)$/,"$1"),v[m],m)) P.trace(m+";"+x,v,p); });
            else if (/^(-?[0-9]*):(-?[0-9]*):?([0-9]*)$/.test(loc)) // [start:end:step]  phyton slice syntax
               P.slice(loc, x, val, path);
         }
         else
            P.store(path, val);
      },
      walk: function(loc, expr, val, path, f) {
         if (val instanceof Array) {
            for (var i=0,n=val.length; i<n; i++)
               if (i in val)
                  f(i,loc,expr,val,path);
         }
         else if (typeof val === "object") {
            for (var m in val)
               if (val.hasOwnProperty(m))
                  f(m,loc,expr,val,path);
         }
      },
      slice: function(loc, expr, val, path) {
         if (val instanceof Array) {
            var len=val.length, start=0, end=len, step=1;
            loc.replace(/^(-?[0-9]*):(-?[0-9]*):?(-?[0-9]*)$/g, function($0,$1,$2,$3){start=parseInt($1||start);end=parseInt($2||end);step=parseInt($3||step);});
            start = (start < 0) ? Math.max(0,start+len) : Math.min(len,start);
            end   = (end < 0)   ? Math.max(0,end+len)   : Math.min(len,end);
            for (var i=start; i<end; i+=step)
               P.trace(i+";"+expr, val, path);
         }
      },
      eval: function(x, _v, _vname) {
         try { return $ && _v && eval(x.replace(/@/g, "_v")); }
         catch(e) { throw new SyntaxError("jsonPath: " + e.message + ": " + x.replace(/@/g, "_v").replace(/\^/g, "_a")); }
      }
   };

   var $ = obj;
   if (expr && obj && (P.resultType == "VALUE" || P.resultType == "PATH")) {
      P.trace(P.normalize(expr).replace(/^\$;/,""), obj, "$");
      return P.result.length ? P.result : false;
   }
} 
