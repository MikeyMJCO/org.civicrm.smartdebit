<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Smartdebit_Form_Payerdetails extends CRM_Core_Form {
  public function buildQuickForm() {
    if ($this->_flagSubmitted) return;

    $reference_number = CRM_Utils_Array::value('reference_number', $_GET);
    if (empty($reference_number)) {
      CRM_Core_Error::statusBounce('You must specify a reference number!');
      return;
    }

    $smartDebitResponse = CRM_Smartdebit_Sync::getSmartdebitPayerContactDetails($reference_number);

    foreach ($smartDebitResponse[0] as $key => $item) {
      $smartDebitDetails[] = array('label' => $key, 'text' => $item);
    }

    $this->assign('transactionId', $reference_number);

    $this->assign('smartDebitDetails', $smartDebitDetails);

    //$url = CRM_Utils_System::url(CRM_Smartdebit_Utils::$reconcileUrl . '/list', 'reset=1');
    $url = $_SERVER['HTTP_REFERER'];
    $buttons[] = array(
      'type' => 'back',
      'js' => array('onclick' => "location.href='{$url}'; return false;"),
      'name' => ts('Back'));
    $this->addButtons($buttons);

    parent::buildQuickForm();
  }

  public function postProcess() {
    CRM_Core_Session::singleton()->pushUserContext($_SESSION['http_referer']);
    parent::postProcess();
  }
}
