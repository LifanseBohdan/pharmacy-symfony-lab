<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:admin:create',
    description: 'Створює користувача з роллю ROLE_ADMIN',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::OPTIONAL, 'Email адміністратора');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $email = $input->getArgument('email');
        if (!$email) {
            $emailQuestion = new Question('Введіть email адміністратора: ');
            $emailQuestion->setValidator(function (?string $value): string {
                if (!$value || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new \RuntimeException('Введіть коректний email.');
                }

                return $value;
            });
            $email = $helper->ask($input, $output, $emailQuestion);
        }

        $existing = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existing) {
            $io->error(sprintf('Користувач з email "%s" вже існує.', $email));

            return Command::FAILURE;
        }

        $passwordQuestion = new Question('Введіть пароль адміністратора: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $passwordQuestion->setValidator(function (?string $value): string {
            if (!$value || strlen($value) < 6) {
                throw new \RuntimeException('Пароль має містити щонайменше 6 символів.');
            }

            return $value;
        });

        $plainPassword = $helper->ask($input, $output, $passwordQuestion);

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Адміністратора "%s" успішно створено!', $email));

        return Command::SUCCESS;
    }
}
