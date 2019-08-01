<?php

namespace Ewert\WebPush\Controller;

/*
 * This file is part of the Ewert.WebPush package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;

use Ewert\WebPush\Domain\Model\Message;
use Ewert\WebPush\Domain\Repository\MessageRepository;
use Ewert\WebPush\Domain\Repository\SubscriptionRepository;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\VAPID;


class MessageController extends ActionController
{
    /**
     * @Flow\Inject
     * @var MessageRepository
     */
    protected $messageRepository;

    /**
     * @Flow\Inject
     * @var SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * @Flow\InjectConfiguration(path="vapid.publicKey")
     * @var string
     */
    protected $vapidPublicKey;

    /**
     * @Flow\InjectConfiguration(path="vapid.privateKey")
     * @var string
     */
    protected $vapidPrivateKey;

    /**
     * Shows a list of all messages
     * @return void
     */
    public function indexAction()
    {
        if ($this->vapidPrivateKey == "" || $this->vapidPublicKey == "") {
            $vapid = VAPID::createVapidKeys();
            $this->view->assign('generateVapid', true);
            $this->view->assign('publicKey', $vapid["publicKey"]);
            $this->view->assign('privateKey', $vapid["privateKey"]);
        } else {
            $messages = $this->messageRepository->findAll();
            $this->view->assign('messages', $messages);
        }
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
     * Edit details for the specified message
     *
     * @param Message $message
     * @return void
     */
    public function editAction(Message $message): void
    {
        $this->view->assignMultiple([
            'message' => $message
        ]);
    }

    /**
     * @return void
     */
    public function newAction()
    { }


    /**
     * Creates a message based on the given information
     *
     * @Flow\Validate(argumentName="title", type="\Neos\Flow\Validation\Validator\NotEmptyValidator")
     * @param string $title
     * @param string $direction
     * @param string $lang
     * @param string $body
     * @param string $url
     * @param string $tag
     * @param string $image
     * @param string $icon
     * @param string $badge
     * @param array $vibrate
     * @param boolean $renotify
     * @param boolean $silent
     * @param boolean $requireInteraction
     * @param array $actions
     * @return void
     */
    public function createAction(
        $title,
        $direction = 'auto',
        $lang = null,
        $body = null,
        $url = null,
        $tag = null,
        $image = null,
        $icon = null,
        $badge = null,
        array $vibrate = [0],
        $renotify = false,
        $silent = false,
        $requireInteraction = false,
        array $actions = []
    ) {
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
        $message->setUrl($url);
        $message->setActions($actions);

        $this->messageRepository->add($message);
        $this->addFlashMessage("The Message has been successfully added!");
        $this->redirect('index');
    }

    /**
     * Deletes the specified message
     *
     * @param Message $message
     * @return void
     */
    public function deleteAction(Message $message): void
    {
        $this->messageRepository->remove($message);
        $this->addFlashMessage("The Message has been successfully deleted!");
        $this->redirect('index');
    }

    /**
     * Updates a message based on the given information
     *
     * @param Message $message
     * @return void
     */
    public function updateAction(Message $message): void
    {
        $this->messageRepository->update($message);
        $this->addFlashMessage("The Message has been successfully edited!");
        $this->redirect('index');
    }

    /**
     * Sends the specified message
     *
     * @param Message $message
     * @return void
     */
    public function sendAction(Message $message): void
    {
        $allSubscriptions = $this->subscriptionRepository->findAll();
        $sentCount = 0;
        $failCount = 0;

        foreach ($allSubscriptions as $subscription) {
            $auth = array(
                'VAPID' => array(
                    'subject' => '',
                    'publicKey' => $this->vapidPublicKey,
                    'privateKey' => $this->vapidPrivateKey,
                ),
            );

            $webPush = new WebPush($auth);

            $webPush->sendNotification(
                Subscription::create([
                    'endpoint' => $subscription->getEndpoint(),
                    'publicKey' => $subscription->getP256dh(),
                    'authToken' => $subscription->getAuth(),
                ]),
                json_encode(array(
                    "title" => $message->getTitle(),
                    "body" => $message->getBody(),
                    "icon" => $message->getIcon(),
                    "image" => $message->getImage(),
                    "badge" => $message->getBadge(),
                    "vibrate" => $message->getVibrate(),
                    "dir" => $message->getDirection(),
                    "lang" => $message->getLang(),

                    "tag" => $message->getTag(),
                    "requireInteraction" => $message->getRequireInteraction(),
                    "renotify" => $message->getRenotify(),
                    "silent" => $message->getSilent(),

                    "actions" => $message->getActions(),
                    "url" => $message->getUrl(),
                ))
            );

            foreach ($webPush->flush() as $report) {
                // $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    $sentCount++;
                } else {
                    $failCount++;
                }
            }
        }

        if ($sentCount > 0) {
            $this->addFlashMessage('The Message has been sent successfully to ' . $sentCount . ' Subscriber(s).');
            $message->setTimestamp(time());
            $this->messageRepository->update($message);
        } elseif ($failCount > 0) {
            $this->addFlashMessage($failCount . ' Subscriber(s) have failed to recieve the Message.', '', \Neos\Error\Messages\Message::SEVERITY_WARNING);
        } else {
            $this->addFlashMessage('There are no Subscribers yet...', '', \Neos\Error\Messages\Message::SEVERITY_WARNING);
        }

        $this->redirect('index');
    }
}
