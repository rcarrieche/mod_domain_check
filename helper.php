<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class modDomainCheckHelper {

   /**
    * Retrieves the hello message
    *
    * @param array $params An object containing the module parameters
    * @access public
    */
   public static function getHello($params) {
      return 'Hello, World!';
   }

   public static function getCheckboxDomains($domains, $checados = array('com', 'org', 'net')) {
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

   public static function getCheckboxCountries($countries = array('iii' => ''), $checados = array('iii', 'br')) {
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

   public static function checkDomain($domain, $server, $findText) {
      $con = fsockopen($server, 43);
      if (!$con)
         return false;
      fputs($con, $domain . "\r\n");
      $response = ' :';
      while (!feof($con)) {
         $response .= fgets($con, 128);
      }
      fclose($con);
      //echo $response;
      if (strpos($response, $findText)) {
         return true;
      } else {
         return false;
      }
   }

   public static function showDomainResult($domain, $server, $findText, $arr) {
      $avaliable_text = isset($arr['avaliable_text']) ? $arr['avaliable_text'] : 'Avaliable';
      $taken_text = isset($arr['taken_text']) ? $arr['taken_text'] : 'Taken';

      $chk = checkDomain($domain, $server, $findText);
      if ($chk) {
         echo "<div class='avaliable_text'>$domain $avaliable_text</div>";
      } else {
         echo "<div class='taken_text'>$domain $taken_text</div>"; //margin-bottom:4px
      }
   }

   public static function getFindText($country, $tld, $conf = null) {
      $retorno = null;
      $textos = array(
          'iii' => array(
              'avaliable' => array('Not match for'),
              'taken' => array(),
              'error' => array()
          ),
          'br' => array(
              'avaliable' => array('Domínio inexistente:', 'inexistente', 'No match for'),
              'taken' => array('owner:', 'ownerid:', 'entidade:', 'documento:', 'country'),
              'error' => array('Consulta inválida')
          ),
      );

      if (isset($servers[$country])) {
         $lista_servers = $servers[$country];
         if ($country === 'iii') {
            $retorno = isset($lista_servers[$tld]) ? $lista_servers[$tld] : $lista_servers['com'];
         } else if (is_array($lista_servers) && count($lista_servers > 1)) {
            $retorno = isset($lista_servers[$tld]) ? $lista_servers[$tld] : $lista_servers['com'];
         } else {
            $retorno = $lista_servers[0];
         }
      }
      return $retorno;
   }

   public static function getServer($country, $tld, $server_default = null) {
      $retorno = null;
      $servers = array(
          'iii' => array(
              'com' => 'whois.crsnic.net',
              'net' => 'whois.crsnic.net',
              'edu' => 'whois.crsnic.net',
              'info' => 'whois.afilias.net',
              'org' => 'whois.publicinterestregistry.net',
              'biz' => 'whois.neulevel.biz'
          ),
          'br' => array('whois.registro.br'),
          'ca' => array('whois.cira.ca'),
          'uk' => array('whois.nic.uk')
      );

      if (isset($servers[$country])) {
         $lista_servers = $servers[$country];
         if (isset($lista_servers[$tld])) {
            $retorno = $lista_servers[$tld];
         } else if (isset($lista_servers[0])) {
            $retorno = $lista_servers [0];
         } else {
            $retorno = $server_default;
         }
      } else {
         $retorno = $server_default;
      }
      return $retorno;
   }

}

?>
