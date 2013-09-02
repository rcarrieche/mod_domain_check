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
   protected $checados;
   private static $instance = null;
   protected $avaliable_text = 'Avaliable';
   protected $taken_text = 'Taken';

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
      /*
        //echo $response;
        if (strpos($response, $findText)) {
        return true;
        } else {
        return false;
        }
       * 
       */
   }

   public function checkDomain($domain, $server, $findArray) {
      $response = $this->getServerResponse($domain, $server);
      $tem = false;
      foreach ($findArray as $ftext) {
         if (strpos($response, $ftext)) {
            $tem = true;
         }
      }
      return $tem;
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
         return "<div class='avaliable_text'>$domain $avaliable_text</div>";
      } else {
         return "<div class='taken_text'>$domain $taken_text</div>"; //margin-bottom:4px
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
      $comum = array(
          'avaliable' => array('No match for'),
          'taken' => array(),
          'error' => array()
      );
      $textos = array(
          'iii' => array(
              'com' => $comum,
              'org' => array(
                  'avaliable' => array('NOT FOUND'),
                  'taken' => array(),
                  'error' => array()
              ),
              'default' => $comum
          ),
          'br' => array(
              'default' => $comum
          ),
          'default' => array('No match for')
      );
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
         if ($c && $c != "iii") {
            $countries_arr[] = "<input type='checkbox' name='$c' value='$c' $checked /> $c";
         } else if ($c === 'iii') {
            $countries_arr[] = "<input type='checkbox' name='iii' value='iii' $checked /> Internacional";
         }
      }
      return $countries_arr;
   }

   public function getCheckboxDomains($domains, $checados = array('com', 'org', 'net')) {
      // se não tiver nenhuma entrada pra $domains, retorna false.
      if (!$domains || !is_array($domains) || !count($domains)) {
         return false;
      }

      $domains_arr = array();
      foreach ($domains as $k => $dom) {
         $checked = '';
         if (in_array($dom, $checados)) {
            $checked = 'checked="checked"';
         }
         if ($dom !== "") {
            $domains_arr[] = "<input type='checkbox' name='$dom' value='$dom' $checked/> $dom";
         }
      }
      return $domains_arr;
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
