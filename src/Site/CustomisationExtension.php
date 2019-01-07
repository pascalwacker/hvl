<?php

namespace Bundle\Site;

use Bolt\Extension\SimpleExtension;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Site bundle extension loader.
 *
 * This is the base bundle you can use to further customise Bolt for your
 * specific site.
 *
 * It is perfectly safe to remove this bundle, just remember to remove the
 * entry from your .bolt.yml or .bolt.php file.
 *
 * For more information on building bundles see https://docs.bolt.cm/extensions
 */
class CustomisationExtension extends SimpleExtension
{
    protected function registerFrontendRoutes(ControllerCollection $collection)
    {
        $collection->post('/mail', [$this, 'callbackMail'])->bind('contactmail');
    }

    public function callbackMail(Application $app, Request $request)
    {
        if ($request->request->get('name', '') && $request->request->get('email', '') && $request->request->get('message')) {
            $name = $request->request->get('name', '');
            $email = $request->request->get('email', '');
            $message = $request->request->get('message');
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Nachricht vom Hilariverein')
                    ->setFrom('info@hilariverein-langwiesen.ch')
                    ->setTo($app['config']->get('general/contactMail'))
                    ->setBody("Von: " . $name . "(" . $email . ")\n\r\n\rNachricht: " . $message)
                    ->addPart("<strong>Von:</strong> " . $name . "(" . $email . ")<br /><br /><strong>Nachricht:</strong> " . nl2br($message), 'text/html');
                $app['mailer']->send($message);
            }
        }

        return new RedirectResponse('/');
    }
}
