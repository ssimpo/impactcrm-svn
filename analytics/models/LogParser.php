<?php
if (!defined('DIRECT_ACCESS_CHECK')) {
	die('Direct access is not allowed');
}

/**
 *  Log Parser Clsss
 *
 *  Parse the specified set of logs, according to the specified profile, using
 *  the interpreter class for the log-format used.
 *  
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.0.2
 *	@license http://www.gnu.org/licenses/lgpl.html LGPL
 *	@package Analytics
 */
class LogParser extends Base {
    private $settings = array();
    
    public function __construct($type='',$profile='') {
        if ($type != '') {
            $this->interpreter = $type;
        }
        if ($profile != '') {
            $this->profile = $profile;
        }
    }
    
    /**
	 *	Generic set property method.
	 *
	 *	@public
	 */
	public function __set($property,$value) {
		$convertedProperty = I::camelize($property);
        switch ($convertedProperty) {
            case 'interpreter':
                $this->settings['interpreter'] = new LogInterpreter($value);
                break;
            case 'profile':
                $this->settings['profile'] = new LogProfile($value);
                break;
            default:
                $this->settings[$convertedProperty] = $value;
                break;
        }
	}
	
	/**
	 *	Generic get property method.
	 *
	 *	@public
	 */
	public function __get($property) {
		$convertedProperty = I::camelize($property);
        
        if (isset($this->settings[$convertedProperty])) {
			return $this->settings[$convertedProperty];
		} else {
			if ($property == 'settings') {
				return $this->settings;
			}
			throw new Exception('Property: '.$convertedProperty.', does not exist');
		}
	}
    
    /**
     *  Get the next line, which passes the filter-profile.
     *
     *  @public
     *  @return string|boolean=false The next valid line or false if end of file has been reached.
     */
    public function next() {
        $handle = $this->_get_file($this->file);
        
        while (!feof($handle)) {
            $line = $this->_get_line($handle);
            $data = $this->interpreter->parse($line);
            
            if ($this->profile->include_line($data)) {
                return $line;
            }
        }
        
        return false;
    }
    
    /**
     *  Get a filehandle for the given file.
     *
     *  @private
     *  @param string $filename The filename + path.
     *  @return filehandle|null
     */
    private function _get_file($filename) {
        if (is_file($filename)) {
            $fh = @fopen($filename,'r');
            if ($fh) {
                return $fh;
            } else {
                throw new Exception('Could not open file: "'.$filename.'".');
            }
        } else {
            throw new Exception('Filename: "'.$filename.'", is not valid.');
        }
        return null;
    }
    
    /**
     *  Get a line from the log file.
     *
     *  @private
     *  @param filehandle $handle The filehandle to get a line from.
     *  @return string The line from specified handle.
     */
    private function _get_line($handle) {
        if ($handle) {
            if (!feof($handle)) {
                return fgets($handle);
            }
        }
        
        return null;
    }
}
?>