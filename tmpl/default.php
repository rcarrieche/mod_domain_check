<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
echo $x;
?>

<?php if ($pretext != "") { ?>
   <div style="margin-bottom:8px">
   <?php echo $pretext ?>
   </div> <?php
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
