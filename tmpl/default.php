<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
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
   </div> <?php
} ?>


<div id="main">
   <form action="<?php echo $forwardurl ?>" method="post" name="domain" id="domain">
      <span class="label"> <?php echo $label ?> : </span>
      <div class="domainname">
         <input class="text" name="domainname" type="text" />
      </div>
      <div class="checkboxes">
         <!-- 
         <div style="margin-bottom:8px">
            <input type="checkbox" name="all" checked="checked"/> <?php echo $checkall ?>
         </div>
         -->
         <div>
            <?php
            $chk_domains = $dck->getCheckboxDomains($domains);
            foreach($chk_domains as $cd){
               echo $cd;
            }
            ?> 
         </div>
         <div>
            <?php $chk_countries = $dck->getCheckboxCountries($countries);
            foreach($chk_countries as $cc){
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
         if(isset($_POST[$c])){
            $chosen_countries[$k] = $c;
         }
      }

      if (strlen($domainbase) > 0) { // verificação pela regex?>
         <div id="caption" ><?php echo $resulttext ?>: </div>
         <div id="result"> <?php
            $results = $dck->results($domainbase, $chosen_domains, $chosen_countries);
            foreach($results as $res){
               print $res;
            } ?>
         </div>
      <?php
   }
}

?>
</div>
