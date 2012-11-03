<?php
// src/Ace/ProjectBundle/Controller/MongoFilesController.php

namespace Ace\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ace\ProjectBundle\Document\ProjectFiles;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ODM\MongoDB\DocumentManager;

class MongoFilesController extends Controller
{
    protected $dm;

	public function createAction()
	{
	    $pf = new ProjectFiles();
	    $pf->setFiles(array());
		$pf->setImages(array());
		$pf->setSketches(array());
		
	    $dm = $this->dm;
	    $dm->persist($pf);
	    $dm->flush();

	    return json_encode(array("success" => true, "id" => $pf->getId()));
	}
	
	public function deleteAction($id)
	{
		$pf = $this->getProjectById($id);
	    $dm = $this->dm;
		$dm->remove($pf);
		$dm->flush();
		return json_encode(array("success" => true));
	}

	public function cloneAction($id)
	{
		$pf = $this->getProjectById($id);
		$new_id = json_decode($this->createAction(), true);
		$new_id = $new_id["id"];
		$new_pf = $this->getProjectById($new_id);
		$new_pf->setFiles($pf->getFiles());
		$new_pf->setFilesTimestamp($pf->getFilesTimestamp());
		$new_pf->setImages($pf->getImages());
		$new_pf->setSketches($pf->getSketches());
		$dm = $this->dm;
		$dm->persist($new_pf);
		$dm->flush();
		return json_encode(array("success" => true, "id" => $new_id));
	}
	
	public function listFilesAction($id)
	{
		$list = $this->listFiles($id);
		return json_encode(array("success" => true, "list" => $list));
	}

	public function createFileAction($id, $filename, $code)
	{
		$list = $this->listFiles($id);

		foreach($list as $file)
		{
			if($file["filename"] == $filename)
				return json_encode(array("success" => false, "id" => $id, "filename" => $filename));
		}
		$list[] = array("filename"=> $filename, "code" => $code);
		$this->setFilesById($id, $list);
		return json_encode(array("success" => true));
	}
	
	public function getFileAction($id, $filename)
	{
		$response = array("success" => false);
		$list = $this->listFiles($id);
		foreach($list as $file)
		{
			if($file["filename"] == $filename)
				$response=array("success" => true, "code" => $file["code"]);
		}
		return json_encode($response);
	}
	
	public function setFileAction($id, $filename, $code)
	{
		$list = $this->listFiles($id);
		foreach($list as &$file)
		{
			if($file["filename"] == $filename)
			{
				$file["code"] = $code;
				$this->setFilesById($id, $list);
				return json_encode(array("success" => true));
			}
		}
		return json_encode(array("success" => false));
		
	}
	
	public function deleteFileAction($id, $filename)
	{
		$list = $this->listFiles($id);
		foreach($list as $key=>$file)
		{
			if($file["filename"] == $filename)
			{
				unset($list[$key]);
				$this->setFilesById($id, $list);
				return json_encode(array("success" => true));
			}
		}
		return json_encode(array("success" => false, "id" => $id, "filename" => $filename));
	}

	public function renameFileAction($id, $filename, $new_filename)
	{
		$list = $this->listFiles($id);
		foreach($list as $key=>$file)
		{
			if($file["filename"] == $filename)
			{
				$list[$key]["filename"] = $new_filename;
				$this->setFilesById($id, $list);
				return json_encode(array("success" => true));
			}
		}
		return json_encode(array("success" => false, "id" => $id, "filename" => $filename));
	}

	public function getProjectById($id)
	{
	    $dm = $this->dm;
		$pf = $dm->getRepository('AceProjectBundle:ProjectFiles')->find($id);
		if(!$pf)
		{
	        throw $this->createNotFoundException('No projectfiles found with id: '.$id);
		}
		
		return $pf;
	}

	public function setFilesById($id, $files)
	{
		$pf = $this->getProjectById($id);
		$pf->setFiles($files);
		$pf->setFilesTimestamp(new \DateTime);
	    $dm = $this->dm;
	    $dm->persist($pf);
	    $dm->flush();
	}

	private function listFiles($id)
	{
		$pf = $this->getProjectById($id);

		$list = $pf->getFiles();
		return $list;
	}

	public function __construct(DocumentManager $documentManager)
	{
	    $this->dm = $documentManager;
	}
}

