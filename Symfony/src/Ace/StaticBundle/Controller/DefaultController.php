<?php

namespace Ace\StaticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ace\StaticBundle\Entity\BlogPost;
use Ace\StaticBundle\Entity\Contact;
use Ace\StaticBundle\Entity\Prereg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class developer
{
	public $name;
	public $image;
	public $description;
	function __construct($name, $subtext, $image, $description)
	{
		$this->name = $name;
		$this->subtext = $subtext;
		$this->image = $image;
		$this->description = $description;
	}
}

class DefaultController extends Controller
{

	public function aboutAction()
	{
		return $this->render('AceStaticBundle:Default:about.html.twig');
	}

	public function teamAction()
	{
		$dev_images_dir = "images/developers/";
		$tzikis_name = "Vasilis Georgitzikis";
		$tzikis_title = "teh lead";
		$tzikis_avatar = $dev_images_dir."tzikis.jpeg";
		$tzikis_desc = "I am a student at the Computer Engineering and Informatics Department of the University of Patras, Greece, a researcher at the Research Academic Computer Technology Institute, and an Arduino and iPhone/OSX/Cocoa developer. Basically, just a geek who likes building stuff, which is what started codebender in the first place.";
		$tzikis = new developer($tzikis_name, $tzikis_title, $tzikis_avatar, $tzikis_desc);

		$tsampas_name = "Stelios Tsampas";
		$tsampas_title = "teh crazor";
		$tsampas_avatar = $dev_images_dir."tsampas.png";
		$tsampas_desc="Yet another student at CEID. My task is to make sure to bring crazy ideas to the table and let others assess their value. I'm also responsible for the Arduino Ethernet TFTP bootloader, the only crazy idea that didn't originate from me. I also have a 'wierd' coding style that causes much distress to $tzikis_name.";
		$tsampas = new developer($tsampas_name, $tsampas_title, $tsampas_avatar, $tsampas_desc);

		$amaxilatis_name = "Dimitris Amaxilatis";
		$amaxilatis_title = "teh code monkey";
		$amaxilatis_avatar = $dev_images_dir."amaxilatis.jpg";
		$amaxilatis_desc = "Master Student at the Computer Engineering and Informatics Department of the University of Patras, Greece. Researcher at  the Research Unit 1 of Computer Technology Institute & Press (Diophantus) in the fields of Distributed Systems and Wireless Sensor Networks.";
		$amaxilatis = new developer($amaxilatis_name, $amaxilatis_title, $amaxilatis_avatar, $amaxilatis_desc);

		$kousta_name = "Maria Kousta";
		$kousta_title = "teh lady";
		$kousta_avatar = $dev_images_dir."kousta.png";
		$kousta_desc = "A CEID graduate. My task is to develop the various parts of the site besides the core 'code and compile' page that make it a truly social-building website.";
		$kousta = new developer($kousta_name, $kousta_title, $kousta_avatar, $kousta_desc);

		$orfanos_name = "Markellos Orfanos";
		$orfanos_title = "teh fireman";
		$orfanos_avatar = $dev_images_dir."orfanos.jpg";
		$orfanos_desc = "I am also (not for long I hope) a student at the Computer Engineering & Informatics Department and probably the most important person in the team. My task? Make sure everyone keeps calm and the team is having fun. And yes, I'm the one who developed our wonderful options page. Apart from that, I'm trying to graduate and some time in the future to become a full blown Gentoo developer.";
		$orfanos = new developer($orfanos_name, $orfanos_title, $orfanos_avatar, $orfanos_desc);

		$dimakopoulos_name = "Dimitris Dimakopoulos";
		$dimakopoulos_title = "teh awesome";
		$dimakopoulos_avatar = $dev_images_dir."dimakopoulos.jpg";
		$dimakopoulos_desc = "Student at the Computer Engineering and Informatics Department of the University of Patras, Greece, have worked as an intern for Philips Consumer Lifestyle in Eindhoven and for the Research Academic Computer Technology Institute in Patras. Totally excited with Codebender as it combines web development and distributed systems, them being among my favorite fields.";
		$dimakopoulos = new developer($dimakopoulos_name, $dimakopoulos_title, $dimakopoulos_avatar, $dimakopoulos_desc);

		$christidis_name = "Dimitrios Christidis";
		$christidis_title = "teh bald guy";
		$christidis_avatar = $dev_images_dir."christidis.jpg";
		$christidis_desc = "Currently a student and an assistant administrator. I am responsible for the compiler backend, ensuring that it's fast and robust.  Known as a perfectionist, I often fuss over coding style and documentation.";
		$christidis = new developer($christidis_name, $christidis_title, $christidis_avatar, $christidis_desc);

		$developers = array($tzikis, $tsampas, $amaxilatis, $orfanos, $dimakopoulos);
		$friends = array($christidis);
		$past = array($kousta);
		return $this->render('AceStaticBundle:Default:team.html.twig', array("developers" => $developers, "friends" => $friends, "past" => $past));
	}

	public function tutorialsAction()
	{
		return $this->render('AceStaticBundle:Default:tutorials.html.twig');
	}

	public function contactAction(Request $request)
	{	    
        // create a task and give it some dummy data for this example
        $task = new Contact();
		if ($this->get('security.context')->isGranted('ROLE_USER') === true)
		{
			$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);
	        $task->setName($user["firstname"]." ".$user["lastname"]." (".$user["username"].")");
	        $task->setEmail($user["email"]);
		}

        $form = $this->createFormBuilder($task)
            ->add('name', 'text')
            ->add('email', 'email')
            ->add('text', 'textarea')
            ->getForm();

		if ($request->getMethod() == 'POST') 
		{
			$form->bindRequest($request);

			if ($form->isValid())
			{
				$email_addr = $this->container->getParameter('email.addr');
				
				// perform some action, such as saving the task to the database
			    $message = \Swift_Message::newInstance()
			        ->setSubject('codebender contact request')
			        ->setFrom($email_addr)
			        ->setTo($email_addr)
			        ->setBody($this->renderView('AceStaticBundle:Default:contact_email_form.txt.twig', array('task' => $task)))
			    ;
			    $this->get('mailer')->send($message);
				$this->get('session')->setFlash('notice', 'Your message was sent!');

				return $this->redirect($this->generateUrl('AceStaticBundle_contact'));
			}
		}

        return $this->render('AceStaticBundle:Default:contact.html.twig', array(
            'form' => $form->createView(),
        ));
	}
	
	public function notifyAction()
	{
		$msg = $this->getRequest()->query->get('message');
		if($msg)
		{
			$email_addr = $this->container->getParameter('email.addr');
			$message = \Swift_Message::newInstance()
		        ->setSubject('[codebender][notification] Java Notification')
		        ->setFrom($email_addr)
		        ->setTo("amaxilatis@codebender.cc")
		        ->setBody($msg);
		    $this->get('mailer')->send($message);
		}
		return new Response("OK");
	}

	public function pluginAction()
	{
                return $this->render('AceStaticBundle:Default:plugin.html.twig', array());
	}

}
