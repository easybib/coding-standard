<?php
/**
 * EasyBib\Sniffs\Methods\ConstructShouldNotReturnSniff
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
 * EasyBib\Sniffs\Methods\ConstructShouldNotReturnSniff
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
class ConstructShouldNotReturnSniff extends \PHP_CodeSniffer_Standards_AbstractScopeSniff
{
    public function __construct()
    {
        parent::__construct(array(T_FUNCTION), array(T_RETURN));
    }

    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     * @param int                  $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(\PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $methodName = $phpcsFile->getDeclarationName($currScope);
        if (null === $methodName) {
            return;
        }
        if ('__construct' !== $methodName) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        if (!isset($tokens[$stackPtr])) {
            return;
        }

        if ('T_RETURN' === $tokens[$stackPtr]['type'] && 'T_SEMICOLON' !== $tokens[$stackPtr+1]['type']) {
            $phpcsFile->addWarning('__construct cannot return anything', $stackPtr, 'NoReturnInConstruct');
            return;
        }
    }
}
