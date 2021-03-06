<?php

/**
 * PHP SMCrypter 
 * Encrypts messages with a key symmetric
 * @link https://github.com/lleocastro/php-encrypter
 * @license https://github.com/lleocastro/php-encrypter/blob/master/LICENSE
 * @copyright 2016 Leonardo Carvalho <leonardo_carvalho@outlook.com>
 */

require_once ('ASCII.php');


class SMCrypter extends ASCII
{
    /**
     * Values for key generation
     * @var encrypted strings
     */
    private $keyValueMin = 'RE13QVRNPT1BTXdB';
    private $keyValueMax = 'VE81a1RPPT1RTzVr';

    /**
     * Symmetric key
     * @var encrypted string
     */
    private $key = null;

    /**
     * For replace space on text
     * @var string
     */
    private $breakText = '{#}';

    /**
     * Default construct
     */
    public function __construct()
    {}

    /**
     * Key Generator
     * Checks if there is already a key instance, if yes, returns, 
     * if not, create, it and then returns.
     * @return string encrypted $key
     */
    public function keyGenerator()
    {
        if($this->key == null):
            $this->key = $this->obscure(
            	(int) mt_rand(
            	    $this->illumin($this->keyValueMin), 
            	    $this->illumin($this->keyValueMax)
            	)
            );
            return $this->key;
        endif;

        return $this->key;
    }
    
    /**
     * Key Validator
     * Verifies that the key manually provided on method 'encode()' follows the 
     * standards of a valid key.
     * @param ((string) or (int)) $key
     * @return (int) $key, or (boolean) false
     */
    private function keyValidator($key)
    {
        if(((is_int($key)) && ($key >= $this->illumin($this->keyValueMin)) && 
                              ($key <= $this->illumin($this->keyValueMax)))):
            return $key;
        elseif((is_string($key)) && ($key != '') && (strlen($key) == 16)):
            return $this->illumin($key);
        endif;

        return false;
    }

    /**
     * Encryptor Messages
     * Get the numerical value of any character and encrypts with key.
     * @param ((string) or (int)) $key for encryption
     * @param (int) $value for encryption
     * @return (int) encrypted $value
     */
    public function encode($key, $text)
    {
        $text = (string) trim(htmlentities($text));
        return $this->translator(
            trim(htmlentities(strip_tags($this->keyValidator($key)))), 
            str_split(str_replace(' ', $this->breakText, $text))
        );
    }
    
    /**
     * Decryptor Messages
     * Get the value encrypted and decryption with the key.
     * @param ((string) or (int)) $key used in encrypting
     * @param (int) $value for decryption
     * @return (int) original value
     */
    public function decode($key, $text)
    {
        $text = (string) trim(htmlentities($text));
        return $this->reverse(
            trim(htmlentities(strip_tags($this->keyValidator($key)))), 
            explode(' ', $text)
        );
    }

    /**
     * Logic from Encryption
     * Crazy calculation for encryption. #sqn #haha
     * @param (int) (optional) $key for encryption
     * @param (int) $value for encryption
     * @return (int) encrypted $value
     */
    private function encrypter($key='', $value)
    {
    	$key = (($key=='')?$this->illumin($key):$key);
        return ((($value*$key)*$key)/$key);
    }
    
    /**
     * Logic from Decryption
     * Crazy calculation for decryption. #sqn #haha
     * @param (int) (optional) $key used in encrypting
     * @param (int) $value for decryption
     * @return (int) original value
     */
    private function decrypter($key='', $value)
    {
    	$key = (($key=='')?$this->illumin($key):$key);
        return ((($value/$key)/$key)*$key);
    }

    /**
     * Translate the Text to Numbers
     * @param (int) $key for encryption
     * @param (array) $characters 
     * @return (array) translated values
     */
    private function translator($key, array $characters)
    {
        $charactersConverted = [];
        for($i = 0; $i <= count($characters); $i++):
            for($x = 0; $x <= ASCII::sizeTable(); $x++):
                if(($characters[$i]) == (ASCII::getTableIndex($x))):
                    $charactersConverted[$i] = $this->encrypter(
                        $key, ASCII::getChar(ASCII::getTableIndex($x)));
                endif;
            endfor;
        endfor;
        return implode(' ', array_filter($charactersConverted));
    }

    /**
     * Translate the Numbers to Text
     * @param (int) $key for decryption
     * @param (array) $characters 
     * @return (array) translated values
     */
    private function reverse($key, array $charactersConverted)
    {
        $characters = [];
        for($i = 0; $i <= count($charactersConverted); $i++):
            for($x = 0; $x <= ASCII::sizeTable(); $x++):           
                if(("{$this->decrypter($key, $charactersConverted[$i])}")
                              == (ASCII::getChar(ASCII::getTableIndex($x)))):
                    $characters[$i] = ASCII::getTableIndex($x);
                endif;
            endfor;
        endfor;
        return str_replace($this->breakText, ' ', implode('', $characters));
    }
    
    /**
     * Logic from Encryption Key
     * The key is encrypted in Base64 then, divided in half, inverted and encrypted again
     * @param (int) $key for encryption
     * @return (string) encrypted $key
     */
    protected function obscure($encryptedData)
    {
        $encryptedData = base64_encode($encryptedData);
        return base64_encode(strrev(
            substr($encryptedData, (strlen($encryptedData)/2)-strlen($encryptedData),strlen($encryptedData)).
            substr($encryptedData, 0, (strlen($encryptedData)/2)-strlen($encryptedData))));
    }

    /**
     * Logic from Decryption Key
     * Reverse process of 'obscure()' to recover the original value.
     * @param (int) $key encrypted
     * @return (int) original $key
     */
    protected function illumin($encryptedData)
    {
    	$encryptedData = base64_decode($encryptedData);
        $encryptedData = strrev(
    	    substr($encryptedData, (strlen($encryptedData)/2)-strlen($encryptedData),strlen($encryptedData)).
    	    substr($encryptedData, 0, (strlen($encryptedData)/2)-strlen($encryptedData)));
        return base64_decode($encryptedData);
    }

}