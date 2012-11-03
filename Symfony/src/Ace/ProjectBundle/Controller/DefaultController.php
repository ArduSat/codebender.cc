<?php

namespace Ace\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Ace\ProjectBundle\Entity\Project as Project;
use Doctrine\ORM\EntityManager;
use Ace\ProjectBundle\Controller\MongoFilesController;

class DefaultController extends Controller
{
    protected $em;
	protected $mfc;

	public function listAction($owner)
	{
		$projects = $this->em->getRepository('AceProjectBundle:Project')->findByOwner($owner);
		$list = array();
		foreach($projects as $project)
		{
			$list[] = array("id"=> $project->getId(), "name"=>$project->getName(), "description"=>$project->getDescription(), "is_public"=>$project->getIsPublic());
		}
		return new Response(json_encode($list));
	}

	public function createAction($owner, $name, $description)
	{
		$project = new Project();
		$user = $this->em->getRepository('AceUserBundle:User')->find($owner);
		$project->setOwner($user);
	    $project->setName($name);
	    $project->setDescription($description);
	    $project->setIsPublic(TRUE);
	
		$project->setType("mongo");
		$mongo = $this->mfc;
		$response = json_decode($mongo->createAction(), true);
		if($response["success"])
		{
			$id = $response["id"];
			$project->setProjectfilesId($id);

		    $em = $this->em;
		    $em->persist($project);
		    $em->flush();

		    return new Response(json_encode(array("success" => true, "id" => $project->getId())));
		}
		else
			return new Response(json_encode(array("success" => false, "owner_id" => $user->getId(), "name" => $name)));
	}
	
	public function deleteAction($id)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$deletion = json_decode($mongo->deleteAction($project->getProjectfilesId()), true);
		if($deletion["success"] == true)
		{
		    $em = $this->em;
			$em->remove($project);
			$em->flush();
			return new Response(json_encode(array("success" => true)));
		}
		else
		{
			return new Response(json_encode(array("success" => false, "id" => $project->getProjectfilesId())));
		}
		
	}

	public function cloneAction($owner, $id)
	{
		$project = $this->getProjectById($id);
		$new_project = new Project();
		$user = $this->em->getRepository('AceUserBundle:User')->find($owner);
		$new_project->setOwner($user);
	    $new_project->setName($project->getName());
	    $new_project->setDescription($project->getDescription());
	    $new_project->setIsPublic(TRUE);

		$new_project->setType("mongo");
		$mongo = $this->mfc;
		// die(var_dump($project->getProjectfilesId()));
		$response = $mongo->cloneAction($project->getProjectfilesId());
		$response = json_decode($response, true);
		if($response["success"] == true)
		{
			$new_project->setProjectfilesId($response["id"]);

		    $em = $this->em;
		    $em->persist($new_project);
		    $em->flush();

		    return new Response(json_encode(array("success" => true, "id" => $new_project->getId())));
		}
		else
		{
			return new Response(json_encode(array("success" => false, "id" => $id)));
		}

	}

	public function renameAction($id, $new_name)
	{
		$output = array("success" => true);

		$project = $this->getProjectById($id);
		$name = $project->getName();

		$mongo = $this->mfc;
		$filename = $name.".ino";
		$code = $mongo->getFileAction($project->getProjectfilesId(), $filename);
		$response = json_decode($mongo->createFileAction($project->getProjectfilesId(), $new_name.".ino", $code), true);
		if($response["success"] == true)
		{
			$response = json_decode($mongo->deleteFileAction($project->getProjectfilesId(), $filename), true);
			if($response["success"])
			{
				$project->setName($new_name);
			    $em = $this->em;
			    $em->persist($project);
			    $em->flush();
			}
			else
				$output = array("success" => false, "id" => $id, "new name" => $new_name, "error" => "could not delete previous file");
		}
		else
			$output = array("success" => false, "id" => $id, "new name" => $new_name, "error" => "could not create new file");
		return new Response(json_encode($output));
	}

	public function getNameAction($id)
	{
		$project = $this->getProjectById($id);
		$name = $project->getName();
		return new Response(json_encode(array("success" => true, "response" => $name)));
	}

	public function getOwnerAction($id)
	{
		$project = $this->getProjectById($id);
		$user = $project->getOwner();
		$response = array("id" => $user->getId(), "username" => $user->getUsername(), "firstname" => $user->getFirstname(), "lastname" => $user->getLastname());
		return new Response(json_encode(array("success" => true, "response" => $response)));
	}

	public function getDescriptionAction($id)
	{
		$project = $this->getProjectById($id);
		$response = $project->getDescription();
		return new Response(json_encode(array("success" => true, "response" => $response)));
	}

	public function setDescriptionAction($id, $description)
	{
		$project = $this->getProjectById($id);
		$project->setDescription($description);
	    $em = $this->em;
	    $em->persist($project);
	    $em->flush();
		return new Response(json_encode(array("success" => true)));
	}
	
	public function listFilesAction($id)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$list = $mongo->listFilesAction($project->getProjectfilesId());
		return new Response($list);
	}

	public function createFileAction($id, $filename, $code)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$create = $mongo->createFileAction($project->getProjectfilesId(), $filename, $code);
		return new Response($create);
	}
	
	public function getFileAction($id, $filename)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$get = $mongo->getFileAction($project->getProjectfilesId(), $filename);
		return new Response($get);
		
	}
	
	public function setFileAction($id, $filename, $code)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$set = $mongo->setFileAction($project->getProjectfilesId(), $filename, $code);
		return new Response($set);
		
	}
		
	public function deleteFileAction($id, $filename)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$delete = $mongo->deleteFileAction($project->getProjectfilesId(), $filename);
		return new Response($delete);
	}

	public function renameFileAction($id, $filename, $new_filename)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$delete = $mongo->renameFileAction($project->getProjectfilesId(), $filename, $new_filename);
		return new Response($delete);
	}

	public function searchAction($token)
	{
		$results_name = json_decode($this->searchNameAction($token)->getContent(), true);
		$results_desc = json_decode($this->searchDescriptionAction($token)->getContent(), true);
		$results = $results_name + $results_desc;
		return new Response(json_encode($results));
	}

	public function searchNameAction($token)
	{
		$em = $this->em;
		$repository = $this->em->getRepository('AceProjectBundle:Project');
		$qb = $em->createQueryBuilder();
		$projects = $repository->createQueryBuilder('p')->where('p.name LIKE :token')->setParameter('token', "%".$token."%")->getQuery()->getResult();
		$result = array();
		foreach($projects as $project)
		{
			$owner = json_decode($this->getOwnerAction($project->getId())->getContent(), true);
			$owner = $owner["response"];
			$proj = array("name" => $project->getName(), "description" => $project->getDescription(), "owner" => $owner);
			$result[] = array($project->getId() => $proj);
		}
		return new Response(json_encode($result));
	}

	public function searchDescriptionAction($token)
	{
		$em = $this->em;
		$repository = $this->em->getRepository('AceProjectBundle:Project');
		$qb = $em->createQueryBuilder();
		$projects = $repository->createQueryBuilder('p')->where('p.description LIKE :token')->setParameter('token', "%".$token."%")->getQuery()->getResult();
		$result = array();
		foreach($projects as $project)
		{
			$owner = json_decode($this->getOwnerAction($project->getId())->getContent(), true);
			$owner = $owner["response"];
			$proj = array("name" => $project->getName(), "description" => $project->getDescription(), "owner" => $owner);
			$result[] = array($project->getId() => $proj);
		}
		return new Response(json_encode($result));
	}

	public function checkExistsAction($id)
	{
		$em = $this->em;
		$project = $this->em->getRepository('AceProjectBundle:Project')->find($id);
	    if (!$project)
			return new Response(json_encode(array("success" => false)));
		return new Response(json_encode(array("success" => true)));
	}

	public function getProjectById($id)
	{
		$em = $this->em;
		$project = $this->em->getRepository('AceProjectBundle:Project')->find($id);
	    if (!$project)
	        throw $this->createNotFoundException('No project found with id '.$id);			
			// return new Response(json_encode(array(false, "Could not find project with id: ".$id)));
		
		return $project;
	}

	public function __construct(EntityManager $entityManager, MongoFilesController $mongoFilesController)
	{
	    $this->em = $entityManager;
		$this->mfc = $mongoFilesController;
	}
}
