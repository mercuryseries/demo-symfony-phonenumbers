<?php

namespace App\Controller;

use App\Entity\Employee;
use Symfony\Component\Intl\Countries;
use libphonenumber\PhoneNumberFormat;
use App\Repository\EmployeeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmployeesController extends AbstractController
{
    #[Route('/employees', name: 'app_employees')]
    public function index(EmployeeRepository $employeeRepo): Response
    {
        $employees = $employeeRepo->findAll();

        return $this->render('employees/index.html.twig', compact('employees'));
    }

    #[Route('/employees/create', name: 'app_employees_create')]
    public function create(Request $request): Response
    {
        $form = $this->createFormBuilder(new Employee)
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
            dd($form->getData());
        }

        return $this->render('employees/create.html.twig', compact('form'));
    }
}
