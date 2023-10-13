<?php

namespace App\Controller;

use App\Entity\Employee;
use Symfony\Component\Intl\Countries;
use libphonenumber\PhoneNumberFormat;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmployeesController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EmployeeRepository $employeeRepo): Response
    {
        $employees = $employeeRepo->findAll();

        return $this->render('employees/index.html.twig', compact('employees'));
    }

    #[Route('/employees/create', name: 'app_employees_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $employee = new Employee;

        $form = $this->createFormBuilder($employee)
            ->add('name', TextType::class)
            ->add(
                'phoneNumber',
                PhoneNumberType::class,
                [
                    'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                    'country_choices' => Countries::getCountryCodes(),
                    'preferred_country_choices' => ['CA', 'SN', 'CI']
                ]
            )
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('employees/create.html.twig', compact('form'));
    }
}
