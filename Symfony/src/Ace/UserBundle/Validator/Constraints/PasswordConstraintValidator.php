<?php

namespace Ace\UserBundle\Validator\Constraints;
 
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
 
class PasswordConstraintValidator extends ConstraintValidator
{

    private function isValidPasswordConstraint($pass){
    	
 		$regnum = 0;
		$reglet = 0;
		$regcaps = 0;
		$regpunc = 0;
		
		if(preg_match('/.*\d/', $pass ))
			$regnum = 1; //number
		if(preg_match('/.*[a-z]/', $pass ))
			$reglet = 1; //letters
		if(preg_match('/.*[A-Z]/', $pass ))
			$regcaps = 1; //caps
		if(preg_match('/.*[@#$%!^&*()\_\-\+=~<>,.?\/:;\'"}{|`[\]]/', $pass ))
			$regpunc = 1; //symbols
		
		
		if ($regnum + $reglet + $regcaps + $regpunc > 2)
			return true; 
		else
			return false;
	}
       
    
 
    public function isValid($pass, Constraint $constraint)
    {
        if( $this->isValidPasswordConstraint($pass) === false ) {
            $this->setMessage($constraint->message, array('{{ pass }}' => $pass));
 
            return false;
        }
        
        return true;
    }
}
