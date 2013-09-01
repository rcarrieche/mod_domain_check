<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$label = $params->get('label', '');
$buttontext = $params->get('buttontext', '');
$resulttext = $params->get('resulttext', '');
$checkall = $params->get('checkall', '');
$pretext = $params->get('pretext', '');
$forwardurl = $params->get('forwardurl', '');
$whois_server = $params->get('whois_server', ''); 
$avaliable_text = $params->get('avaliable_text', 'Avaliable'); 
$taken_text = $params->get('taken_text', 'Taken'); 

$servers = array(
    'iii' => array(
        'com' =>'whois.crsnic.net',
        'net' =>'whois.crsnic.net',
        'edu' =>'whois.crsnic.net',
        'info' => 'whois.afilias.net',
        'org' => 'whois.publicinterestregistry.net',
        'biz' => 'whois.neulevel.biz'
    ),
    'br' => array('whois.registro.br'),
    'ca' => array('whois.cira.ca'),
    'uk' => array('whois.nic.uk')
);

// whois.crsnic.net
// whois.registro.br
/*
 * 
      $tlds = array("com=whois.crsnic.net", "net=whois.crsnic.net", "org=whois.publicinterestregistry.net", "info=whois.afilias.net", "biz=whois.neulevel.biz", "us=whois.nic.us", "co.uk=whois.nic.uk", "org.uk=whois.nic.uk", "ltd.uk=whois.nic.uk", "ca=whois.cira.ca", "cc=whois.nic.cc", "edu=whois.crsnic.net", "com.au=whois.aunic.net", "net.au=whois.aunic.net", "de=whois.denic.de", "ws=whois.worldsite.ws", "sc=whois2.afilias-grs.net");
 */

$num_domains = 12;
$domains = array();
for ($i = 1; $i <= $num_domains; $i++) {
   $domains[$i] = $params->get('domain' . $i, '');
}
$num_countries = 3;
$countries = array();
for ($i = 0; $i <= $num_countries; $i++) {
   $countries[$i] = $params->get('country' . $i, '');
}
?>

<?php if ($pretext != "") { ?>
   <div style="margin-bottom:8px">
   <?php echo $pretext ?>
   </div> <?php
}


function getCheckboxDomains($domains, $checados = array('com', 'org', 'net')) {
   // se não tiver nenhuma entrada pra $domains, retorna false.
   if (!$domains || !is_array($domains) || !count($domains)) {
      return false;
   }

   $domains_arr = array();
   foreach ($domains as $k => $dom) {
      $checked = '';
      if(in_array($dom, $checados)){
         $checked = 'checked="checked"';
      }
      if ($dom !== "") { 
         $domains_arr[] = "<input type='checkbox' name='$dom' value='$dom' $checked/> $dom'";
      }
   }
   return $domains_arr;
}


function getCheckboxCountries($countries = array('iii' => ''), $checados = array('iii', 'br')) {
   if (!$countries || !is_array($countries) || !count($countries)) {
      return array();
   }

   $countries_arr = array();
   foreach ($countries as $k => $c) {
      $checked = '';
      if(in_array($c, $checados)){
         $checked = 'checked="checked"';
      }
      if ($c && $c != "iii") { 	
         $countries_arr[] = "<input type='checkbox' name='$c' value='$c' $checked /> $c";
      } else if($c === 'iii' ){ 
         $countries_arr[] = "<input type='checkbox' name='iii' value='iii' $checked /> Internacional";
      }
   }
   return $countries_arr;
}

function checkDomain($domain, $server, $findText) {
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


function showDomainResult($domain, $server, $findText, $arr) {
   $avaliable_text = isset($arr['avaliable_text']) ? $arr['avaliable_text'] : 'Avaliable';
   $taken_text = isset($arr['taken_text']) ? $arr['taken_text'] : 'Taken';

   $chk = checkDomain($domain, $server, $findText);
   if ($chk) {
      echo "<div style='margin-bottom:4px'>$domain $avaliable_text</div>";
   } else {
      echo "<div style='margin-bottom:4px'>$domain $taken_text</div>";
   }
}


function getFindText($country, $tld, $conf = null){
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
   
   if(isset($servers[$country])){
      $lista_servers = $servers[$country];
      if($country === 'iii'){
         $retorno = isset($lista_servers[$tld]) ? $lista_servers[$tld] : $lista_servers['com'];
      } else if(is_array($lista_servers) && count($lista_servers > 1)){
         $retorno = isset($lista_servers[$tld]) ? $lista_servers[$tld] : $lista_servers['com'];
      }else {
         $retorno = $lista_servers[0];
      }
   }
   return $retorno;
}


function getServer($country, $tld, $server_default = null){
   $retorno = null;
   $servers = array(
       'iii' => array(
           'com' =>'whois.crsnic.net',
           'net' =>'whois.crsnic.net',
           'edu' =>'whois.crsnic.net',
           'info' => 'whois.afilias.net',
           'org' => 'whois.publicinterestregistry.net',
           'biz' => 'whois.neulevel.biz'
       ),
       'br' => array('whois.registro.br'),
       'ca' => array('whois.cira.ca'),
       'uk' => array('whois.nic.uk')
   );
   
   if(isset($servers[$country])){
      $lista_servers = $servers[$country];
      if(isset($lista_servers[$tld])){
         $retorno = $lista_servers[$tld];
      } else if(isset($lista_servers[0])){
         $retorno = $lista_servers [0];
      } else {
         $retorno = $server_default;
      }
   } else {
      $retorno = $server_default;
   }
   return $retorno;
}


?>

<div id="main">
   <form action="<?php echo $forwardurl ?>" method="post" name="domain" id="domain">
      <span style="font-weight:bold">
         <?php echo $label ?> :
      </span>
      <div style="margin-top:8px; margin-bottom:8px">
         <input class="text" name="domainname" type="text" />
      </div>
      <div style="margin-top:8px; margin-bottom:8px">
         <!-- 
         <div style="margin-bottom:8px">
            <input type="checkbox" name="all" checked="checked"/> <?php echo $checkall ?>
         </div>
         -->
         <div style="margin-bottom:8px">
         <?php getCheckboxDomains($domains);?> 
         </div>
         <div style="margin-bottom:8px">
         <?php getCheckboxCountries($countries);?> 
         </div>
      </div>
      <input class="button" type="submit" name="submitBtn" value="<?php echo $buttontext ?>"/>
   </form>


   <?php
   if (isset($_POST['submitBtn'])) {
      // validar com regex
       
      if(isset($_POST['domainname'])){
         $domainbase = $_POST['domainname'];
      } else {
         return false;
      }

      $chosen_domains = array();
      foreach($domains as $k => $dom){
         if(isset($_POST[$dom])){
            $chosen_domains[$k] = $dom;
         }
      }
      $chosen_countries= array();
      foreach($countries as $k => $c){
         //var_dump($k, $c);
         //echo "<br />";
         if(isset($_POST[$c])){
            $chosen_countries[$k] = $c;
            //var_dump($chosen_countries[$k]);
            //echo "<br />";
         }
      }

      if (strlen($domainbase) > 0) { // verificação pela regex?>
         <div id="caption" style="margin-top:8px; margin-bottom:8px; font-weight:bold"><?php echo $resulttext ?> :</div>
         <div id="result"> <?php
         //echo "server antes do getServer: ".$whois_server."<br />";
         $sv = $whois_server;
         //var_dump($domains, $countries);
         //var_dump($chosen_domains, $chosen_countries);
         foreach($chosen_domains as $kd => $d){
            foreach($chosen_countries as $kc => $c){
               //echo "pesquisando $d , $c<br />";
               $domain = $domainbase . ".".$d.($c!='iii' ? ".".$c : '');
               //$whois_server = getServer($c, $d, $whois_server);
               //$txt = 'No match for';
               if(!isset($whois_server) || $whois_server == ''){
                  $sv= getServer($c, $d);
                  //$txt = 'No match for';
                  //$txt = getFindText($c, $d);
                  if($c == 'iii' && $d == 'org'){
                     $txt = 'NOT FOUND';
                  } else {
                     $txt = 'No match for';
                  }
               } else {
                  $txt = 'No match for';
               }
               //echo "domain: $domain   <br />server: ".$whois_server."<br />    findtext: ".$txt."<br />";
               showDomainResult($domain, $sv, $txt, array('avaliable_text' => $avaliable_text, 'taken_text' => $taken_text));
            }
         }
            ?>

         </div>
      <?php
   }
}
?>    

</div>
