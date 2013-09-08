<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
   $(document).ready(function() {
      $('#check_all_domains').click(function() {
         $('.domains_chks').prop('checked', $(this).is(':checked'));
      });

   });

</script>


<?php if ($pretext != "") { ?>
   <div class="pretext">
      <?php echo $pretext ?>
   </div> <?php }
   ?>


<div id="main">
   <div id="form_domain_check">
      <form action="<?php echo $forwardurl ?>" method="post" name="domain_check" id="domain_check">
         <!-- <span class="label"> <?php echo $label ?> : </span> -->
         <h3> <?php echo $label ?> : </h3>
         <div class="domainname">
            <span class="pre_domain_text">www.</span><input class="text" name="domainbase" type="text" />
            <input class="button submit_domain" type="submit" name="submitBtn" value="<?php echo $buttontext ?>"/>
         </div>
         <div class="checkboxes">
            <div id="check_all">
               <input type="checkbox" id="check_all_domains" name="check_all_domains"/> <label for="check_all_domains"><?php echo $checkall ?></label>
            </div>
            <div class="tlds_and_countries">
               <?php
               $chk_tlds = $dck->getCheckboxTLDS($tlds, $chosen_tlds);
               foreach ($chk_tlds as $cd) {
                  echo '<span class="checkbox_wrapper">'.$cd.'</span>';
               }
               ?> 
            </div>
            <div class="tlds_and_countries">
               <?php
               $chk_countries = $dck->getCheckboxCountries($countries, $chosen_countries);
               foreach ($chk_countries as $cc) {
                  echo '<span class="checkbox_wrapper">'.$cc.'</span>';
               }
               ?> 
            </div>
         </div>
         <!-- 
         <div>
            <input class="button submit_domain" type="submit" name="submitBtn" value="<?php echo $buttontext ?>"/>
         </div>
         -->
      </form>
   </div>


   <?php
   if (isset($_POST['submitBtn'])) {
      // validar com regex
      ?> 
      <div id="caption" ><?php echo $resulttext ?>: </div> 
      <?php if (!count($chosen_countries) || !count($chosen_tlds)) { ?>
         <div id="result error"> Error: select, at least, 1 country and 1 top level domain.</div><?php
      } else if (strlen($domainbase) > 2) { // verificação pela regex
         ?>
         <div id="result"> <?php
            $results = $dck->results($domainbase, $chosen_tlds, $chosen_countries);
            foreach ($results as $res) {
               print $res;
            }
            ?>
         </div> <?php } else {
            ?>
         <div id="result error"> Error: domain name is too short.</div><?php
      }
      $errors = $dck->getErrors();
      foreach ($errors as $e) {
         print $e;
      }
      unset($_POST['submitBtn']);
   }
   ?>
</div>
