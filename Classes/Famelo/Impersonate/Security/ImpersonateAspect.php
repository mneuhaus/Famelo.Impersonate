<?php
namespace Famelo\Impersonate\Security;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * An aspect which centralizes the logging of security relevant actions.
 *
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class ImpersonateAspect {
	/**
	 * @var boolean
	 */
	protected $alreadyLoggedAuthenticateCall = FALSE;

	/**
	 * @var \Famelo\Impersonate\ImpersonateService
	 * @Flow\Inject
	 */
	protected $impersonateService;

	/**
	 * @Flow\After("within(TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface) && method(.*->authenticate())")
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint The current joinpoint
	 * @return mixed The result of the target method if it has not been intercepted
	 * @throws \Exception
	 */
	public function logManagerAuthenticate(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		if ($this->alreadyLoggedAuthenticateCall === FALSE) {
			if ($joinPoint->getProxy()->getSecurityContext()->getAccount() !== NULL) {
				$impersonation = $this->impersonateService->getImpersonation();
				if ($impersonation instanceof \TYPO3\Flow\Security\Account) {
					foreach ($joinPoint->getProxy()->getSecurityContext()->getAuthenticationTokens() as $token) {
						$token->setAccount($impersonation);
					}
				}
			}
			$this->alreadyLoggedAuthenticateCall = TRUE;
		}
	}
}

?>