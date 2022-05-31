<?php
namespace PaysonEmbedded{
    class Gui{
        /** @var string $colorScheme Color scheme of the checkout snippet("white", "black", "blue" (default), "red"). */
        public $colorScheme;
        /** @var string $locale Used to change the language shown in the checkout snippet ("se", "fi", "en" (default)). */
        public $locale;
        /** @var string $verfication  Can be used to add extra customer verfication ("bankid", "none" (default)). */
        public $verfication;
        /** @var bool $requestPhone  Can be used to require the user to fill in his phone number. */
        public $requestPhone;
        /** @var bool $phoneOptional Used to ask the user to fill in his phone number, but not strict required. */
        public $phoneOptional;
        /** @var list $countries  List of countries shown in the checkout snippet. */
        public $countries = array();
        
        public function __construct($locale = "sv", $colorScheme = "gray", $verification = "none", $requestPhone = NULL, $phoneOptional = NULL, $countries = NULL){
            $this->colorScheme = $colorScheme;
            $this->locale = $locale; 
            $this->verification = $verification;
            $this->requestPhone = $requestPhone;
            $this->phoneOptional = $phoneOptional;
            $this->countries = $countries;
        }

        public static function create($data) {
            return new Gui($data->locale, $data->colorScheme, $data->verification, $data->requestPhone, $data->phoneOptional, $data->countries);
        }
        
        public function toArray(){
            return get_object_vars($this);      
        }
    }
}

