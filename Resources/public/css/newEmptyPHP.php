<?php

/** Vmreport Model for Vmreport World Component *
 * @packageJoomla.Tutorials
 * @subpackage Components
 * @link http://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_4
 * @license GNU/GPL */
// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

/** Vmreport Vmreport Model *
 * @packageJoomla.Tutorials
 * @subpackage Components */
class VmreportsModelVmreport extends JModel {

  /**
   * Constructor that retrieves the ID from the request
   *
   * @access public
   * @return void
   */
  function __construct() {
    parent::__construct();
    $array = JRequest::getVar('cid', 0, '', 'array');
    $this->setId((int) $array[0]);
  }

  /**
   * Method to set the vmreport identifier
   *
   * @access
    public
   * @param
    int Vmreport identifier
   * @return
    void
   */
  function setId($id) {
// Set id and wipe data
    $this->_id = $id;
    $this->_data = null;
  }

  /**
   * Method to get a vmreport
   * @return object with data
   */
  function &getData() {
// Load the data
    if (empty($this->_data)) {
      $query = ' SELECT * FROM #__vmreport ' .
              'WHERE id = ' . $this->_id;
      $this->_db->setQuery($query);
      $this->_data = $this->_db->loadObject();
    }
    if (!$this->_data) {
      $this->_data = new stdClass();
      $this->_data->id = 0;
      $this->_data->date_start = null;
      $this->_data->date_end = null;
    }
    return $this->_data;
  }

  /**
   * Method to store a record
   *
   * @access
    public
   * @return
    boolean
    True on success
   */
  /*
    function store()
    {
    $row =& $this->getTable();
    $data = JRequest::get( 'post' );
    // Bind the form fields to the vmreport table
    if (!$row->bind($data)) {
    $this->setError($this->_db->getErrorMsg());
    return false;
    }
    // Make sure the vmreport record is valid
    if (!$row->check()) {
    $this->setError($this->_db->getErrorMsg());
    return false;
    }
    // Store the web link table to the database
    if (!$row->store()) {
    $this->setError( $row->getErrorMsg() );
    return false;
    }
    return true;
    } */
  function date_mysql2timestamp($date) {
    return mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));
  }

  function store() {
    $row = & $this->getTable();
    $data = JRequest::get('post');
    $data['date_start'] = $this->date_mysql2timestamp($data['date_start']);
    $data['date_end'] = $this->date_mysql2timestamp($data['date_end']) + 60 * 60 * 24 - 1;
    $db = & JFactory::getDBO();
    $query = "SELECT * FROM " . $db->nameQuote('#__vmreport') . " AS reports WHERE
		reports.date_start = '" . $data['date_start'] . "'
		AND
		reports.date_end = '" . $data['date_end'] . "'
		";
    print $query . '<br />';
// Requête SQL
    $db->setQuery($query);
    $result = $db->query();
    if ($data['id'] == 0 && $db->loadResult()) {
      $this->setError("Un rapport à déjà été créé pour cette période. Pour le récreer supprimer-le d'abord.");
      echo("Un rapport à déjà été créé pour cette période. Pour le récreer supprimer-le d'abord.");
      return false;
    }
// Bind the form fields to the vmreport table
    if (!$row->bind($data)) {
      $this->setError($this->_db->getErrorMsg());
      return false;
    }
// Make sure the vmreport record is valid
    if (!$row->check()) {
      $this->setError($this->_db->getErrorMsg());
      return false;
    }
    if (!$this->storeReport($data)) {//
      $this->setError($this->storeReport->getErrorMsg());
      return false;
    }
// Store the web link table to the database
    if (!$row->store()) {
      $this->setError($row->getErrorMsg());
      return false;
    }
    return true;
  }

  function storeReport($data) {
// Requête SQL
    $db = & JFactory::getDBO();
    $query = "SELECT
		item.order_id,
		item.order_item_name,
		item.product_item_price,
		item.product_final_price,
		uinfo.company,
		uinfo.title,
		uinfo.last_name,
		uinfo.first_name,
		uinfo.address_1,
		uinfo.address_2,
		uinfo.zip,
		uinfo.city,
		uinfo.country,
		uinfo.phone_1,
		uinfo.user_email,
		uinfo.perms,
		user.name,
		user.usertype,
		shopper_group.shopper_group_name,
		ord.order_shipping,
		ord.order_total,
		ord.cdate
		FROM
		" . $db->nameQuote('#__vm_order_item') . " AS item,
		" . $db->nameQuote('#__vm_orders') . " AS ord,
		" . $db->nameQuote('#__vm_user_info') . " AS uinfo,
		" . $db->nameQuote('#__users') . " AS user,
		" . $db->nameQuote('#__vm_shopper_vendor_xref') . " AS user_shopper,
		" . $db->nameQuote('#__vm_shopper_group') . " AS shopper_group
		WHERE
		item.order_id = ord.order_id
		AND
		ord.user_id = user.id
		AND
		ord.user_id = uinfo.user_id
		AND
		ord.user_id = user_shopper.user_id
		AND
		user_shopper.shopper_group_id = shopper_group.shopper_group_id
		AND
		ord.cdate >= " . $data['date_start'] . "
		AND
		ord.cdate <= " . $data['date_end'] . "
		GROUP BY ord.order_id
		ORDER BY ord.cdate;
		"; //
    echo $query;
    $db->setQuery($query); //
    $result = $db->query();
    $rows = $db->loadObjectList();
// Titre des colonnes de votre fichier .CSV ou .XLS
    $fichier = utf8_decode("Commande; Date; Société; Civilité; Prénom; Nom; Adresse 1;Adresse 2;Code Postal;Ville;Pays;Téléphone;Email;Autorisations;Groupe de client; Total de la commande;Frais de livraison; utilisateur; Type d'utilisateur; Premier produit de la commande; Prix du produit HT; Prix du produit TTC");
    $fichier .= "\n";

// Enregistrement des résultats ligne par ligne
    function numberFrench($input) {
      return str_replace('.', ',', $input);
    }

    foreach ($rows AS $row) {
      $fichier .= utf8_decode("" . $row->order_id . ";" . date('Y-m-d', $row->cdate) . ";" . $row->company . ";" . $row->title . ";" . $row->last_name . ";" . $row->first_name . ";" . $row->address_1 . ";" . $row->address_2 . ";" . $row->zip . ";" . $row->city . ";" . $row->country . ";" . $row->phone_1 . ";" . $row->user_email . ";" . $row->perms . ";" . $row->shopper_group_name . ";" . numberFrench($row->order_total) . ";" . numberFrench($row->order_shipping) . ";" . $row->name . ";" . $row->usertype . ";" . $row->order_item_name . ";" . numberFrench($row->product_item_price) . ";" . numberFrench($row->product_final_price) . "\n");
    }
    echo $fichier;
    $params = &JComponentHelper::getParams('com_vmreport');
    if (!(file_put_contents('./components/com_vmreport/' . $params->get('reports_save_folder') . date('Y-m-d', $data['date_start']) . '-' . date('Y-m-d', $data['date_end']) . '.csv', $fichier))) {
      $this->setError("Error writing file");
      return false;
    }
    return true;
  }

  /**
   * Method to delete record(s)
   *
   * @access
    public
   * @return
    boolean
    True on success
   */
  function delete() {
    $cids = JRequest::getVar('cid', array(0), 'post', 'array');
    $row = & $this->getTable();
    if (count($cids)) {
      foreach ($cids as $cid) {
        if (!$row->delete($cid)) {
          $this->setError($row->getErrorMsg());
          return false;
        }
      }
    }
    return true;
  }

}