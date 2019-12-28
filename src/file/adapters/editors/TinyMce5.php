<?php

namespace ant\file\adapters\editors;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * TinyMCE helper
 */
class TinyMce5
{
    /**
     * Callback for TinyMCE 5 file_picker_callback
     * @param array|string $url Url to TinyMCEAction
     * @param array $popupSettings TinyMCE popup settings
     * @param \yii\web\View|null $view
     * @return JsExpression
     */
    public static function getFilePickerCallback($url, $popupSettings = [], $view = null)
    {
        $default = [
            'title' => 'elFinder',
            'width' => 900,
            'height' => 500,
        ];

        $settings = array_merge($default, $popupSettings);
        $settings['file'] = Url::to($url);

        $encodedSettings = Json::htmlEncode($settings);

        if ($view === null) {
            $view = Yii::$app->view;
        }
        //\alexantr\elfinder\HelperAsset::register($view);
		$view->registerJs('
			elFinder = {
				filePickerCallback: function (settings) {
					return function (callback, value, meta) {
						// append filter query param
						var separator = settings.file.indexOf("?") !== -1 ? "&" : "?";
						if (meta.filetype === "image") {
							settings.url = settings.file + separator + "filter=image";
						} else if (meta.filetype === "media") {
							settings.url = settings.file + separator + "filter=" + encodeURIComponent("audio,video");
						} else {
							settings.url = settings.file;
						}
						settings.onAction = function () {
						};
						settings.onClose = function() {
						};
						settings.onMessage = function(dialog, message) {
							//alert("message");
							console.log("message");
							console.log(dialog);
							var url = message.file.url, reg = /\/[^/]+?\/\.\.\//;
							while (url.match(reg)) {
								url = url.replace(reg, "/");
							}
							console.log(url);
							callback(url);
							dialog.close();
						};
						tinymce.activeEditor.windowManager.openUrl(settings, {});
						return false;
					}
				}
			};
		');

        return new JsExpression("elFinder.filePickerCallback($encodedSettings)");
    }
}
