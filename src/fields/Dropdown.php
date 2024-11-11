<?php

namespace lewisjenkins\craftdynamicfields\fields;

use lewisjenkins\craftdynamicfields\CraftDynamicFields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;
use craft\helpers\Cp;

class Dropdown extends Field
{
     
    public $dropdownOptions = '';
    public $columnType = 'text';

    public static function displayName(): string
    {
        return Craft::t('craft-dynamic-fields', 'Dropdown (dynamic)');
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate(
            'craft-dynamic-fields/_components/fields/Dropdown_settings',
            [
                'field' => $this,
            ]
        );
    }
    
    public function getContentColumnType(): string
    {
        return $this->columnType;
    }

    public function normalizeValue($value, ElementInterface $element = null): string
    {	
		$view = Craft::$app->getView();
		$templateMode = $view->getTemplateMode();
		$view->setTemplateMode($view::TEMPLATE_MODE_SITE);

		$variables['element'] = $element;
		$variables['this'] = $this;
		
		$options = json_decode('[' . $view->renderString($this->dropdownOptions, $variables) . ']', true);
		
		$view->setTemplateMode($templateMode);
		
		if (!$value && $this->isFresh($element) ) :
			foreach ($options as $key => $option) :
				if (!empty($option['default'])) :
					$value = $option['value'];
				endif;
			endforeach;
		endif;
		
		return (is_null($value) ? '' : $value);
    }
    
    public function getInputHtml($value, ElementInterface $element = null): string
    {
	$view = Craft::$app->getView();
	$templateMode = $view->getTemplateMode();
	$view->setTemplateMode($view::TEMPLATE_MODE_SITE);

	$variables['element'] = $element;
	$variables['this'] = $this;
	
	$options = json_decode('[' . $view->renderString($this->dropdownOptions, $variables) . ']', true);
	
	$view->setTemplateMode($templateMode);
		
	return Cp::selectizeHtml([
            'id' => $this->getInputId(),
            'describedBy' => $this->describedBy,
            'name' => $this->handle,
            'value' => $value,
            'options' => $options,
        ]);
    }
}
