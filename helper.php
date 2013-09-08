<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class modDomainCheckHelper {

   protected $servers = array(
       'iii' => array(
           'com' => 'whois.crsnic.net',
           'net' => 'whois.crsnic.net',
           'edu' => 'whois.crsnic.net',
           'info' => 'whois.afilias.net',
           'org' => 'whois.publicinterestregistry.net',
           'biz' => 'whois.neulevel.biz',
           'default' => 'whois.crsnic.net'
       ),
       'br' => array('default' => 'whois.registro.br'),
       'ca' => array('default' => 'whois.cira.ca'),
       'uk' => array('default' => 'whois.nic.uk'),
       'default' => ''
   );
   protected $textos = array(
       'iii' => array(
           'com' => array(
               'avaliable' => array('No match for'),
               'taken' => array(),
               'error' => array()
           ),
           'org' => array(
               'avaliable' => array('NOT FOUND'),
               'taken' => array(),
               'error' => array()
           ),
           'default' => array(
               'avaliable' => array('No match for'),
               'taken' => array(),
               'error' => array()
           )
       ),
       'br' => array(
           'default' => array(
               'avaliable' => array('No match for'),
               'taken' => array(),
               'error' => array()
           )
       ),
       'default' => array('No match for')
   );
   protected $checados;
   private static $instance = null;
   protected $avaliable_text = 'Avaliable';
   protected $taken_text = 'Taken';
   protected $errors = array();

   private function __construct() {
      
   }

   public static function getInstance() {
      if (self::$instance === null) {
         self::$instance = new modDomainCheckHelper();
      }
      return self::$instance;
   }

   public function getServerResponse($domain, $server) {
      $con = fsockopen($server, 43);
      if (!$con)
         return false;
      fputs($con, $domain . "\r\n");
      $response = ' :';
      while (!feof($con)) {
         $response .= fgets($con, 128);
      }
      fclose($con);
      return $response;
   }

   public function checkDomain($domain, $server, $findArray) {
      $response = $this->getServerResponse($domain, $server);
      if (!$response) {
         $this->errors[] = '<div class="result_error">Server error: ' . $domain . '</div>';
      }
      $tem_text = false;
      foreach ($findArray as $ftext) {
         if (strpos($response, $ftext)) {
            $tem_text = true;
         }
      }
      return $tem_text;
   }

   public function setResultText($avaliable_text, $taken_text) {
      $this->avaliable_text = $avaliable_text;
      $this->taken_text = $taken_text;
   }

   public function getDomainResult($domain, $server, $findArray) {
      $avaliable_text = $this->avaliable_text;
      $taken_text = $this->taken_text;
      $chk = $this->checkDomain($domain, $server, $findArray);
      if ($chk) {
         return "<div class='avaliable_text'>"
         . "<img width='20px' height='20px' src='".JURI::root()."modules/mod_domain_check/images/registro_ok.png'><span>$domain </span><span class='domain_txt'>$avaliable_text</span>"
                 . "</div>";
      } else {
         return "<div class='taken_text'>"
         . "<img width='20px' height='20px' src='".JURI::root()."modules/mod_domain_check/images/registro_fail.png'><span>$domain </span><span class='domain_txt'>$taken_text</span>"
                 . "</div>";
      }
   }

   /**
    * 
    * @param type $country
    * @param type $tld
    * @param type $conf
    * @return array
    */
   public function getFindArray($country, $tld, $conf = 'avaliable') {
      $retorno = null;
      $textos = $this->textos;
      if (isset($textos[$country])) {
         $lista_textos = $textos[$country];
         if (isset($lista_textos[$tld])) {
            $retorno = $lista_textos[$tld][$conf];
         } else {
            $retorno = $lista_textos['default'][$conf];
         }
      } else {
         $retorno = $textos['default'];
      }
      return $retorno;
   }

   /**
    * 
    * @param type $country
    * @param type $tld
    * @return string
    */
   public function getServer($country, $tld) {
      $retorno = null;
      $servers = $this->servers;
      if (isset($servers[$country])) {
         $lista_servers = $servers[$country];
         if (isset($lista_servers[$tld])) {
            $retorno = $lista_servers[$tld];
         } else {
            $retorno = $lista_servers ['default'];
         }
      } else {
         $retorno = $servers['default'];
      }
      return $retorno;
   }

   public function getErrors() {
      $a = $this->errors;
      $this->errors = array();
      return $a;
   }

   public function getCheckboxCountries($countries = array('iii' => ''), $checados = array('iii', 'br')) {
      if (!$countries || !is_array($countries) || !count($countries)) {
         return array();
      }

      $countries_arr = array();
      foreach ($countries as $k => $c) {
         $checked = '';
         if (in_array($c, $checados)) {
            $checked = 'checked="checked"';
         }
         $countries_arr[] = "<input type='checkbox' class='domains_chks' name='chosen_countries[]' value='$c' $checked /> " . ($c == 'iii' ? 'Internacional' : $c);
      }
      return $countries_arr;
   }

   public function getCheckboxTLDS($tlds, $checados = array('com', 'org', 'net')) {
      // se não tiver nenhuma entrada pra $tlds, retorna false.
      if (!$tlds || !is_array($tlds) || !count($tlds)) {
         return false;
      }

      $tlds_arr = array();
      foreach ($tlds as $k => $dom) {
         $checked = '';
         if (in_array($dom, $checados)) {
            $checked = 'checked="checked"';
         }
         if ($dom !== "") {
            $tlds_arr[] = "<input type='checkbox' class='domains_chks' name='chosen_tlds[]' value='$dom' $checked/> $dom";
         }
      }
      return $tlds_arr;
   }

   public function results($domainbase, array $tlds, array $countries) {
      $res = array();
      foreach ($tlds as $kd => $d) {
         foreach ($countries as $kc => $c) {
            $domain = $domainbase . "." . $d . ($c != 'iii' ? "." . $c : '');
            $sv = $this->getServer($c, $d);
            $findArr = $this->getFindArray($c, $d);
            $res[] = $this->getDomainResult($domain, $sv, $findArr);
         }
      }
      return $res;
   }

}

?>
