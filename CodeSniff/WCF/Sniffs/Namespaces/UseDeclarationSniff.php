<?php
/**
 * This sniff is based on Squiz_Sniffs_Classes_ClassFileNameSniff. Originally written
 * by Greg Sherwood <gsherwood@squiz.net> and released under the terms of the BSD Licence.
 * See: https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/PSR2/Sniffs/Namespaces/UseDeclarationSniff.php
 * 
 * @author	Tim Duesterhus
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @category	Community Framework
 */
class WCF_Sniffs_Namespaces_UseDeclarationSniff implements PHP_CodeSniffer_Sniff {
	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(T_USE);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int				  $stackPtr  The position of the current token in
	 *										the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		
		// Ignore USE keywords inside closures.
		$next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
		if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
			return;
		}
		
		// Only one USE declaration allowed per statement.
		$next = $phpcsFile->findNext(array(T_COMMA, T_SEMICOLON), ($stackPtr + 1));
		if ($tokens[$next]['code'] === T_COMMA) {
			$error = 'There must be one USE keyword per declaration';
			$phpcsFile->addError($error, $stackPtr, 'MultipleDeclarations');
		}
		
		// Leading NS_SEPARATOR must be omitted
		$next = $phpcsFile->findNext(array(T_NS_SEPARATOR, T_STRING), ($stackPtr + 1));
		if ($tokens[$next]['code'] === T_NS_SEPARATOR) {
			$error = 'Leading backslash must be omitted.';
			$phpcsFile->addError($error, $stackPtr, 'LeadingNSSeparator');
		}
		
		// Make sure this USE comes after the first namespace declaration.
		$prev = $phpcsFile->findPrevious(T_NAMESPACE, ($stackPtr - 1));
		if ($prev !== false) {
			$first = $phpcsFile->findNext(T_NAMESPACE, 1);
			if ($prev !== $first) {
				$error = 'USE declarations must go after the first namespace declaration';
				$phpcsFile->addError($error, $stackPtr, 'UseAfterNamespace');
			}
		}
		
		$end = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
		$next = $phpcsFile->findNext(T_WHITESPACE, ($end + 1), null, true);
		if ($tokens[$next]['code'] === T_USE) {
			$diff = $tokens[$next]['line'] - $tokens[$stackPtr]['line'] - 1;
			if ($diff !== 0) {
				$error = 'There must not be any blank lines between use statements; %s found;';
				$data = array($diff);
				$phpcsFile->addError($error, $stackPtr, 'SpaceBetweenUse', $data);
			}
		}
		
		// Only interested in the last USE statement from here onwards.
		$nextUse = $phpcsFile->findNext(T_USE, ($stackPtr + 1));
		if ($nextUse !== false) {
			$next = $phpcsFile->findNext(T_WHITESPACE, ($nextUse + 1), null, true);
			if ($tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
				return;
			}
		}
		
		$end  = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
		$next = $phpcsFile->findNext(T_WHITESPACE, ($end + 1), null, true);
		$diff = ($tokens[$next]['line'] - $tokens[$end]['line'] - 1);
		if ($diff !== 1) {
			if ($diff < 0) {
				$diff = 0;
			}
			
			$error = 'There must be one blank line after the last USE statement; %s found;';
			$data = array($diff);
			$phpcsFile->addError($error, $stackPtr, 'SpaceAfterLastUse', $data);
		}
	
	}
}
