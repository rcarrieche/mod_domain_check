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
       'br' => array( 'default' => 'whois.registro.br'),
       'ca' => array('default' => 'whois.cira.ca'),
       'uk' => array('default' => 'whois.nic.uk')
   );
   protected $checados;
   private static $instance = null;

   private function __construct() {
      
   }

   public static function getInstance() {
      if (self::instance === null) {
         self::$instance = new modDomainCheckHelper();
      }
      return self::$instance;
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
            $domains_arr[] = "<input type='checkbox' name='$dom' value='$dom' $checked/> $dom'";
         }
      }
      return $domains_arr;
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

   public function getDomainResult($domain, $server, $findText, $arr) {
      $avaliable_text = isset($arr['avaliable_text']) ? $arr['avaliable_text'] : 'Avaliable';
      $taken_text = isset($arr['taken_text']) ? $arr['taken_text'] : 'Taken';

      $chk = checkDomain($domain, $server, $findText);
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
   public function getFindText($country, $tld, $conf = 'avaliable') {
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

   public function getServer($country, $tld) {
      $retorno = null;
      $servers = $this->servers;
      if (isset($servers[$country])) {
         $lista_servers = $servers[$country];
         if (isset($lista_servers[$tld])) {
            $retorno = $lista_servers[$tld];
         } else if (isset($lista_servers[0])) {
            $retorno = $lista_servers [0];
         }
      }
      return $retorno;
   }

}

?>
