<?php

/**

 * File for class FT_ListMulti.

 *

 * @package Vortex

 * @subpackage DB

 * @author Thiago Ramon Gonçalves Montoya

 * @copyright Copyright 2007, Thiago Ramon Gonçalves Montoya

 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License

 */



/** Require the base class */

require_once('FT_List.class.php');



/**

 * Database field, List (<select multiple>) with contents in a table, stored in a intermediate table.

 *

 * @package Vortex

 * @subpackage DB

 */

class FT_ListMulti extends FT_List

{

	/**

	 * Intermediate table to store the selected values.

	 *

	 * @var string

	 */

	var $multi_table = '';



	/**

	 * Field(s) to use as key in the intermediate table, separated by commas.

	 *

	 * @var string

	 */

	var $multi_key = '';



	/**

	 * Field(s) to use as the target key in the intermediate table, separated by commas.

	 *

	 * @var string

	 */

	var $multi_rel = '';



	/**

	 * String used to separate multiple $multi_rel in the form.

	 *

	 * @var string

	 */

	var $separator = '|';



	/**

	 * Constructor: Load all parameters into member variables.

	 *

	 * @param DB_Base $db Database where the field is.

	 * @param array $opts Parameters for the object, as 'var' => 'value'.

	 */

	function FT_ListMulti(&$db, $opts = array())

	{

		$this->default = '';

		parent::FT_List($db, $opts);

		$this->name_db = '';

	}



	/**

	 * Output the field as a HTML Form or just for display.

	 *

	 * @param array $data Array containing the field values as 'field' => 'value'.

	 * @param bool $form Show field as a INPUT object?

	 */

	function Show(&$data, $form = TRUE, $origin = FT_OR_DB)

	{

		global $vortex_msgs;

		

		if (!$form) return;



		echo "<tr class='ft_{$this->name}'><th>".$this->label.'</th><td>';



		$selected = array();

		if (!empty($data)) {

			$sel = array(

				'fields' => array($this->multi_rel),

				'from' => array($this->multi_table)

			);

			$where = '';

			$p = explode(',', $this->multi_key);

			foreach ($p as $f) $where .= (empty($where)?'':' AND ')."($f = '{$data[$f]}')";

			if (!empty($where)) $sel['where'] = $where;

			if (!($rs = $this->db->Select($sel))) {

				return;

			}

			while ($row = $rs->Row()) $selected[] = implode($this->separator, $row);

			$rs->Close();

			dv(5, 'Selected', $selected);

		}



		$sel = array(

			'fields' => array($this->rel_key, $this->rel_label),

			'from' => array($this->rel_table)

		);

		if (!empty($this->rel_order)) $sel['order'] = $this->rel_order;

		if (!($rs = $this->db->Select($sel))) {

			return;

		}

		echo "<select multiple name='{$this->name_form}[]'>\n";

		while ($row = $rs->Row()) {

			echo "\t<option value='{$row[$this->rel_key]}'".(in_array($row[$this->rel_key], $selected)?' selected':'').">{$row[$this->rel_label]}</option>\n";

		}

		echo "</select>\n";

		echo "</td></tr>\n";

		$rs->Close();

	}



	/**

	 * Replaces the values in the intermediate table with the new ones.

	 *

	 * @param array $vars Array containing the FORM data (usually $_POST).

	 * @return string Returns a string containing the parsed field data, or FALSE if the field is invalid.

	 */

	function Consist(&$vars)

	{

		if (!empty($vars[$this->name_form])) {

			$where = '';

			$p = explode(',', $this->multi_key);

			foreach ($p as $f) $where .= (empty($where)?'':' AND ')."($f = '{$vars[$f]}')";

			$this->db->Delete($this->multi_table, $where);

			foreach ($vars[$this->name_form] as $v) {

				$values = array();

				$p = explode(',', $this->multi_key);

				foreach ($p as $f) $values[$f] = $vars[$f];

				$p = explode(',', $this->multi_rel);

				$vs = explode($this->separator, $v);

				foreach ($p as $i => $f) $values[$f] = $vs[$i];

				$this->db->Insert($this->multi_table, $values);

			}

		}

		return '';

	}



	/**

	 * Format the field for database insertion.

	 * In this case, it replaces the values of the intermediate database.

	 *

	 * @param string $field The data from the field to be formated.

	 * @return string Returns the formated field.

	 */

	function ConsistFormat(&$field)

	{

		return '';

	}

}



?>