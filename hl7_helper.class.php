<?php

/**
* 
*/
class Hl7Helper
{


	var $_hl7Globals = array();
	public $fileName;


	function __construct($fName = FALSE){

		$this->_hl7Globals['SEGMENT_SEPARATOR'] = "\015";
		$this->_hl7Globals['FIELD_SEPARATOR'] = "|";
		$this->_hl7Globals['NULL'] = '""';
		$this->_hl7Globals['COMPONENT_SEPARATOR'] = "^";
		$this->_hl7Globals['REPETITION_SEPARATOR'] = "~";
		$this->_hl7Globals['ESCAPE_CHARACTER'] = "\\";
		$this->_hl7Globals['SUBCOMPONENT_SEPARATOR'] = "&";
		$this->_hl7Globals['HL7_VERSION'] = "2.3";
		$this->_hl7Globals['MLLP_PREFIX'] = chr(11);
		$this->_hl7Globals['MLLP_SUFFIX'] = chr(28) . chr(13);

		if ($fName) {
			$this->fileName = $fName;
		}

	}
	/**
	 *  Cleans the suffix and prefix from the hl7
	 *  file (if they exist)
	 *
	 * @access public
	 */
	public function cleanup($msg) {

		$msg = str_replace($this->_hl7Globals['MLLP_PREFIX'], "", $msg);
		$msg = str_replace($this->_hl7Globals['MLLP_SUFFIX'], "", $msg);

		return $msg;
	}
	/**
	 *  Verifies validity of hl7 file
	 *
	 * @access public
	 */
	public function isValid($msg) {


		if (strlen($msg) < 4) {
	
			return FALSE;
	
		} elseif (strpos($msg, "MSH") != 0) {
	
			return FALSE;
	
		}


		return TRUE;
	}
	/**
	 * Get unique value. Grabs the specified 
	 * value from the hl7 formatted file
	 *
	 * @access public
	 */
	public function getValue($msg, $segmentName, $fieldId = FALSE, $componentId = FALSE, $subcomponentId = FALSE, $test = TRUE) {

		$segments = explode($this->_hl7Globals['SEGMENT_SEPARATOR'], $msg);

		foreach ($segments as $segment) {

			if (substr($segment, 0, 3) == $segmentName) {
				
				if ($fieldId) {

					$fields = explode($this->_hl7Globals['FIELD_SEPARATOR'], $segment);

					if ($componentId) {

						$components = explode($this->_hl7Globals['COMPONENT_SEPARATOR'], $fields[$fieldId]);
	
						if($subcomponentId) {

							$subcomponents = explode($this->_hl7Globals['SUBCOMPONENT_SEPARATOR'], $components[$componentId - 1]);
	
							return $subcomponents[$subcomponentId - 1];
				
						} else {

							return $components[$componentId - 1];
						}

					} else {

						if ($segmentName == 'MSH') {  //Different than other lines
							return $fields[$fieldId - 1];
						}

						return $fields[$fieldId];
							
					}

				} else {

					return $segment;

				}

			} else {

				//return 'segmentnamemismatch';

			}

		}

	}

	/**
	 * Set the component separator. Should
	 * be a single character. Default ^
	 *
	 * @param string Component separator char.
	 * @return boolean true if value has been set.
	 * @access public
	 */
	public function setComponentSeparator($value) {

	    if (strlen($value) != 1) return false;

	    return $this->_setGlobal('COMPONENT_SEPARATOR', $value);
	}


	/**
	 * Set the subcomponent separator. Should
	 * be a single character. Default: &
	 *
	 * @param string Subcomponent separator char.
	 * @return boolean true if value has been set.
	 * @access public
	 */
	public function setSubcomponentSeparator($value) {

	    if (strlen($value) != 1) return false;

	    return $this->_setGlobal('SUBCOMPONENT_SEPARATOR', $value);
	}


	/**
	 * Set the repetition separator to be used by the factory. Should
	 * be a single character. Default: ~
	 *
	 * @param string Repetition separator char.
	 * @return boolean true if value has been set.
	 * @access public
	 */
	public function setRepetitionSeparator($value) {

	    if (strlen($value) != 1) return false;

	    return $this->_setGlobal('REPETITION_SEPARATOR', $value);
	}


	/**
	 * Set the field separator to be used by the factory. Should
	 * be a single character. Default: |
	 *
	 * @param string Field separator char.
	 * @return boolean true if value has been set.
	 * @access public
	 */
	public function setFieldSeparator($value) {

	    if (strlen($value) != 1) return false;

	    return $this->_setGlobal('FIELD_SEPARATOR', $value);
	}


	/**
	 * Set the segment separator to be used by the factory. Should
	 * be a single character. Default: \015
	 *
	 * @param string Segment separator char.
	 * @return boolean true if value has been set.
	 * @access public
	 */
	public function setSegmentSeparator($value) {

	    if (strlen($value) != 1) return false;

	    return $this->_setGlobal('SEGMENT_SEPARATOR', $value);
	}


	/**
	 * Set the escape character to be used by the factory. Should
	 * be a single character. Default: \
	 *
	 * @param string Escape character.
	 * @return boolean true if value has been set.
	 * @access public
	 */
	public function setEscapeCharacter($value) {

	    if (strlen($value) != 1) return false;
	    
	    return $this->_setGlobal('ESCAPE_CHARACTER', $value);
	}


	/**
	 * Set the HL7 version to be used by the factory.
	 *
	 * @param string HL7 version character.
	 * @return boolean true if value has been set.
	 * @access public
	 */
	public function setHL7Version($value) {

	    return $this->_setGlobal('HL7_VERSION', $value);
	}


	/**
	 * Set the NULL string to be used by the factory.
	 *
	 * @param string NULL string.
	 * @return boolean true if value has been set.
	 * @access public
	 */
	public function setNull($value) {

	    return $this->_setGlobal('NULL', $value);
	}


	/**
	 * Convenience method for obtaining the special NULL value.
	 *
	 * @return string null value
	 * @access public
	 */
	public function getNull() {

	    return $this->_hl7Globals['NULL'];
	}


	/**
	 * Set the HL7 global variable
	 *
	 * @access private
	 * @param string name
	 * @param string value
	 * @return boolean True when value has been set, false otherwise.
	 */
	public function _setGlobal($name, $value) {
		
	    $this->_hl7Globals[$name] = $value;

	    return true;
	}

}
?>
