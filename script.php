<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class mod_domainCheckInstallerScript {

   function postflight($type, $parent) {
      // $parent is the class calling this method
      // $type is the type of change (install, update or discover_install)

      if ($type == 'install') {
         $db = JFactory::getDBO();
         $query = $db->getQuery(true);
         $query->update($db->quoteName('#__extensions'));
         $defaults = '{"moduleclass_sfx":"lalala","pretext":"kkkkkk","label":"Nome do dom\u00ednio","buttontext":"Verificar Dom\u00ednio","checkall":"Marcar Todos","resulttext":"Resultados da busca","forwardurl":"","avaliable_text":"Dispon\u00edvel","taken_text":"Indispon\u00edvel","tlds":["com","net","org","gov","info"],"countries":["iii","br"]}';
         $query->set($db->quoteName('params') . ' = ' . $db->quote($defaults));
         $query->where($db->quoteName('name') . ' = ' . $db->quote('mod_domain_check')); // com_XXX is your component 
         $db->setQuery($query);
         $db->query();
      }
   }

}

?>
