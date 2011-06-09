<?php
/*
 *  SQL Sequencer
 *
 *  Class to perform a series of search and replace operations on a SQL
 *  statement, producing an array of SQL statements.  The statements can be
 *  executed in-turn until a result is found.
 *   
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.0.1
 *	@license http://www.gnu.org/licenses/lgpl.html LGPL
 *	@package Database
 *
 */
class Database_SqlSequencer extends ImpactBase {
    private $settings = array();
    private $matrix;
    private $sql = array();
    
     /**
     *  Constructor
     *  
     *  @public
     *  @param string|array The entities (values to search for in the SQL).
     *  @param string|array The values (what to replace the entities with).
     */
    public function __construct($entities='',$values='') {
        if (!empty($entities)) {
            $this->entities = $entities;
        }
        if (!empty($entities)) {
            $this->values = $values;
        }
    }
    
    /**
     *  Set class properties.
     *
     *  Properties are stored in the private settings array and can be
     *  changed here.  values, entities and size are treated differently, with
     *  conversion methods used on the first two and error thrown for the
     *  later.  Size cannot be set as it is a reflection of the matrix size.
     *
     *  @public
     *  @param string $property The property to set.
     *  @param mixed $value The value to set the property to.
     */
    public function __set($property,$value) {
		$convertedProperty = I::camelize($property);
        
        switch ($convertedProperty) {
            case 'values':
                $this->settings[$convertedProperty] = $this->_make_array_of_array($value);
                $this->settings['size'] = $this->_matrix_size();
                break;
            case 'entities':
                $this->settings[$convertedProperty] = $this->_make_array($value);
                break;
            case 'size':
                throw new Exception('Cannot set the matrix size');
                break;
            default:
                $this->settings[$convertedProperty] = $value;
                break;
        }
	}
    
    /**
     *  Get class properties.
     *
     *  Properties are stored in the private settings array and can be
     *  accessed here.
     *
     *  @public
     *  @param string $property The property to get.
     *  @return mixed
     */
    public function __get($property) {
		$convertedProperty = I::camelize($property);
        
        if (isset($this->settings[$convertedProperty])) {
			return $this->settings[$convertedProperty];
		} else {
			if ($property = 'settings') {
				return $this->settings;
			}
			throw new Exception('Property: '.$convertedProperty.', does not exist');
		}
	}
    
    /**
     *  Calculate a series of SQL statements from class settings.
     *
     *  Take the supplied SQL statement and run a series of search and replace
     *  operations against it, according to the values stored in $this->entities
     *  and $this->values.  Results are returned in an array of SQL statements,
     *  which can be run in sequence.
     *  
     *  @public
     *  @param string $SQL The SQL, which needs converting.
     *  @return array An array of SQL statements.
     */
    public function exec($SQL) {
        $this->_create_matrix();
        
        $sql = array();
        for ($i = 0; $i < count($this->matrix); $i++) {
            $sql[$i] = $SQL;
            foreach($this->matrix[$i] as $enity => $value) {
                $sql[$i] = str_replace($enity,$value,$sql[$i]);
            }
        }
        
        return $sql;
    }
    
    /**
     *  Create an exececution-matrix.
     *
     *  Calculated from the supplied entities and values.  The matrix will be
     *  used for search and replace operations and forms the order that these
     *  should be done in.  Each row is a separate *result* and each column
     *  in the row represents a search and replace operation to produce the
     *  required result.
     *  
     *  @private
     *  @return In the form: (('entity1=>'value1'...etc),('entity1=>'value2'...etc),...etc)
     */
    private function _create_matrix() {
        $this->_create_blank_matrix();
        
        for ($entityNo = 0; $entityNo < count($this->entities); $entityNo++) {
            $entity = $this->entities[$entityNo];
            
            $repeatNo = $this->_calc_repeat_number($entityNo);
            $counter = 0;
            while ($counter < $this->size) {
                
                for ($valueNo = 0; $valueNo < count($this->values[$entityNo]); $valueNo++) {
                    for ($ii = 0; $ii < $repeatNo; $ii++) {
                        $this->matrix[$counter][$entity] = $this->values[$entityNo][$valueNo];
                        $counter++;
                    }
                }
                
            }
        }
        
        return $this->matrix;
    }
    
    /**
     *  The number of times an entity is repeated in the matrix-group.
     *
     *  Calculated by taking the number of values assigned to all previous
     *  entities in the sequence and multiplying them together.
     *  
     *  @private
     *  @param integer $entityNo The entity-number, ie. it position or order-number.
     *  @return integer
     */
    private function _calc_repeat_number($entityNo) {
        $repeatNo = 1;
        
        for ($i = 1; $i <= $entityNo; $i++) {
            $repeatNo *= count($this->values[$i-1]);
        }
        
        return $repeatNo;
    }
    
    /**
     *  Create a new blank execution matrix.
     *
     *  @note The return-value is not needed or generally used but is useful for testing.
     *  
     *  @private
     *  @return array In the form: (('entity1=>''...etc),('entity1=>''...etc),...etc)
     */
    private function _create_blank_matrix() {
        $this->matrix = array();
        
        for ($i = 0; $i < $this->size; $i++) {
            $this->matrix[$i] = $this->_create_blank_row();
        }
        
        return $this->matrix;
    }
    
    /**
     *  Create a blank-row in the execution matrix.
     *  
     *  @private
     *  @return array() In the form: ('entity1=>'','entity2'=>'',...etc)
     */
    private function _create_blank_row() {
        $row = array();
        
        foreach($this->entities as $entity) {
            $row[$entity] = '';
        }
        
        return $row;
    }
    
    /**
     *  Create and array-of-array of the supplied data.
     *
     *  A string will be first turned into an array via the _make_array method.
     *  An array or converted-string will be converted so that each item is
     *  an array of it's own.
     *  
     *  @private
     *  @param string|array $data The data to convert.
     *  @return array
     */
    private function _make_array_of_array($data) {
		if (!is_array($data)) {
			$data = array($data);
		}
		$newArray = $data;
		
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$newArray[$key] = $value;
			} else {
				$newArray[$key] = array($value);
			}
		}
		
		return $newArray;
	}
    
    /**
     *  Turn the supplied value into an array.
     *
     *  A string will be converted so that it is contained in a one item
     *  array, where-as, arrays will be left untouched.
     *  
     *  @private
     *  @param string|array $data The data to convert.
     *  @return array
     */
    private function _make_array($data) {
		if (!is_array($data)) {
			return array($data);
		}
		return $data;
	}
    
    /**
     *  Calculate the number of rows in the execution matrix
     *  
     *  @private
     *  @return integer
     */
    private function _matrix_size() {
        $total = 1;
        
        for ($i = 0; $i < count($this->values); $i++) {
            $total *= count($this->values[$i]);
        }
        
        return $total;
    }
}
?>