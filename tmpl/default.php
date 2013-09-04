<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
   $(document).ready(function(){
      $('#check_all_domains').click(function(){
         var c = $(this).is(':checked')?'checked': false;
         console.info(c);
         if(c){
            $('.domains_and_countries').attr('checked', c);
         } else {
            $('.domains_and_countries').removeAttr('checked');
         }
      });

   });

</script>

<style>
   .pretext{
      margin-bottom:8px;
   }
   .label{
      font-weight:bold;
   }
   .domainname {
      margin-top:8px; 
      margin-bottom:8px;
   }
   .checkboxes{
      margin-top:8px; margin-bottom:8px;
   }
   #caption {
      margin-top:8px; 
      margin-bottom:8px; 
      font-weight:bold;
   }
</style>

<?php if ($pretext != "") { ?>
   <div class="pretext">
      <?php echo $pretext ?>
   </div> <?php }
   ?>


<div id="main">
   <form action="<?php echo $forwardurl ?>" method="post" name="domain" id="domain">
      <span class="label"> <?php echo $label ?> : </span>
      <div class="domainname">
         <input class="text" name="domainname" type="text" />
      </div>
      <div class="checkboxes">
         <div id="check_all">
            <input type="checkbox" id="check_all_domains" name="check_all_domains"/> <label for="check_all_domains"><?php echo $checkall ?></label>
         </div>
         <div>
            <?php
            $pogdomains = array();
            if(!isset($_SESSION['pogdomains'])){
               $pogdomains = array('com', 'net', 'info', 'org');
            } else {
               $pogdomains = $_SESSION['pogdomains'];
            }
            $chk_domains = $dck->getCheckboxDomains($domains, $pogdomains);
            foreach ($chk_domains as $cd) {
               echo $cd;
            }
            ?> 
         </div>
         <div>
            <?php
            $pogcountries = array();
            if(!isset($_SESSION['pogcountries'])){
               $pogcountries = array('iii', 'br');
            } else {
               $pogcountries = $_SESSION['pogcountries'];
            }
            $chk_countries = $dck->getCheckboxCountries($countries, $pogcountries);
            foreach ($chk_countries as $cc) {
               echo $cc;
            }
            ?> 
         </div>
      </div>
      <div>
         <input class="button" type="submit" name="submitBtn" value="<?php echo $buttontext ?>"/>
      </div>
   </form>


   <?php
   if (isset($_POST['submitBtn'])) {
      // validar com regex

      if (isset($_POST['domainname'])) {
         $domainbase = $_POST['domainname'];
      } else {
         return false;
      }

      $chosen_domains = array();
      foreach ($domains as $k => $dom) {
         if (isset($_POST[$dom])) {
            $chosen_domains[$k] = $dom;
         }
      }
      $chosen_countries = array();
      foreach ($countries as $k => $c) {
         if (isset($_POST[$c])) {
            $chosen_countries[$k] = $c;
         }
      }
      $_SESSION['pogcountries'] = $chosen_countries;
      $_SESSION['pogdomains'] = $chosen_domains;
      ?> <div id="caption" ><?php echo $resulttext ?>: </div> <?php 
      if (!count($chosen_countries) || !count($chosen_domains)) { ?>
         <div id="result error"> Error: select, at least, 1 country and 1 top level domain.</div><?php
      } else if (strlen($domainbase) > 2) { // verificação pela regex
         ?>
         <div id="result"> <?php
         $results = $dck->results($domainbase, $chosen_domains, $chosen_countries);
         foreach ($results as $res) {
            print $res;
         }
         ?>
         </div> <?php 
      } else { ?>
         <div id="result error"> Error: domain name is too short.</div><?php
      }
      unset($_POST['submitBtn']);
   }
   ?>
</div>
