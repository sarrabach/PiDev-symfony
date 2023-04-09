<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\TouristType;
use App\Form\AdminType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
   

    #[Route('/addGuide', name: 'addUser')]
    public function add(Request $req)
    {
        $class = new User();
        $form = $this->createForm(UserType::class,$class);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($class);
            $em->flush();
            return $this->redirectToRoute('listuser');
        }
        return $this->render('user/add.html.twig',['formClass'=>$form->createView()]);
    }

    

    #[Route('/listuser', name: 'listuser')]
    public function list(UserRepository $c):Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $users = $c->findAll();
        return $this->render('user/list.html.twig',['list'=>$users]);
    }

    #[Route('/updateUser/{id}', name: 'updateUser')]
    public function update(Request $req,$id,UserRepository $rep)
    {/* $this->getDoctrine()->getRepository(Classroom::class)*/
        $class = $rep->find($id);
        $form = $this->createForm(UserType::class,$class);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listuser');
        }
        return $this->render('user/edit.html.twig',['formClass'=>$form->createView()]);
    }

    #[Route('/delete/{id}', name:'delete')]
     
    public function delete($id,EntityManagerInterface $manager)
    {
        $manager->remove($this->getDoctrine()->getRepository(User::class)->find($id));
        $manager->flush();
        return $this->redirectToRoute('listuser');
    } 
    #[Route('/addTourist', name: 'addTourist')]
    public function addTourist(Request $req)
    {
        $class = new User();
        $form = $this->createForm(TouristType::class,$class);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($class);
            $em->flush();
            return $this->redirectToRoute('listtourist');
        }
        return $this->render('user/addTourist.html.twig',['formClassT'=>$form->createView()]);
    }

    #[Route('/listTourist', name: 'listtourist')]
    public function listTourist(UserRepository $c):Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $users = $c->findAll();
        return $this->render('user/listTourist.html.twig',['list'=>$users]);
    }

    #[Route('/updateTourist/{id}', name: 'updateTourist')]
    public function updateTourist(Request $req,$id,UserRepository $rep)
    {/* $this->getDoctrine()->getRepository(Classroom::class)*/
        $class = $rep->find($id);
        $form = $this->createForm(TouristType::class,$class);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listtourist');
        }
        return $this->render('user/editTourist.html.twig',['formClassT'=>$form->createView()]);
    }

    #[Route('/deleteTourist/{id}', name:'deleteTourist')]
     
    public function deleteTourist($id,EntityManagerInterface $manager)
    {
        $manager->remove($this->getDoctrine()->getRepository(User::class)->find($id));
        $manager->flush();
        return $this->redirectToRoute('listtourist');
    } 

    #[Route('/addAdmin', name: 'addAdmin')]
    public function addAdmin(Request $req)
    {
        $class = new User();
        $form = $this->createForm(AdminType::class,$class);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($class);
            $em->flush();
            return $this->redirectToRoute('listadmins');
        }
        return $this->render('user/addAdmin.html.twig',['formClassA'=>$form->createView()]);
    }

    #[Route('/listAdmins', name: 'listadmins')]
    public function listAdmins(UserRepository $c):Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $users = $c->findAll();
        return $this->render('user/listAdmins.html.twig',['list'=>$users]);
    }

    #[Route('/updateAdmin/{id}', name: 'updateAdmin')]
    public function updateAdmin(Request $req,$id,UserRepository $rep)
    {/* $this->getDoctrine()->getRepository(Classroom::class)*/
        $class = $rep->find($id);
        $form = $this->createForm(AdminType::class,$class);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listadmins');
        }
        return $this->render('user/editAdmin.html.twig',['formClassA'=>$form->createView()]);
    }
    #[Route('/deleteAdmin/{id}', name:'deleteAdmin')]
     
    public function deleteAdmin($id,EntityManagerInterface $manager)
    {
        $manager->remove($this->getDoctrine()->getRepository(User::class)->find($id));
        $manager->flush();
        return $this->redirectToRoute('listadmins');
    } 
}



   

   

