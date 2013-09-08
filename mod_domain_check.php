<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
require_once( dirname(__FILE__).DS.'helper.php' );

$label = $params->get('label', '');
$buttontext = $params->get('buttontext', '');
$resulttext = $params->get('resulttext', '');
$checkall = $params->get('checkall', '');
$pretext = $params->get('pretext', '');
$forwardurl = $params->get('forwardurl', '');
$whois_server = $params->get('whois_server', ''); 
$avaliable_text = $params->get('avaliable_text', 'Avaliable'); 
$taken_text = $params->get('taken_text', 'Taken'); 
$tlds = $params->get('tlds', array('com', 'net', 'org', 'info')); 
$countries = $params->get('countries', array('iii', 'br'));
//JHtml::stylesheet(Juri::base() . 'templates/custom/css/style.js');


$dck = modDomainCheckHelper::getInstance();
$dck->setResultText($avaliable_text, $taken_text);
// whois.crsnic.net
// whois.registro.br
/*
 * 
      $tlds = array("com=whois.crsnic.net", "net=whois.crsnic.net", "org=whois.publicinterestregistry.net", "info=whois.afilias.net", "biz=whois.neulevel.biz", "us=whois.nic.us", "co.uk=whois.nic.uk", "org.uk=whois.nic.uk", "ltd.uk=whois.nic.uk", "ca=whois.cira.ca", "cc=whois.nic.cc", "edu=whois.crsnic.net", "com.au=whois.aunic.net", "net.au=whois.aunic.net", "de=whois.denic.de", "ws=whois.worldsite.ws", "sc=whois2.afilias-grs.net");
 */

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base() . 'modules/mod_domain_check/css/mod_domain_check.css');


$jinput = JFactory::getApplication()->input;
$domainbase = $jinput->post->get('domainbase', '', 'CMD');
$chosen_tlds = $jinput->get('chosen_tlds', array(), 'ARRAY');
$chosen_countries = $jinput->get('chosen_countries', array(), 'ARRAY');

require(JModuleHelper::getLayoutPath('mod_domain_check'));
?>    
