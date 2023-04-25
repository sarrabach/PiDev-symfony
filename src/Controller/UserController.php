<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Form\User2Type;
use App\Form\UserType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/user')]
class UserController extends AbstractController
{

    #[Route('/forgetpassword', name: 'app_forgetpwd')]
    public function forgetpwd(Request $request,UtilisateurRepository $repository,SessionInterface $session,MailerInterface $mailer): Response
    {
        $utilisateurController = new UserController();
        $userconnected=$utilisateurController->getDataUserConnected($session);
        $emailerror=false;

        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('userMail',TextType::class, [
                'label' => 'Email','attr'=>['class'=>'form-control']
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() )
        {
            $email=$form->get('userMail')->getData();
            $Existe =$repository->findByemail($email);
            if (!$Existe)
            {
                $emailerror=true;
            }
            else
            {
                $code=$this->randw(6);
                $uppcode=strtoupper($code);
                $this->SendCodeMail($email,$mailer,$uppcode);
                $session=$request->getSession();
                $session->set('email',$email);
                $session->set('codepwd',$uppcode);
                return $this->redirectToRoute("app_confirmcode");
            }

        }
        return $this->render('user/Auth/Requestpwd.html.twig',[
            'form' => $form->createView(),
            'emailerror'=>$emailerror,
            'userconnected'=>$userconnected
        ]);
    }
    #[Route('/confirmcode', name: 'app_confirmcode')]
    public function confirmcode(Request $request,UtilisateurRepository $repository,SessionInterface $session): Response
    {
        $utilisateurController = new UserController();
        $userconnected=$utilisateurController->getDataUserConnected($session);
        $codeerror=true;
        $code=null;
        if ($session->has('codepwd')){
            $codepwd = $session->get('codepwd');
        }
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('userMail',TextType::class, [
                'label' => 'Code','attr'=>['class'=>'form-control']
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            $code=$form->get('userMail')->getData();
            if ($code==$codepwd)
            {
                return $this->redirectToRoute("app_changepwd");

            }else
            {
                $codeerror=false;
            }

        }
        return $this->render('user/Auth/confirmcode.html.twig',[
            'form' => $form->createView(),
            'emailerror'=>$codeerror,
            'userconnected'=>$userconnected
        ]);
    }
    #[Route('/changepwd', name: 'app_changepwd')]
    public function changepwd(Request $request,UtilisateurRepository $repository,SessionInterface $session,EntityManagerInterface $entityManager): Response
    {
        $email = $session->get('email');
        $user=new User();
        $passwordInv=false;
        $form = $this->createFormBuilder($user)
            ->add('username',PasswordType::class, [
                'label' => 'Nouveau Mot de passe','attr'=>['class'=>'form-control']
            ])
            ->add('password',PasswordType::class, [
                'label' => 'Repeter votre mot de passe','attr'=>['class'=>'form-control']
            ])

            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            $pwd1=$form->get('username')->getData();
            $pwd2=$form->get('password')->getData();
            if ($pwd1==$pwd2)
            {
            $user=$repository->findByemail($email);
            $user->setPassword(sha1($pwd1));
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute("app_login");
            }
            else
            {
                $passwordInv=true;
            }
        }
        return $this->render('user/Auth/ChangePassword.html.twig',[
            'form' => $form->createView(),
            'passwordInv'=>$passwordInv
        ]);
    }
    #[Route('/login', name: 'app_login')]
    public function login(Request $request,UtilisateurRepository $repository,SessionInterface $session): Response
    {
        $utilisateurController = new UserController();
        $userconnected=$utilisateurController->getDataUserConnected($session);

        $emailerror=false;

        $session=$request->getSession();

        //If session existe
        if ($session->has('user')){
            $user1=$this->getDataUserConnected($session);

        }
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('username',TextType::class, [
                'label' => 'UserName','attr'=>['class'=>'form-control']
            ])
            ->add('password',PasswordType::class, [
                'label' => 'Password','attr'=>['class'=>'form-control']
            ])

            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() )
        {
            $pwd = $user->getPassword();
            $login = $user->getUsername();
          

            $Existe =$repository->findOneBy(array('username'=>$login,'password'=>sha1($pwd)));

            if (!$Existe)
            {
                $emailerror=true;
            }else
            {
                $myObj = new stdClass();
                $myObj->Id = $Existe->getIdUser();
                $myObj->nom = $Existe->getUserLastname();
                $myObj->prenom = $Existe->getUserFirstname();
                $myObj->email = $Existe->getUserMail();
                $myObj->login = $Existe->getUsername();
                $myObj->rank = $Existe->getRole();
                $myObj->location = $Existe->getNationality();
                $myObj->tel = $Existe->getUserPhone();
                $myJSON = json_encode($myObj);

                $obj = json_decode($myJSON,true);
                $session->set('user', $obj);

                return $this->redirectToRoute("app_user_index");
            }
        }
       /* return $this->render('user/login.html.twig',[
            'user' => $user,
            'form' => $form->createView(),
            'emailerror'=>$emailerror,
            'userconnected'=>$userconnected
        ]);
*/
        return $this->render('user/Auth/login.html.twig',[
            'user' => $user,
            'form' => $form->createView(),
            'emailerror'=>$emailerror,
            'userconnected'=>$userconnected
        ]);
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->clear();
        return $this->redirectToRoute('app_user_index');
    }
    #[Route('/signup', name: 'app_user_register')]
    public function signup(SessionInterface $session,Request $request,EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $formTourist = $this->createForm(User2Type::class,$user);
        $formTourist->remove('submit');
        $formTourist->remove('role');
        $formTourist->handleRequest($request);
        $formGuide = $this->createForm(User1Type::class,$user);
        $formGuide->remove('submit');
        $formGuide->remove('role');
        $formGuide->handleRequest($request);
        if ($formGuide->isSubmitted()){
            $user->setRole("Guide");
            $hashPwd=sha1($user->getPassword());
            $user->setPassword($hashPwd);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_index');

        }
        if ($formTourist->isSubmitted()){
            $user->setRole("Touriste");
            $hashPwd=sha1($user->getPassword());
            $user->setPassword($hashPwd);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_index');

        }
        return $this->render('user/Auth/signup.html.twig',['formTouriste'=>$formTourist->createView(),'formGuide'=>$formGuide->createView()]);
    }

    #[Route('/updateprofile', name: 'app_user_updateprofile')]
    public function updateprofile(SessionInterface $session,Request $request,EntityManagerInterface $entityManager,UserController $utilisateurController,UtilisateurRepository $utilisateurRepository): Response
    {

        $userconnected = $utilisateurController->getDataUserConnected($session);
        $user=$utilisateurRepository->findByIDobject($userconnected->getIdUser());

        if ($user->getRole()=="Touriste")
        {
            $form = $this->createForm(User2Type::class, $user);

            $form->remove("role");
            $form->remove("submit");
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid())
            {
                $user=$form->getData();
                $user->setRole("Touriste");
                $hashPwd=sha1($user->getPassword());
                $user->setPassword($hashPwd);
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_user_index');
            }
        }elseif ($user->getRole()=="Guide")
        {
            $form = $this->createForm(User1Type::class, $user);
            $form->remove("role");
            $form->remove("submit");
            $form->handleRequest($request);
            if ($form->isSubmitted()  && $form->isValid())
            {
                $user->setRole("Guide");
                $hashPwd=sha1($user->getPassword());
                $user->setPassword($hashPwd);
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_user_index');
            }
        }
        else
        {
            return $this->redirectToRoute('app_user_index');
        }
        return $this->render('user/Auth/updateprofil.html.twig', [
            'form' => $form->createView(),
            'userconnected'=>$userconnected
        ]);
    }


    #[Route('/approve', name: 'app_user_approve')]
    public function add(Request $request, EntityManagerInterface $entityManager,SessionInterface $session,UtilisateurRepository $utilisateurRepository,UserController $utilisateurController): Response
    {

        $userconnected = $utilisateurController->getDataUserConnected($session);
        $user=$utilisateurRepository->findByIDobject($userconnected->getIdUser());
        if ($user->getRole()=="Guide")
        {
            $defaultCityName = $user->getCityname(); // replace with your default value

            $form = $this->createFormBuilder($user)
                ->add('cityname',TextType::class, [
                    'label' => 'City','attr'=>['class'=>'form-control','disabled' => true],
                    'data' => $defaultCityName, // set default value
                ])
                ->add('datebeg',DateType::class, ['data'   => new \DateTime(),
                    'label' => 'Date de debut','attr'=>['class'=>'form-control','min' => ( new \DateTime() )->format('Y-m-d H:i:s')]
                ])
                ->add('dateend',DateType::class, ['data'   => new \DateTime(),
                'label' => 'Date de debut','attr'=>['class'=>'form-control','min' => ( new \DateTime() )->format('Y-m-d H:i:s')]
            ])
                ->getForm();
        }
        else
        {
            $form = $this->createFormBuilder($user)
                ->add('cityname',TextType::class, [
                    'label' => 'City','attr'=>['class'=>'form-control']
                   // set default value
                ])
                ->add('datebeg',DateType::class, ['data'   => new \DateTime(),
                'label' => 'Date de debut','attr'=>['class'=>'form-control','min' => ( new \DateTime() )->format('Y-m-d H:i:s')]
            ])
                ->add('dateend',DateType::class,  ['data'   => new \DateTime(),
                'label' => 'Date de debut','attr'=>['class'=>'form-control','min' => ( new \DateTime() )->format('Y-m-d H:i:s')]
            ])
                ->getForm();
        }


        $form->handleRequest($request);
        if ($form->isSubmitted() )
        {
            if ($user->getRole()=="Guide")
            {
                $user->setDatebeg($form->get('datebeg')->getData());
                $user->setDateend($form->get('dateend')->getData());
                $user->setCityname($defaultCityName);
                $entityManager->persist($user);
                $entityManager->flush();
            }
            elseif ($user->getRole()=="Touriste")
            {
                $user->setCityname($form->get('cityname')->getData());
                $user->setDatebeg($form->get('datebeg')->getData());
                $user->setDateend($form->get('dateend')->getData());
                $userguide=$utilisateurRepository->findGuidewithdates($form->get('datebeg')->getData(),$form->get('dateend')->getData(),$form->get('cityname')->getData());
                if ($userguide)
                {
                    $userguide->setDisponibility(1);
                    $userguide->setIdRelation($user->getIdUser());
                    $userguide->setCityname($form->get('cityname')->getData());
                    $entityManager->persist($userguide);
                    $entityManager->flush();
                    $user->setDatebeg($form->get('datebeg')->getData());
                    $user->setDateend($form->get('dateend')->getData());
                    $user->setIdRelation($userguide->getIdUser());
                    $user->setDisponibility(1);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    //SMS
                    $twilioAccountSid = 'AC1f62dfc0c094269ec0447893fd971c77';
                    $twilioAuthToken = '5922a7d53abebea6b5b444ef851ce82b';
                    $client = new Client($twilioAccountSid, $twilioAuthToken);
                    $client->messages->create(
                    // Where to send a text message (your cell phone?)
                        '+216'.$user->getUserPhone(),
                        array(
                            'from' => '+16813217199',
                            'body' => 'Le numero de votre guide est : '.$userguide->getUserPhone()
                        )
                    );
                }
                else
                {
                    $user->setDatebeg($form->get('datebeg')->getData());
                    $user->setDateend($form->get('dateend')->getData());
                    $entityManager->persist($user);
                    $entityManager->flush();
                }
            }
        }
        return $this->render('user/Auth/add.html.twig', [
            'userconnected'=>$userconnected,
            'form'=>$form
        ]);

    }

    /**
     * @Route("/search", name="app_user_search")
     */
    public function search(Request $request, UtilisateurRepository $userRepository, SerializerInterface $serializer)
    {
        // Get the search query from the request parameters
        $query = $request->query->get('query');

        // Search for users matching the query
        $users = $userRepository->createQueryBuilder('u')
            ->where('u.userFirstname LIKE :query')
            ->orWhere('u.userLastname LIKE :query')
            ->orWhere('u.userMail LIKE :query')
            ->orWhere('u.userPhone LIKE :query')
            ->orWhere('u.username LIKE :query')
            ->orWhere('u.lang1 LIKE :query')
            ->orWhere('u.lang2 LIKE :query')
            ->orWhere('u.lang3 LIKE :query')
            ->orWhere('u.cityname LIKE :query')
            ->orWhere('u.nationality LIKE :query')
            ->orWhere('u.langue LIKE :query')
            ->orWhere('u.role LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        // Serialize the users to HTML
        $json = $serializer->serialize($users, 'json');

// Return the search results as JSON
        return new JsonResponse($json);
    }



    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        $users=null;
        $utilisateurController = new UserController();
        $userconnected=$utilisateurController->getDataUserConnected($session);
        if ($userconnected->getIdUser()==null)
        {
            return $this->redirectToRoute('app_login');
        }
        if ($userconnected->getRole()=="Admin")
        {
        $users = $entityManager
            ->getRepository(User::class)
            ->findAll();
        }
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'userconnected'=>$userconnected
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        $utilisateurController = new UserController();
        $userconnected=$utilisateurController->getDataUserConnected($session);
        if ($userconnected->getIdUser()==null)
        {
            return $this->redirectToRoute('app_login');
        }
        if ($userconnected->getRole()!="Admin")
        {
            return $this->redirectToRoute('app_user_index');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->remove("role");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRole("Admin");
            $hashPwd=sha1($user->getPassword());
            $user->setPassword($hashPwd);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
            'userconnected'=>$userconnected
        ]);
    }

    #[Route('/{idUser}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user,SessionInterface $session): Response
    {
        $utilisateurController = new UserController();
        $userconnected=$utilisateurController->getDataUserConnected($session);
        if ($userconnected->getIdUser()==null)
        {
            return $this->redirectToRoute('app_login');
        }
        if ($userconnected->getRole()!="Admin")
        {
            return $this->redirectToRoute('app_user_index');
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,'userconnected'=>$userconnected
        ]);
    }

    #[Route('/{idUser}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        $utilisateurController = new UserController();
        $userconnected=$utilisateurController->getDataUserConnected($session);
        if ($userconnected->getIdUser()==null)
        {
            return $this->redirectToRoute('app_login');
        }
        if ($userconnected->getRole()!="Admin")
        {
            return $this->redirectToRoute('app_user_index');
        }
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashPwd=sha1($user->getPassword());
            $user->setPassword($hashPwd);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,'userconnected'=>$userconnected
        ]);
    }

    #[Route('/{idUser}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        $utilisateurController = new UserController();
        $userconnected=$utilisateurController->getDataUserConnected($session);
        if ($userconnected->getIdUser()==null)
        {
            return $this->redirectToRoute('app_login');
        }
        if ($userconnected->getRole()!="Admin")
        {
            return $this->redirectToRoute('app_user_index');
        }
        if ($this->isCsrfTokenValid('delete'.$user->getIdUser(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }









    public function getDataUserConnected(SessionInterface $session)
    {
        $userConnected = new User();
        if ($session->has('user')) {
            $user = ($session->get('user'));
            $ar = json_encode($user);
            $aee = json_decode($ar);
            $userConnected->setIdUser($aee->Id);
            $userConnected->setUserLastname($aee->nom);
            $userConnected->setUserFirstname($aee->prenom);
            $userConnected->setUserMail($aee->email);
            $userConnected->setUsername($aee->login);
            $userConnected->setRole($aee->rank);
            $userConnected->setCityname($aee->location);
            $userConnected->setUserPhone($aee->tel);
        }
        return $userConnected;
    }
    function randw($length){
        return substr(str_shuffle("qwertyuiopasdfghjklzxcvbnm1234567890"),0,$length);
    }

    private function SendCodeMail(?string $email, MailerInterface $mailer,?string $code)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('roadrevel77@gmail.com', 'RoadRevel code'))
            ->to($email)
            ->subject('Code de recuperation')
            ->html('<p>Bonjour Monsieur/Madame </p><p>Votre code de verifivation</p>'.$code
            );
        ;
        $mailer->send($email);
    }


}
