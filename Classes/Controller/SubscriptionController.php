<?php
namespace Ewert\WebPush\Controller;

/*
 * This file is part of the Ewert.WebPush package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Ewert\WebPush\Domain\Model\Subscription;
use Ewert\WebPush\Domain\Repository\SubscriptionRepository;


class SubscriptionController extends ActionController
{
    /**
     * A list of IANA media types which are supported by this controller
     *
     * @var array
     */
    protected $supportedMediaTypes = array('application/json');

    /**
     * @var string
     */
    protected $defaultViewObjectName = \Neos\Flow\Mvc\View\JsonView::class;

    /**
     * @Flow\Inject
     * @var SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * Creates a new Subscription based on the given information
     *
     * @Flow\Validate(argumentName="endpoint", type="\Neos\Flow\Validation\Validator\NotEmptyValidator")
     * @Flow\Validate(argumentName="p256dh", type="\Neos\Flow\Validation\Validator\NotEmptyValidator")
     * @Flow\Validate(argumentName="auth", type="\Neos\Flow\Validation\Validator\NotEmptyValidator")
     * @param string $endpoint
     * @param string $p256dh
     * @param string $auth
     * @return void
     */
    public function createAction(
        $endpoint,
        $p256dh,
        $auth
    )
    {
        $existingSubscription = $this->subscriptionRepository->findByEndpoint($endpoint);

        if(count($existingSubscription) > 0) {
            $this->view->assignMultiple(array(
                'created' => false,
                'timestamp' => time(),
                'message' => 'Subscription already exists'
            ));
        } else {
            $message = new Subscription();
            $message->setEndpoint($endpoint);
            $message->setP256dh($p256dh);
            $message->setAuth($auth);

            $this->subscriptionRepository->add($message);
            $this->view->assign('status', 'ok');
            // set vars for use in json view
            $this->view->assignMultiple(array(
                'created' => true,
                'timestamp' => time(),
                'message' => 'ok'
            ));
        }

        // set variables to render. By default only the variable 'value' will be rendered
        $this->view->setVariablesToRender(array('created', 'timestamp', 'message'));
    }
}
