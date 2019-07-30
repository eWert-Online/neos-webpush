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


class BackendController extends ActionController
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
    )
    {
        $data = json_encode([
            "url" => $url
        ]);

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
        $message->setActions($actions);

        $this->messageRepository->add($message);
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
     * Sends the specified message
     *
     * @param Message $message
     * @return void
     */
    public function sendAction(Message $message): void
    {
        $allSubscriptions = $this->subscriptionRepository->findAll();

        foreach($allSubscriptions as $subscription) {
            $auth = array(
                'VAPID' => array(
                    'subject' => 'https://github.com/Minishlink/web-push-php-example/',
                    'publicKey' => 'BMBlr6YznhYMX3NgcWIDRxZXs0sh7tCv7_YCsWcww0ZCv9WGg-tRCXfMEHTiBPCksSqeve1twlbmVAZFv7GSuj0',
                    'privateKey' => 'vplfkITvu0cwHqzK9Kj-DYStbCH_9AhGx9LqMyaeI6w',
                ),
            );

            $webPush = new WebPush($auth);

            $res = $webPush->sendNotification(
                Subscription::create([
                    'endpoint' => $subscription->getEndpoint(),
                    'publicKey' => $subscription->getP256dh(),
                    'authToken' => $subscription->getAuth(),
                ]),
                "{
                    \"title\": \"". $message->getTitle() ."\"
                }",
                true // flush
            );

            // handle eventual errors here, and remove the subscription from your server if it is expired
            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    echo "[v] Message sent successfully for subscription {$endpoint}.";
                } else {
                    echo "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}";
                }
            }
        }

        $this->addFlashMessage('The Message has been sent successfully to '. count($allSubscriptions) .' Subscriber(s).');
        $this->redirect('index');
    }
}
