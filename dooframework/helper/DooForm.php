<?php
/**
 * DooForm class file.
 * @package doo.helper
 * @author Milos Kovacki <kovacki@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/*
* DooForm class manage forms
*
* @author Milos Kovacki <kovacki@gmail.com>
* @copyright &copy; 2009 Milos Kovacki
* @license http://www.doophp.com/license
 * @package doo.helper
 * @since 1.3
*/
class DooForm extends DooValidator {

	/**
	* Form attributes
	* @var array
	*/
	protected $_attr = array();

	/**
	* Form decorators
	* @var array
	*/
	protected $_decorators = array();

	/**
	* Display groups for grouping elements
	* @var array
	*/
	protected $_displayGroups = array();

	/**
	* Element array
	* @var array
	*/
	protected $_elements = array();

	/**
	* Form elements
	* @var array
	*/
	protected $_formElements = array();

	/**
	* Form action
	* @var string
	*/
	protected $_action = "";

	/**
	* Form method
	* @var string
	*/
	protected $_method = "post";

	/**
	* Form validators
	* @var array
	*/
	protected $_validators = array();

	/**
	* Form errors
	* @var array
	*/
	protected $_errors = array();

	/**
	* Element values
	* @var array
	*/
	protected $_elementValues = array();


	/**
	* Class constructor
	*/
	public function __construct($form) {
		if (is_array($form)) {
			$this->setForm($form);
		} else {
			throw new DooFormException("Form must be set using array");
		}
	}

	/**
	* Set form construct it
	*/
	public function setForm($form) {
		// set method
		if (isset($form['method'])) {
			$this->_setMethod($form['method']);
		}
		if (isset($form['action'])) {
			$this->_setAction($form['action']);
		}
		if (isset($form['elements'])) {
			$this->_elements = $form['elements'];
		}
	}

	/**
	* Form rendering
	*
	* @return string Form html
	*/
	public function render() {
		$this->_addElements();
		$formElements = $this->_formElements;
		$errors = $this->_errors;
		$formHtml = '<form action="'.$this->_action . '" method="'.$this->_method.'" class="doo-form">';
		foreach ($formElements as $element => $e) {
			$formHtml .= $formElements[$element];
			if ((count($this->_errors) > 0) && (isset($errors[$element]))) {
				$formHtml .= '<ul class="errors">';
				foreach ($errors[$element] as $error) {
					if (is_array($error)) $error = array_shift($error);
					$formHtml .= '<li>'.$error.'</li>';
				}
				$formHtml .= '</ul>';
			}
		}
		$formHtml .= '</form>';
		return $formHtml;
	}

	public function _setMethod($method) {
		$this->_method = $method;
	}

	/**
	* Set form action
	*/
	public function _setAction($action) {
		$this->_action = (string)$action;
	}

	/**
	* Add elements to form
	*
	* Making form
	* <code>
	* $form = new DooForm(array(
            'method' => 'post',
            'action' => $action,
            'elements' => array(
                'username' => array('text', array(
                    'required' => true,
                    'label' => 'Username:',
					'attributes' => array("style" => 'border:1px solid #000;', 'class' => 'mitar'),
					'wrapper' => 'div'
                )),
				'profile_type' => array('select', array(
                    'required' => true,
					'multioptions' => array(1 => 'private', 2 => 'public'),
					'label' => 'Profile type:'
                )),
                'password' => array('password', array(
                    'required' => true,
                    'label' => 'Password:'
                )),
                'looking_for' => array('MultiCheckbox', array(
                    'required' => false,
					'multioptions' => array(0 => 'love', 1 => 'hate', 3 => 'other'),
                    'label' => 'I am looking for:'
                )),
                'submit' => array('submit', array(
                    'label' => $this->translate("Edit password"),
                    'order' => 100,
                ))
            ),
        ));
	* </code>
	*
	* Then just do, it will return you form html.
	* <code>
	* $this->view()->form = $form->render();
	* </code>
	*
	* And then in your view script:
	* <code>
	* echo $this->form;
	* </code>
	*/
	public function _addElements() {
		$formHtml = "";
		$formElements = "";
		foreach ($this->_elements as $element => $k) {
			$elementHtml = "";
			$elementAttributes = "";
			$elementRequred = (isset($k[1]['required']) && ($k[1]['required'] == 1))?'requred="true"':'';
			// handle element attributes
			if (isset($k[1]['attributes']) && count($k[1]['attributes']) > 0) { // there are element attributes handle them
				foreach ($k[1]['attributes'] as $attribute => $a) {
					$elementAttributes .= $attribute . '="'.$a.'" ';
				}
			}
			// handle values for all fields except select, multicheckbox, checkbox, radio...

			if (count($this->_elementValues) > 0) {
				if (($k[0] != 'select') && ($k[0] != 'MultiCheckbox') && ($k[0] != 'MultiRadio') && ($k[0] != 'checkbox'))
				$elementValues = $this->_elementValues;
				if (isset($elementValues[$element])) {
					$elementAttributes .= ' value="'.$elementValues[$element].'"';
				}
			}
			// make wrapper div or dd or something other
			$elementWrapper = (isset($k[1]['wrapper']))?$k[1]['wrapper']:'dt';
			// make label wrapper
			$labelWrapper = (isset($k[1]['label-wrapper']))?$k[1]['label-wrapper']:$elementWrapper;
			// add lable if there is one

			if (isset($k[1]['label']) && ($k[0] != "submit") && ($k[0] != "captcha")) {
				$labelField = '<'.$elementWrapper.' id="'.$element.'-label"><label for="'.$element.'" '.$elementRequred.'>'. $k[1]['label'] . '</label></'.$elementWrapper.'>';
				$formElements[$element.'-label'] = $labelField;
			}
			// switch by type and make elements
			switch ($k[0]) {
				// input text
				case 'text':
					$elementHtml = '<'.$elementWrapper.' id="'.$element.'-element"><input '.$elementAttributes.' type="text" name="'.$element.'" '.$elementRequred.' /></'.$elementWrapper.'>';
					break;
				// input password
				case 'password':
					$elementHtml = '<'.$elementWrapper.' id="'.$element.'-element"><input '.$elementAttributes.' type="password" name="'.$element.'" '.$elementRequred.' /></'.$elementWrapper.'>';
					break;
				// submit field
				case 'submit':
					$elementHtml = '<'.$elementWrapper.' id="'.$element.'-element"><input '.$elementAttributes.' type="submit" name="'.$element.'" value="'.$k[1]['label']. '" /></'.$elementWrapper.'>';
					break;
				// hidden field
				case 'hidden':
					$elementHtml = '<'.$elementWrapper.' id="'.$element.'-element"><input '.$elementAttributes.' type="hidden" name="'.$element.'"/></'.$elementWrapper.'>';
					break;
				// select
				case 'select':
					$elementHtml = '<'.$elementWrapper.' id="'.$element.'-element" '.$elementRequred.'><select '.$elementAttributes.' name="'.$element.'"/>';
					if (isset($k[1]['multioptions']) && (count($k[1]['multioptions'] > 0))) {
						foreach ($k[1]['multioptions'] as $optionValue => $optionName) {
							if (is_array($optionName)) {
								foreach ($optionName as $v => $n) {
									$selected = (isset($k[1]['value']) && ($k[1]['value'] === $v))?'selected="selected"':'';
									$elementHtml .= '<option value="'.$v.'" '.$selected.'>'.$n.'</option>';
								}
							} else {
								$selected = (isset($k[1]['value']) && ($k[1]['value'] == $optionValue))?'selected="selected"':'';
								$selected = (isset($elementValues[$element]) && ($elementValues[$element] == $optionValue))?'selected="selected"':'';
								$elementHtml .= '<option value="'.$optionValue.'" '.$selected.'>'.$optionName.'</option>';
							}
						}
					}
					$elementHtml .= '</select></'.$elementWrapper.'>';
					break;
				// checkbox
				case 'checkbox':
					break;
				// multi checkbox (zomg), crazy shit
				// Ok this multicheckbox for value gets and damn fucking array of elements because
				// you can check what ever you damn like in that array of checkboxes, we can maybe say it is
				// checkbox group :)
				case 'MultiCheckbox':
					//first add wrapper

					$elementHtml = '<'.$elementWrapper.' id="'.$element.'-element" '.$elementRequred.'>';
					// now get trough all multioptions and create checkboxes
					if (isset($k[1]['multioptions']) && (count($k[1]['multioptions'] > 0))) {
						foreach ($k[1]['multioptions'] as $optionValue => $optionName) {
							$checked = (isset($k[1]['value']) && (in_array($optionValue, $k[1]['value'])))?'checked="checked"':'';
							$checked = (isset($elementValues[$element]) && (in_array($optionValue, $elementValues[$element])))?'checked="checked"':'';
							// add name for every checkbox
							$elementHtml .= '<label for="'.$element.'-'.$optionValue.'">' . $optionName;
							$elementHtml .= '<input type="checkbox" value="'.$optionValue.'" name="'.$element.'[]" '.$checked.'/>';
							$elementHtml .= '</label>';
							$elementHtml .= '<br/>';
						}
					}
					$elementHtml .= '</'.$elementWrapper.'>';
					break;
				// multi radio same thing as multi checkbox
				case 'MultiRadio':
					//first add wrapper
					$elementHtml = '<'.$elementWrapper.' id="'.$element.'-element" '.$elementRequred.'>';
					// now get trough all multioptions and create checkboxes
					if (isset($k[1]['multioptions']) && (count($k[1]['multioptions'] > 0))) {
						foreach ($k[1]['multioptions'] as $optionValue => $optionName) {
							$checked = (isset($k[1]['value']) && (in_array($optionValue, $k[1]['value'])))?'checked="checked"':'';
							$checked = (isset($elementValues[$element]) && (in_array($optionValue, $elementValues[$element])))?'checked="checked"':'';
							// add name for every checkbox
							$elementHtml .= '<label for="'.$element.'-'.$optionValue.'">' . $optionName;
							$elementHtml .= '<input type="radio" value="'.$optionValue.'" name="'.$element.'" '.$checked.'/>';
							$elementHtml .= '</label>';
							$elementHtml .= '<br/>';
						}
					}
					break;
				// captcha
				case 'captcha':
					if (!isset($_SESSION)) session_start();
					$md5 = md5(microtime() * time());
					$string = substr($md5,0,5);
					if (file_exists($k[1]['image'])) {

						$captcha = imagecreatefromjpeg($k[1]['image']);
						$black = imagecolorallocate($captcha, 250, 250, 250);
						$line = imagecolorallocate($captcha,233,239,239);
						$buffer = imagecreatetruecolor (20, 20);
						$buffer2 = imagecreatetruecolor (40, 40);
						// Add string to image
						$rotated = imagecreatetruecolor (70, 70);
						$x = 0;
						for ($i = 0; $i < strlen($string); $i++) {
							$buffer = imagecreatetruecolor (20, 20);
							$buffer2 = imagecreatetruecolor (40, 40);

							// Get a random color
							$red = mt_rand(0,255);
							$green = mt_rand(0,255);
							$blue = 255 - sqrt($red * $red + $green * $green);
							$color = imagecolorallocate ($buffer, $red, $green, $blue);

							// Create character
							imagestring($buffer, 5, 0, 0, $string[$i], $color);

							// Resize character
							imagecopyresized ($buffer2, $buffer, 0, 0, 0, 0, 25 + mt_rand(0,12), 25 + mt_rand(0,12), 20, 20);

							// Rotate characters a little
							$rotated = imagerotate($buffer2, mt_rand(-25, 25),imagecolorallocatealpha($buffer2,0,0,0,0));
							imagecolortransparent ($rotated, imagecolorallocatealpha($rotated,0,0,0,0));

							// Move characters around a little
							$y = mt_rand(1, 3);
							$x += mt_rand(2, 6);
							imagecopymerge ($captcha, $rotated, $x, $y, 0, 0, 40, 40, 100);
							$x += 22;

							imagedestroy ($buffer);
							imagedestroy ($buffer2);
						}
						imageline($captcha,0,20,140,80+rand(1,10),$line);
						imageline($captcha,40,0,120,90+rand(1,10),$line);
						// add value to session
						$_SESSION['doo_captcha_'.$element] = $string;
						if (is_dir($k[1]['directory'])) {
							imagejpeg($captcha, $k[1]['directory'] . '/'.$string.'.jpg');
							$elementHtml .= '<'.$elementWrapper.' id="'.$element.'-element"><img class="doo-captcha-image" height="50" width="120" src="'.$k[1]['url'].$string.'.jpg"/><br/>'.
							'<'.$elementWrapper.' id="'.$element.'-label"><label for="'.$element.'" '.$elementRequred.'>'. $k[1]['label'] . '</label></'.$elementWrapper.'>'.
							'<input size="'.strlen($string).'" '.$elementAttributes.' type="text" name="'.$element.'" '.$elementRequred.' class="doo-captcha-text" /></'.$elementWrapper.'>';
						} else throw new Exception("Cant create captcha there is no captcha directory: " . $k[1]['directory']);
					} else throw new Exception("Cant create captcha of missing image: " . $k[1]['image']);
					break;
			}
			// add element
			$formElements[$element] = $elementHtml;
		}
		$this->_formElements = $formElements;
	}

	/**
	* Validate form
	*
	* @var array Values for form, for example $_POST
	* @return boolean True or false if form is not valid
	*/
	public function isValid($values) {
		$valid = true;
		$errors = array();
		$v = new DooValidator();
		$formElements = $this->_formElements;
		$elementValues = array();
		foreach ($this->_elements as $element => $e) {
			// handle values
			if (isset($values[$element])) {
				$elementValues[$element] = $values[$element];
			}
			// handle validators
			if (isset($e[1]['validators'])) {
				$elementRules = array($element => $e[1]['validators']);
				$errors[$element] = $v->validate($values, $elementRules);
				if ($errors[$element]) unset($elementValues[$element]);

			}
			// handle captcha
			if (isset($e[0]) && ($e[0] == 'captcha')) {
				$sessionData = (isset($_SESSION['doo_captcha_'.$element]))?$_SESSION['doo_captcha_'.$element]:'';
				$elementRules = array($element => array('equal', $sessionData));
				$errors[$element] = $v->validate($values, $elementRules);
				if ($errors[$element]) unset($elementValues[$element]);
			}
		}
		// set values
		$this->_elementValues = $elementValues;
		if (count($errors) > 0) {
			$this->_errors = $errors;
			foreach ($errors as $error => $e) {
				if (!empty($e)) $valid = false;
			}
		}
		return $valid;
	}
}

class DooFormException extends Exception {

}
?>