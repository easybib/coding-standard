<?php
/**
 * EasyBib\Sniffs\Methods\AddSetShouldBeFluentSniff
 *
 * PHP version 5.3
 *
 * @category QA
 * @package  EasyBib\Sniffs
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://opensource.org/licenses/bsd-license.php New BSD License
 * @version  GIT: <git_id>
 * @link     http://
 */

namespace EasyBib\Sniffs\Methods;

/**
 * EasyBib\Sniffs\Methods\AddSetShouldBeFluentSniff
 *
 * Ensures that there are no `return` statements in a `__construct` and that the docblock
 * says `return $this` - if anything.
 *
 * @category  QA
 * @package   EasyBib\Sniffs
 * @author    Till Klampaeckel <till@php.net>
 * @license   http://opensource.org/licenses/bsd-license.php New BSD License
 * @version   Release: @package_version@
 * @link      http://
 */
class AddSetShouldBeFluentSniff implements \PHP_CodeSniffer_Sniff
{
    public function register()
    {
        return array(T_FUNCTION);
    }

    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void
     */
    public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if (null === $methodName) {
            return;
        }

        // check fluent interface for `set*()` and `add*()`.
        $methodType = substr($methodName, 0, 3);
        if (false === in_array($methodType, array('set', 'add'))) {
            return;
        }

        $error = sprintf('`%s` should implement a fluent interface', $methodName);

        $t_return = $phpcsFile->findNext(T_RETURN, $stackPtr);
        if (false === $t_return) {
            $phpcsFile->addError(
                $error,
                $stackPtr,
                'AddSetNoFluentInterface'
            );
            return;
        }

        $tokens = $phpcsFile->getTokens();
        if ($tokens[($t_return+1)]['type'] !== 'T_WHITESPACE') {
            $phpcsFile->addError(
                $error,
                ($t_return+1),
                'AddSetNoFluentInterface'
            );
            return;
        }
        if ($tokens[($t_return+2)]['content'] !== '$this') {
            $phpcsFile->addError(
                $error,
                ($t_return+2),
                'AddSetNoFluentInterface'
            );
            return;
        }
    }
}
