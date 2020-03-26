<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoListController extends AbstractController
{
    /**
     * @Route("/", name="to_do_list")
     */
    public function index()
    {
        /*return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ToDoListController.php',
        ]);*/
        //return $this->render('index.html.twig');
        $tasks=$this->getDoctrine()->getRepository(Task::class)->findBy([],['id'=>'desc']);
        dump($tasks);
        return $this->render('index.html.twig',['tasks'=>$tasks]);
    }

    /**
     * @Route("/create", name="create_task",methods={"POST"})
     */
    public function create(Request $request)
    {
        $title = trim($request->request->get('title'));
        if(empty($title)){
            return $this->redirectToRoute('to_do_list');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $task= new Task();
        $task->setTitle($title);
        //$task->setStatus(1);
        $entityManager->persist($task);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        return $this->redirectToRoute('to_do_list');
        //return new Response('Saved new product with id '.$task->getId());

    }

    /**
     * @Route("/switch-status/{id}", name="switch_status")
     */
    public function switchStatus($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            throw $this->createNotFoundException(
                'No task found for id '.$id
            );
        }

        $task->setStatus(!$task->getStatus());
        $entityManager->flush();

        return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/delete/{id}", name="task_delete")
     */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            throw $this->createNotFoundException(
                'No task found for id '.$id
            );
        }

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('to_do_list');
    }
}
