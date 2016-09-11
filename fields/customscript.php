<?php
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldCustomscript extends JFormField {
 
	protected $type = 'customscript';
 
	// getLabel() left out
 
	public function getInput() {

		$app = JFactory::getApplication();
    	if ($app->isSite()){
    		return '
			<script src="/modules/mod_preflightslider/assets/js/editing.js"></script>
			<link href="/modules/mod_preflightslider/assets/css/editing.css" rel="stylesheet" />
			';
    	}

		return '';
	}
}