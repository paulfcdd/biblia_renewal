<?php


namespace App\Command;


use App\Entity\Book;
use App\Entity\BookGroup;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAdminUserCommand extends Command
{
    /** @var UserPasswordEncoderInterface  */
    public $passwordEncoder;
    /** @var string  */
    public $name;
    /** @var EntityManagerInterface  */
    public $entityManager;
    /** @var ValidatorInterface  */
    public $validator;
    /** @var string  */
    protected static $defaultName = 'app:admin:create';
    /** @var bool */
    private $requirePassword;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        bool $requirePassword = true,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->name = $name;
        $this->passwordEncoder = $passwordEncoder;
        $this->requirePassword = $requirePassword;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add new user')
            ->setHelp('This command allows you to create a user...')
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();

        $user
            ->setEmail($input->getArgument('email'))
            ->setPassword($this->passwordEncoder->encodePassword($user, $input->getArgument('password')))
            ->setRoles(['ROLE_ADMIN'])
            ;

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {

            $errorsString = (string) $errors;

            $output->writeln($errorsString);

            return 1;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Admin ' . $user->getEmail() . ' was successfully created');

        return 0;
    }
}
