<?php 

/**
 * @group Pepit
 */

require('/Pepit/Auth/Hash.php');

class PepitAuthHashTest extends PHPUnit_Framework_TestCase
{
    public function testPassHash()
    {

        $hash = new Pepit_Auth_Hash();
        
        $pbkdf2_vectors = array(
            array(
                'algorithm' => 'sha1', 
                'password' => "password", 
                'salt' => "salt", 
                'iterations' => 1, 
                'keylength' => 20, 
                'output' => "0c60c80f961f0e71f3a9b524af6012062fe037a6" 
                ),
            array(
                'algorithm' => 'sha1', 
                'password' => "password", 
                'salt' => "salt", 
                'iterations' => 2, 
                'keylength' => 20, 
                'output' => "ea6c014dc72d6f8ccd1ed92ace1d41f0d8de8957"
                ),
            array(
                'algorithm' => 'sha1', 
                'password' => "password", 
                'salt' => "salt", 
                'iterations' => 4096, 
                'keylength' => 20, 
                'output' => "4b007901b765489abead49d926f721d065a429c1"
                ),
            array(
                'algorithm' => 'sha1', 
                'password' => "passwordPASSWORDpassword", 
                'salt' => "saltSALTsaltSALTsaltSALTsaltSALTsalt", 
                'iterations' => 4096, 
                'keylength' => 25, 
                'output' => "3d2eec4fe41c849b80c8d83662c0e44a8b291a964cf2f07038"
                ), 
            array(
                'algorithm' => 'sha1', 
                'password' => "pass\0word", 
                'salt' => "sa\0lt", 
                'iterations' => 4096, 
                'keylength' => 16, 
                'output' => "56fa6aa75548099dcc37d7f03425e0c3"
                ),            
        );

        foreach($pbkdf2_vectors as $test) 
        {
            $realOut = $hash->pbkdf2(
                $test['algorithm'],
                $test['password'],
                $test['salt'],
                $test['iterations'],
                $test['keylength'],
                false
            );

            $this->assertTrue($realOut === $test['output'], "PBKDF2 vector");
            
        }

        $salt = $hash->getSalt();
        $pass = 'pass';
        $this->assertTrue($hash->checkPassword(
                    $pass,
                    $salt,
                    $hash->hashPassword($pass,$salt)
            ));
        
        $good_hash = $hash->create_hash("foobar");
        $this->assertTrue($hash->validate_password("foobar", $good_hash), "Correct password");
        $this->assertTrue($hash->validate_password("foobar2", $good_hash) === false, "Wrong password");

        $h1 = explode(":", $hash->create_hash(""));
        $h2 = explode(":", $hash->create_hash(""));
        $this->assertTrue($h1[HASH_PBKDF2_INDEX] != $h2[HASH_PBKDF2_INDEX], "Different hashes");
        $this->assertTrue($h1[HASH_SALT_INDEX] != $h2[HASH_SALT_INDEX], "Different salts");

        $this->assertTrue($hash->slow_equals("",""), "Slow equals empty string");
        $this->assertTrue($hash->slow_equals("abcdef","abcdef"), "Slow equals normal string");

        $this->assertTrue($hash->slow_equals("aaaaaaaaaa", "aaaaaaaaab") === false, "Slow equals different");
        $this->assertTrue($hash->slow_equals("aa", "a") === false, "Slow equals different length 1");
        $this->assertTrue($hash->slow_equals("a", "aa") === false, "Slow equals different length 2");
    }
    
}


    



