<?php
namespace Famelo\Impersonate;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 */
class ImpersonateService {
	/**
	 * The securityContext
	 *
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 * @var \TYPO3\Flow\Session\SessionInterface
	 * @Flow\Inject
	 */
	protected $session;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @param \TYPO3\Flow\Security\Account $account
	 * @return void
	 */
	public function impersonate($account) {
		$this->session->putData('OriginalIdentity', $this->persistenceManager->getIdentifierByObject($this->securityContext->getAccount()));

		$tokens = $this->securityContext->getAuthenticationTokens();
		foreach ($tokens as $token) {
			$token->setAccount($account);
		}

		$this->session->putData('Impersonate', $this->persistenceManager->getIdentifierByObject($account));
	}

	public function undoImpersonate() {
		$this->session->putData('Impersonate', $this->session->getData('OriginalIdentity'));
	}

	public function getImpersonation() {
		if ($this->session->hasKey('Impersonate')) {
			return $this->persistenceManager->getObjectByIdentifier($this->session->getData('Impersonate'), '\TYPO3\Flow\Security\Account');
		}
		return NULL;
	}
}
?>