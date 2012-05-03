<?
/*-- TODO ---------------------------//
Writeup how to use the VALIDATE class, add in support for form id checks
Complete the number and date validation
Finish the GenerateJS stuff
//-----------------------------------*/

class VALIDATE {
	var $Fields=array();

	function SetFields($FieldName,$Required,$FieldType,$ErrorMessage,$Options=array()) {
		$this->Fields[$FieldName]['Type']=strtolower($FieldType);
		$this->Fields[$FieldName]['Required']=$Required;
		$this->Fields[$FieldName]['ErrorMessage']=$ErrorMessage;
		if(!empty($Options['maxlength'])) {
			$this->Fields[$FieldName]['MaxLength']=$Options['maxlength'];
		}
		if(!empty($Options['minlength'])) {
			$this->Fields[$FieldName]['MinLength']=$Options['minlength'];
		}
		if(!empty($Options['comparefield'])) {
			$this->Fields[$FieldName]['CompareField']=$Options['comparefield'];
		}
		if(!empty($Options['allowperiod'])) {
			$this->Fields[$FieldName]['AllowPeriod']=$Options['allowperiod'];
		}
		if(!empty($Options['allowcomma'])) {
			$this->Fields[$FieldName]['AllowComma']=$Options['allowcomma'];
		}
		if(!empty($Options['inarray'])) {
			$this->Fields[$FieldName]['InArray']=$Options['inarray'];
		}
		if(!empty($Options['regex'])) {
			$this->Fields[$FieldName]['Regex']=$Options['regex'];
		}
	}

	function ValidateForm($ValidateArray) {
		reset($this->Fields);
		foreach ($this->Fields as $FieldKey => $Field) {
			$ValidateVar=$ValidateArray[$FieldKey];

			if($ValidateVar!="" || !empty($Field['Required']) || $Field['Type']=="date") {
				if($Field['Type']=="string") {
					if(isset($Field['MaxLength'])) { $MaxLength=$Field['MaxLength']; } else { $MaxLength=255; }
					if(isset($Field['MinLength'])) { $MinLength=$Field['MinLength']; } else { $MinLength=1; }

					if(strlen($ValidateVar)>$MaxLength) { return $Field['ErrorMessage']; }
					elseif(strlen($ValidateVar)<$MinLength) { return $Field['ErrorMessage']; }

				} elseif($Field['Type']=="number") {
					if(isset($Field['MaxLength'])) { $MaxLength=$Field['MaxLength']; } else { $MaxLength=''; }
					if(isset($Field['MinLength'])) { $MinLength=$Field['MinLength']; } else { $MinLength=0; }

					$Match='0-9';
					if(isset($Field['AllowPeriod'])) { $Match.='.'; }
					if(isset($Field['AllowComma'])) { $Match.=','; }

					if(preg_match('/[^'.$Match.']/', $ValidateVar) || strlen($ValidateVar)<1) { return $Field['ErrorMessage']; }
					elseif($MaxLength!="" && $ValidateVar>$MaxLength) { return $Field['ErrorMessage']."!!"; }
					elseif($ValidateVar<$MinLength) { return $Field['ErrorMessage']."$MinLength"; }

				} elseif($Field['Type']=="email") {
					if(isset($Field['MaxLength'])) { $MaxLength=$Field['MaxLength']; } else { $MaxLength=255; }
					if(isset($Field['MinLength'])) { $MinLength=$Field['MinLength']; } else { $MinLength=6; }

					if(!preg_match("/^".EMAIL_REGEX."$/i", $ValidateVar)) { return $Field['ErrorMessage']; }
					elseif(strlen($ValidateVar)>$MaxLength) { return $Field['ErrorMessage']; }
					elseif(strlen($ValidateVar)<$MinLength) { return $Field['ErrorMessage']; }

				} elseif($Field['Type']=="link") {
					if(isset($Field['MaxLength'])) { $MaxLength=$Field['MaxLength']; } else { $MaxLength=255; }
					if(isset($Field['MinLength'])) { $MinLength=$Field['MinLength']; } else { $MinLength=10; }

					if(!preg_match('/^(https?):\/\/([a-z0-9\-\_]+\.)+([a-z]{1,5}[^\.])(\/[^<>]+)*$/i', $ValidateVar)) { return $Field['ErrorMessage']; }
					elseif(strlen($ValidateVar)>$MaxLength) { return $Field['ErrorMessage']." (must be < $MaxLength)"; }
					elseif(strlen($ValidateVar)<$MinLength) { return $Field['ErrorMessage']." (must be > $MinLength)"; }

				} elseif($Field['Type']=="username") {
                                if(isset($Field['MaxLength'])) { $MaxLength=$Field['MaxLength']; } else { $MaxLength=20; }
                                if(isset($Field['MinLength'])) { $MinLength=$Field['MinLength']; } else { $MinLength=1; }

                                if(preg_match('/[^a-z0-9_\-?]/i', $ValidateVar)) { return $Field['ErrorMessage']; }
                                elseif(strlen($ValidateVar)>$MaxLength) { return $Field['ErrorMessage']; }
                                elseif(strlen($ValidateVar)<$MinLength) { return $Field['ErrorMessage']; }

				} elseif($Field['Type']=="checkbox") {
                                if(!isset($ValidateArray[$FieldKey])) { return $Field['ErrorMessage']; }

				} elseif($Field['Type']=="compare") {
                                if($ValidateArray[$Field['CompareField']]!=$ValidateVar) { return $Field['ErrorMessage']; }

				} elseif($Field['Type']=="inarray") {
                                if(array_search($ValidateVar, $Field['InArray'])===false) { return $Field['ErrorMessage']; }

				} elseif($Field['Type']=="regex") {
                                if(!preg_match($Field['Regex'], $ValidateVar)) { return $Field['ErrorMessage']; }
                         
                                
                                
				} elseif($Field['Type']=="image") {
                            
                            // Validate an imageurl : 1) valid url form 2)length 3)whilelist
                                // Get parameters to validate against from fields set  
                                if(isset($Field['MaxLength'])) { $MaxLength=$Field['MaxLength']; } else { $MaxLength=255; }
                                if(isset($Field['MinLength'])) { $MinLength=$Field['MinLength']; } else { $MinLength=10; }

                                if(isset($Field['Regex'])) { $WLRegex=$Field['Regex']; } else { $WLRegex='/nohost.com/'; }
                              
                                // get validation result
                                $result = $this->ValidateImageUrl($ValidateVar, $MinLength, $MaxLength, $WLRegex); 
                                if ($result !== TRUE){ return $result; } 
                              
                              
                         
				} elseif($Field['Type']=="desc") {
                        
                                // desc Type gets 3 checks for the price of one 
                                // 1)desc length 2)imglink as valid url 3)imglinks against whitelist
                                // this kind of breaks the pattern of this class but screw it... 
                                // we will hardcode changes to return messages as this class matches fields by 
                                // name (so one check per field only) and I dont want to redesign it
                            
                                if(isset($Field['MaxLength'])) { $MaxLength=$Field['MaxLength']; } else { $MaxLength=255; }
                                if(isset($Field['MinLength'])) { $MinLength=$Field['MinLength']; } else { $MinLength=1; }

                                if(strlen($ValidateVar)>$MaxLength) { 
                                    $Field['ErrorMessage'] =  "Your ".$Field['ErrorMessage']." must be less than $MaxLength characters long.";  
                                    return $Field['ErrorMessage'];
                                }
                                elseif(strlen($ValidateVar)<$MinLength) { 
                                    $Field['ErrorMessage'] =  "Your ".$Field['ErrorMessage']." must be more than $MinLength characters long.";  
                                    return $Field['ErrorMessage'];
                                }
                              
                              
                                //  Check image urls inside the desc text against the whitelist.
                                //  the whitelist is set inside the $Field['Regex'] var (in options arrary in ->SetFields)
                            
                                if(isset($Field['Regex'])) { $WLRegex=$Field['Regex']; } else { $WLRegex='/nohost.com/'; }
                              
                                // get all the image urls in the field ; inside [img][/img] 
                                $num = preg_match_all('#\[img\](.*?)\[/img\]#ism', $ValidateVar, $imageurls);
                            
                                if($num) { // if there are no img tags then it validates 
                                    for ($j=0;$j<$num;$j++) {  
                                         // validate each image url  
                                         // (for the moment use hardcoded image lengths but ideally they should
                                         // probably be taken from some new option fields).
                                        $result = $this->ValidateImageUrl($imageurls[1][$j], 12, 255, $WLRegex); 
                                        if ($result !== TRUE){ return $Field['ErrorMessage'].' field: ' .$result; } 
                                     }
                                } /*else {  // if there are no img tags then it validates unless required flag is set
                                    if (!empty($Field['Required'])) {   
                                        // this kind of breaks the pattern of this class but screw it... 
                                        // we will hardcode a change to return message to avoid having to do the 
                                        // preg_match_all(regex) again or adding another return msg variable
                                        $Field['ErrorMessage'] = "You are required to have screenshots for every scene";
                                        return $Field['ErrorMessage'];
                                    } 
                                }*/
                        
                        }
			}  // if (dovalidation)
		} // foreach
	} // function

     
     
     
     /* --------------------------------
      * Validates the passed imageurl with the passed parameters
        Returns TRUE if it validates and a user readable error message if it fails 
      ----------------------------------- */
     private function ValidateImageUrl($Imageurl, $MinLength, $MaxLength, $WhitelistRegex) {
          
        $ErrorMessage = "'$Imageurl' is not a valid url.";
        
        if(!preg_match('/^(https?):\/\/([a-z0-9\-\_]+\.)+([a-z]{1,5}[^\.])(\/[^<>]+)*$/i', $Imageurl)) {  
            return $ErrorMessage;  
        } 
        elseif(strlen($Imageurl)>$MaxLength) { 
            return "$ErrorMessage (must be < $MaxLength)";  
        }
        elseif(strlen($Imageurl)<$MinLength) { 
            return "$ErrorMessage (must be > $MinLength)";  
        } 
        elseif(!preg_match($WhitelistRegex, $Imageurl)) { 
            return "$Imageurl is not on an approved pichost."; 
        }
        else { // hooray it validated
            return TRUE;
        }
     }
   
     
     
     
     
     /* --------------------------------
      * Returns a regex string in the form '/imagehost.com|otherhost.com|imgbox.com/i'
       for fast whitelist checking
      ----------------------------------- */
     function GetWhitelistRegex() {
           /*  wont work because we need to preg escape the host names
        $DB->query("SELECT 
                   GROUP_CONCAT(w.Imagehost SEPARATOR '|') AS Whitelist 
                   FROM imagehost_whitelist as w");
		*/
        global $DB; 
        $DB->query("SELECT w.Imagehost
                      FROM imagehost_whitelist as w");  
        if($DB->record_count()>0) {
            $pattern = '@'; 
            $div = '';
            while(list($host)=$DB->next_record()){
                $pattern .= $div . preg_quote($host, '@');
                $div = '|';
            } 
            $pattern .= '@i'; 
         }  else  {
             $pattern = '/nohost.com/i';
         }
         
         return $pattern; 
     }
     
     
     
     
     
     
     
     
	function GenerateJS($FormID) {
		$ReturnJS="<script type=\"text/javascript\" language=\"javascript\">\r\n";
		$ReturnJS.="//<![CDATA[\r\n";
		$ReturnJS.="function formVal() {\r\n";
		$ReturnJS.="	clearErrors('".$FormID."');\r\n";

		reset($this->Fields);
		foreach ($this->Fields as $FieldKey => $Field) {
			if($Field['Type']=="string") {
				$ValItem='	if($(\'#'.$FieldKey.'\').raw().value==""';
				if(!empty($Field['MaxLength'])) { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length>'.$Field['MaxLength']; } else { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length>255'; }
				if(!empty($Field['MinLength'])) { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length<'.$Field['MinLength']; }
				$ValItem.=') { return showError(\''.$FieldKey.'\',\''.$Field['ErrorMessage'].'\'); }'."\r\n";

			} elseif($Field['Type']=="number") {
				$Match='0-9';
				if(!empty($Field['AllowPeriod'])) { $Match.='.'; }
				if(!empty($Field['AllowComma'])) { $Match.=','; }

				$ValItem='	if($(\'#'.$FieldKey.'\').raw().value.match(/[^'.$Match.']/) || $(\'#'.$FieldKey.'\').raw().value.length<1';
				if(!empty($Field['MaxLength'])) { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value/1>'.$Field['MaxLength']; }
				if(!empty($Field['MinLength'])) { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value/1<'.$Field['MinLength']; }
				$ValItem.=') { return showError(\''.$FieldKey.'\',\''.$Field['ErrorMessage'].'\'); }'."\r\n";

			} elseif($Field['Type']=="email") {
				$ValItem='	if(!validEmail($(\'#'.$FieldKey.'\').raw().value)';
				if(!empty($Field['MaxLength'])) { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length>'.$Field['MaxLength']; } else { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length>255'; }
				if(!empty($Field['MinLength'])) { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length<'.$Field['MinLength']; } else { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length<6'; }
				$ValItem.=') { return showError(\''.$FieldKey.'\',\''.$Field['ErrorMessage'].'\'); }'."\r\n";

			} elseif($Field['Type']=="link") {
				$ValItem='	if(!validLink($(\'#'.$FieldKey.'\').raw().value)';
				if(!empty($Field['MaxLength'])) { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length>'.$Field['MaxLength']; } else { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length>255'; }
				if(!empty($Field['MinLength'])) { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length<'.$Field['MinLength']; } else { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length<10'; }
				$ValItem.=') { return showError(\''.$FieldKey.'\',\''.$Field['ErrorMessage'].'\'); }'."\r\n";

			} elseif($Field['Type']=="username") {
				$ValItem='	if($(\'#'.$FieldKey.'\').raw().value.match(/[^a-zA-Z0-9_\-]/)';
				if(!empty($Field['MaxLength'])) { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length>'.$Field['MaxLength']; }
				if(!empty($Field['MinLength'])) { $ValItem.=' || $(\'#'.$FieldKey.'\').raw().value.length<'.$Field['MinLength']; }
				$ValItem.=') { return showError(\''.$FieldKey.'\',\''.$Field['ErrorMessage'].'\'); }'."\r\n";

			} elseif($Field['Type']=="regex") {
				$ValItem='	if(!$(\'#'.$FieldKey.'\').raw().value.match('.$Field['Regex'].')) { return showError(\''.$FieldKey.'\',\''.$Field['ErrorMessage'].'\'); }'."\r\n";

			} elseif($Field['Type']=="date") {
				$DisplayError=$FieldKey."month";
				if(isset($Field['MinLength']) && $Field['MinLength']==3) { $Day='$(\'#'.$FieldKey.'day\').raw().value'; $DisplayError.=",".$FieldKey."day"; } else { $Day="1"; }
				$DisplayError.=",".$FieldKey."year";
				$ValItemHold='	if(!validDate($(\'#'.$FieldKey.'month\').raw().value+\'/\'+'.$Day.'+\'/\'+$(\'#'.$FieldKey.'year\').raw().value)) { return showError(\''.$DisplayError.'\',\''.$Field['ErrorMessage'].'\'); }'."\r\n";

				if(empty($Field['Required'])) {
					$ValItem='	if($(\'#'.$FieldKey.'month\').raw().value!=""';
					if(isset($Field['MinLength']) && $Field['MinLength']==3) { $ValItem.=' || $(\'#'.$FieldKey.'day\').raw().value!=""'; }
					$ValItem.=' || $(\'#'.$FieldKey.'year\').raw().value!="") {'."\r\n";
					$ValItem.=$ValItemHold;
					$ValItem.="	}\r\n";
				} else {
					$ValItem.=$ValItemHold;
				}

			} elseif($Field['Type']=="checkbox") {
				$ValItem='	if(!$(\'#'.$FieldKey.'\').checked) { return showError(\''.$FieldKey.'\',\''.$Field['ErrorMessage'].'\'); }'."\r\n";

			} elseif($Field['Type']=="compare") {
				$ValItem='	if($(\'#'.$FieldKey.'\').raw().value!=$(\'#'.$Field['CompareField'].'\').raw().value) { return showError(\''.$FieldKey.','.$Field['CompareField'].'\',\''.$Field['ErrorMessage'].'\'); }'."\r\n";
			}

			if(empty($Field['Required']) && $Field['Type']!="date") {
				$ReturnJS.='	if($(\'#'.$FieldKey.'\').raw().value!="") {'."\r\n	";
				$ReturnJS.=$ValItem;
				$ReturnJS.="	}\r\n";
			} else {
				$ReturnJS.=$ValItem;
			}
			$ValItem='';
		}

		$ReturnJS.="}\r\n";
		$ReturnJS.="//]]>\r\n";
		$ReturnJS.="</script>\r\n";
		return $ReturnJS;
	}
}
?>