<?php
/**
 * DooForm class file.
 * @package doo.helper
 * @author Milos Kovacki <kovacki@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
* DooForm class manage forms
*
* @author Milos Kovacki <kovacki@gmail.com>
* @copyright &copy; 2009 Milos Kovacki
* @license http://www.doophp.com/license
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
	* Form enctype
	* @var string
	*/

	protected $_enctype = null;


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
		if (isset($form['enctype'])) {
			$this->_enctype = $form['enctype'];
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
		$enctype = (isset($this->_enctype) && ($this->_enctype == 'multipart/form-data'))?'enctype="'.$this->_enctype.'"':'';
		$formHtml = '<form action="'.$this->_action . '" method="'.$this->_method.'" '.$enctype.' class="doo-form">';
		foreach ($formElements as $element => $e) {
			$formHtml .= $formElements[$element];
			if ((count($this->_errors) > 0) && (isset($errors[$element]))) {
				$formHtml .= '<ul class="errors">';
				foreach ($errors[$element] as $error) {
					if (is_array($error)) {
						$error = array_shift($error);
					}
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
    *        'method' => 'post',
    *        'action' => $action,
    *        'elements' => array(
    *            'username' => array('text', array(
    *                'required' => true,
    *                'label' => 'Username:',
	*				'attributes' => array("style" => 'border:1px solid #000;', 'class' => 'mitar'),
	*				'wrapper' => 'div'
    *            )),
	*			'profile_type' => array('select', array(
    *                'required' => true,
	*				'multioptions' => array(1 => 'private', 2 => 'public'),
	*				'label' => 'Profile type:'
    *            )),
    *            'password' => array('password', array(
    *                'required' => true,
    *                'label' => 'Password:'
    *            )),
    *            'looking_for' => array('MultiCheckbox', array(
    *                'required' => false,
	*				'multioptions' => array(0 => 'love', 1 => 'hate', 3 => 'other'),
    *                'label' => 'I am looking for:'
    *            )),
    *            'submit' => array('submit', array(
    *                'label' => $this->translate("Edit password"),
    *                'order' => 100,
    *            ))
    *        ),
    *    ));
	* </code>
	*
	* Then just render it, it will return you form html.
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
		$elementValues = $this->_elementValues;
		foreach ($this->_elements as $element => $k) {

			$elementHtml = "";
			$elementAttributes = "";
			$elementRequred = (isset($k[1]['required']) && ($k[1]['required'] == 1))?'requred="true"':'';

			// add element values
			 if (isset($k[1]['value'])) {
				$elementValues[$element] = $k[1]['value'];
				//$this->_elementValues = $elementValues;
			 }
			// handle element attributes
			if (isset($k[1]['attributes']) && count($k[1]['attributes']) > 0) { // there are element attributes handle them
				foreach ($k[1]['attributes'] as $attribute => $a) {
					$elementAttributes .= $attribute . '="'.htmlspecialchars($a).'" ';
				}
			}
			// handle values for all fields except select, multicheckbox, checkbox, radio...
			if (($k[0] != 'select') && ($k[0] != 'MultiCheckbox') && ($k[0] != 'MultiRadio') && ($k[0] != 'checkbox') && ($k[0] != 'textarea')) {
				if (isset($elementValues[$element])) {
					$elementAttributes .= ' value="'.htmlspecialchars($elementValues[$element]).'"';
				}
			}
			// make wrapper div or dd or something other
			$elementWrapper = (isset($k[1]['wrapper']))?$k[1]['wrapper']:'dt';
			// make label wrapper
			$labelWrapper = (isset($k[1]['label-wrapper']))?$k[1]['label-wrapper']:$elementWrapper;
			// add lable if there is one
			if (!isset($k[1]['hide-label']) || ($k[1]['hide-label'] != true))
			{
				if (isset($k[1]['label']) && ($k[0] != "submit") && ($k[0] != "captcha")) {
					$labelField = '<'.$labelWrapper.' id="'.$element.'-label"><label for="'.$element.'-element" '.$elementRequred.'>'. $k[1]['label'] . '</label></'.$labelWrapper.'>';
					$formElements[$element.'-label'] = $labelField;
				}
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
				case 'file':
					$elementHtml = '<'.$elementWrapper.' id="'.$element.'-element"><input '.$elementAttributes.' type="file" name="'.$element.'"/></'.$elementWrapper.'>';
					// add automatic enctype for form
					if ($this->_enctype != 'multipart/form-data') {
						$this->_enctype = 'multipart/form-data';
					}
					break;
				// select
				case 'select':
					$elementHtml = '<'.$elementWrapper.' id="'.$element.'-element" '.$elementRequred.'><select '.$elementAttributes.' name="'.$element.'">';
					if (isset($k[1]['multioptions']) && (count($k[1]['multioptions'] > 0))) {
						foreach ($k[1]['multioptions'] as $optionValue => $optionName) {
							if (is_array($optionName)) { // if its array make option groups
								$elementHtml .= '<optgroup label="'.$optionValue.'">';
								foreach ($optionName as $v => $n) {
									$selected = (isset($k[1]['value']) && ($k[1]['value'] === $v))?'selected="selected"':'';
									$elementHtml .= '<option value="'.$v.'" '.$selected.'>'.$n.'</option>';
								}
								$elementHtml .= '</optgroup>';
							} else {
								$selected = (isset($k[1]['value']) && ($k[1]['value'] == $optionValue))?'selected="selected"':'';
								$selected = (isset($elementValues[$element]) && ($elementValues[$element] == $optionValue))?'selected="selected"':'';
								$elementHtml .= '<option value="'.$optionValue.'" '.$selected.'>'.$optionName.'</option>';
							}
						}
					}
					$elementHtml .= '</select></'.$elementWrapper.'>';
					break;
				// text area
				case 'textarea':
					$text =(isset($elementValues[$element]))?$elementValues[$element]:'';
					$elementHtml = '<'.$elementWrapper.' id="'.$element.'-element"><textarea '.$elementAttributes.' name="'.$element.'"/>'.$text.'</textarea></'.$elementWrapper.'>';
					break;
				// checkbox
				case 'checkbox':
					$checked = (isset($elementValues[$element]) && ($elementValues[$element] == "on"))?'checked="checked"':'';
					$elementHtml = '<'.$elementWrapper.' id="'.$element.'-element"><input '.$elementAttributes.' type="checkbox" name="'.$element.'" '.$checked.'/></'.$elementWrapper.'>';
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
					if (!isset($_SESSION)) {
						session_start();
					}
					$md5 = md5(microtime() * time());
					$string = substr($md5,0,4);
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
							// if there is font color set font color
							if (isset($k[1]['font-color'])) {
								switch ($k[1]['font-color']) {
									case 'white':
										$color = imagecolorallocate($buffer, 255, 255, 255);
										break;
									case 'black':
										$color = imagecolorallocate($buffer, 0, 0, 0);
										break;
									case 'green':
										$color = imagecolorallocate($buffer, 0, 175, 24);
										break;
									case 'blue':
										$color = imagecolorallocate($buffer, 41, 56, 223);
										break;
									case 'red':
										$color = imagecolorallocate($buffer, 207, 25, 25);
										break;
									case 'pink':
										$color = imagecolorallocate($buffer, 207, 25, 23);
										break;
									case 'yellow':
										$color = imagecolorallocate($buffer, 230, 255, 15);
										break;
									case 'orange':
										$color = imagecolorallocate($buffer, 255, 167, 15);
										break;
									default:
										$color = imagecolorallocate($buffer, 0, 0, 0);
										break;
								}
							} else {
								$color = imagecolorallocate ($buffer, $red, $green, $blue);
							}
							// Create character
							if (isset($k[1]['font']) && file_exists($k[1]['font'])) {
								$fontSize = (isset($k[1]['font-size']))?$k[1]['font-size']:12;
								imagefttext($buffer, $fontSize, 0, 2, 15, $color, $k[1]['font'], $string[$i]);
							} else {
								imagestring($buffer, 5, 0, 0, $string[$i], $color);
							}

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
							imagejpeg($captcha, $k[1]['directory'] . '/'.md5($string).'.jpg');
							$elementHtml .= '<'.$elementWrapper.' id="'.$element.'-element"><img class="doo-captcha-image" height="50" width="120" src="'.$k[1]['url'].md5($string).'.jpg"/><br/>'.
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
		$this->_elementValues = $elementValues;
	}

	/**
	* Validate form
	*
	* @var array Values for form, for example $_POST
	*
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
			if ((isset($e[1]['validators']))) {
				if (!isset($e[1]['required']) || ($e[1]['required'] != false)) {
					$elementRules = array($element => $e[1]['validators']);
					$errors[$element] = $v->validate($values, $elementRules);
					if ($errors[$element]) {
						unset($elementValues[$element]);
					}
				} else if (isset($elementValues[$element]) && $elementValues[$element] != "") {
					$elementRules = array($element => $e[1]['validators']);
					$errors[$element] = $v->validate($values, $elementRules);
				}
			}
			// handle captcha
			if (isset($e[0]) && ($e[0] == 'captcha')) {
				$sessionData = (isset($_SESSION['doo_captcha_'.$element]))?md5($_SESSION['doo_captcha_'.$element]):'';
				$msg = (isset($e[1]['message']))?$e[1]['message']:null;
				$elementRules = array($element => array('equal', $sessionData, $msg));
				$values[$element] = md5($values[$element]);
				$errors[$element] = $v->validate($values, $elementRules);
				if ($errors[$element]) {
					unset($elementValues[$element]);
				}
				// delete captcha if captcha is good
				if (isset($e[1]['url']) && file_exists($e[1]['directory'].'/'.$sessionData.".jpg")) {
					unlink($e[1]['directory'].'/'.$sessionData.".jpg");
				}
			}
			// handle file
			if (isset($e[0]) && ($e[0] == 'file')) {
				// if there is file check if file exists
				if (isset($_FILES[$element])) {
					// check file extension
					if (isset($e[1]['extension'])) {
						$extensions = array();
						$extension = substr($_FILES[$element]['name'], strrpos($_FILES[$element]['name'], '.')+1);
						$extensions = explode(',', $e[1]['extension']);
						if (!in_array($extension, $extensions)) {
							$errors[$element] = array('File must have ' . $e[1]['extension'] . ' extension.');
						}
					}
					// check file size
					if (isset($e[1]['size'])) {
						if ($e[1]['size'] < $_FILES[$element]['size']) {
							$errors[$element] = array('File is too big!');
						}
					}
				} else {
					if (isset($e[1]['required']) && $e[1]['required'] == 1)  {
						$errors[$element][] =  array('File for upload is required.');
					}
				}
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