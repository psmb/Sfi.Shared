<?php
namespace Sfi\Shared\TypoScript;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Neos\Domain\Exception;
use TYPO3\Neos\Service\LinkingService;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;

/**
 * A TypoScript Object that automatically creates links to tags pages
 */
class ReplaceTagsImplementation extends AbstractTypoScriptObject {

	/**
	 * @Flow\Inject
	 * @var LinkingService
	 */
	protected $linkingService;

	/**
	 * Automatically create links to pages provided in `tags`.
	 * Tag page can have CSV property `replaceVariants` for alternative spelling/cases of tag name.
	 *
	 * @return string
	 * @throws Exception
	 */
	public function evaluate() {
		$text = $this->tsValue('value');
		$tags = $this->tsValue('tags');
		$node = $this->tsValue('node');
		$documentNode = $this->tsValue('documentNode');
		if ($text === '' || $text === NULL) {
			return '';
		}
		if (!$node instanceof NodeInterface) {
			throw new Exception(sprintf('The current node must be an instance of NodeInterface, given: "%s".', gettype($text)), 1382624087);
		}
		if ($node->getContext()->getWorkspace()->getName() !== 'live') {
			return $text;
		}
		$linkingService = $this->linkingService;
		$controllerContext = $this->tsRuntime->getControllerContext();

		foreach ($tags as $tag) {
			if ($tag->getProperty('replaceVariants') && ($tag != $documentNode)) {
				$replacementVariants = explode(',', $tag->getProperty('replaceVariants'));
				foreach ($replacementVariants as $replacementVariant) {
					$replacementVariant = trim($replacementVariant);
					if (strpos($text, $replacementVariant) !== false) {
						$tagUri = $linkingService->createNodeUri($controllerContext, $tag);
						$text = preg_replace('/(?!(?:[^<]+>|[^>]+<\/a>))\b(' . $replacementVariant . ')\b/ui', '<a href="' . $tagUri . '">$1</a>', $text);
					}
				}
			}
		}
		return $text;
	}
}
