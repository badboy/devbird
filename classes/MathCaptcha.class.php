<?php
	/* Diese Datei wurde von michfrm.net/source heruntergeladen, das Copyright der Datei liegt bei michfrm.net. Verwendung und Veränderung ist uneingeschränkt gestattet, ein Backlink auf michfrm.net wird gern gesehen ;) */
/*
	Class to make Captcha
	Autor: Michael Mayr (michael@michfrm.net)
	License: Public Domain
	Language: German
*/


class MathCaptcha
{

	
	var $operators = array('+', '-');

	/**
	 * @desc Generates the captcha
	 * @access public
	 * @return string
	 */

	public function Generate($lang='en')
	{
		$number1 = mt_rand(1,10);
		$number2 = mt_rand(1,10);

		if ($number1 == $number2 ) $number1++;

		if ($number1 < $number2 )
		{
			$n1t = $number1;
			$number1 = $number2;
			$number2 = $n1t;
		}

		
		$operator = $this->operators[rand(0,count($this->operators) -1)];

		
		eval("\$result = $number1 $operator $number2;");

		
		#$_SESSION['MathCaptchaResult'] = $result;

		return array($this->MakeQuestion($number1, $operator, $number2, $lang), $result);
	}

	/**
	 * @desc Generates the captcha question
	 * @access private
	 * @param	int			$number1	First number
	 * @param	string	$operator	Operator
	 * @param	int			$number2	Second number
	 * @return string
	 */
	private function MakeQuestion($number1, $operator, $number2, $lang)
	{
		
		if($lang == 'de')
		{
			$replacements = array(
				'+' => 'plus',
				'-' => 'minus',
				'15' => 'fünfzehn',
				'14' => 'vierzehn',
				'13' => 'dreizehn',
				'12' => 'zwölf',
				'11' => 'elf',
				'10' => 'zehn',
				'9' => 'neun',
				'8' => 'acht',
				'7' => 'sieben',
				'6' => 'sechs',
				'5' => 'fünf',
				'4' => 'vier',
				'3' => 'drei',
				'2' => 'zwei',
				'1' => 'eins',
			);
		}
		else
		{
			$replacements = array(
				'+' => 'plus',
				'-' => 'minus',
				'15' => 'fifteen',
				'14' => 'fourteen',
				'13' => 'thirteen',
				'12' => 'twelve',
				'11' => 'eleven',
				'10' => 'ten',
				'9' => 'nine',
				'8' => 'eight',
				'7' => 'seven',
				'6' => 'six',
				'5' => 'five',
				'4' => 'four',
				'3' => 'three',
				'2' => 'two',
				'1' => 'one',
			);
		}

		if($lang == 'de')
			$question = "Was ist die Lösung von {$number1} {$operator} {$number2}?";
		else
			$question = "What is the solution of {$number1} {$operator} {$number2}?";
		
		foreach ($replacements as $find => $replace)
		{
			$question = str_replace($find, $replace, $question);
		}

		return $question;
	}

	/**
	 * @desc Check given solution
	 * @access public
	 * @param	int	$solution	Solution
	 * @return bool
	 */	 	 	 	 	
	public function Check($solution)
	{
		#echo '<br /><br />ses_mcr: '.$_SESSION['MathCaptchaResult'];
		$check = ( $_SESSION['MathCaptchaResult'] == $solution );

		unset($_SESSION['MathCaptchaResult']);

		return $check;
	}
}
?>
