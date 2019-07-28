<?php
namespace Ewert\WebPush\Controller;

/*
 * This file is part of the Ewert.WebPush package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Ewert\WebPush\Domain\Model\Message;
use Ewert\WebPush\Domain\Repository\MessageRepository;


class BackendController extends ActionController
{
    /**
     * @Flow\Inject
     * @var MessageRepository
     */
    protected $messageRepository;

    /**
     * Shows a list of all messages
     * @return void
     */
    public function indexAction()
    {
        $messages = $this->messageRepository->findAll();
        $this->view->assignMultiple([
            'messages' => $messages
        ]);
    }

    /**
     * Shows details for the specified message
     *
     * @param Message $message
     * @return void
     */
    public function showAction(Message $message): void
    {
        $this->view->assignMultiple([
            'message' => $message
        ]);
    }

    /**
     * @return void
     */
    public function newAction()
    {
    }


    /**
     * Creates a message based on the given information
     *
     * @Flow\Validate(argumentName="title", type="\Neos\Flow\Validation\Validator\NotEmptyValidator")
     * @param string $title
     * @param string $direction
     * @param string $lang
     * @param string $body
     * @param string $tag
     * @param string $image
     * @param string $icon
     * @param string $badge
     * @param array $vibrate
     * @param boolean $renotify
     * @param boolean $silent
     * @param boolean $requireInteraction
     * @param string $data
     * @param array $actions
     * @return void
     */
    public function createAction(
        $title,
        $direction = 'auto',
        $lang = null,
        $body = null,
        $tag = null,
        $image = null,
        $icon = null,
        $badge = null,
        array $vibrate = [0],
        $renotify = false,
        $silent = false,
        $requireInteraction = false,
        $data = null,
        array $actions = []
    )
    {
        $message = new Message($title, $body);
        $message->setTitle($title);
        $message->setDirection($direction);
        $message->setLang($lang);
        $message->setTag($tag);
        $message->setImage($image);
        $message->setIcon($icon);
        $message->setBadge($badge);
        $message->setBadge($badge);
        $message->setVibrate($vibrate);
        $message->setVibrate($vibrate);
        $message->setRenotify($renotify);
        $message->setSilent($silent);
        $message->setSilent($silent);
        $message->setRequireInteraction($requireInteraction);
        $message->setData($data);
        $message->setData($data);
        $message->setActions($actions);

        $this->messageRepository->add($message);
        $this->redirect('index');
    }
}
