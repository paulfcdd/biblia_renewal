<?php


namespace App\Command\DatabaseMapper;

use App\Entity\User;
use Symfony\Component\Console\Helper\ProgressBar;

class UserMapper extends AbstractMapper
{
    private const USER_ROLE_MAPPER = [
        'reader' => ['ROLE_READER'],
        'editor' => ['ROLE_USER'],
        'admin' => ['ROLE_ADMIN'],
    ];
    private const DEFAULT_PASSWORD = 'qwerty';

    public function run()
    {
        $sourceTableData = $this->getUserDataToImport();
        $this->output->writeln('<info>ИМПОРТ ПОЛЬЗОВАТЕЛЕЙ</info>');
        $progressBar = new ProgressBar($this->output, count($sourceTableData));

        foreach ($sourceTableData as $sourceUser) {
//            $user = new User();
//            $user
//                ->setEmail($sourceUser['email'])
//                ->setRoles($sourceUser['role'])
//                ->setPassword($this->encoder->encodePassword($user, self::DEFAULT_PASSWORD))
//                ;
//
//            $this->entityManager->persist($user);
//            $this->entityManager->flush();

            $query = "INSERT INTO ".$this->targetDb.".users VALUES (
                    NULL, 
                    '".str_replace("'", "''", $sourceUser['email'])."', 
                    '".str_replace("'", "''", json_encode($sourceUser['role']))."', 
                    '".str_replace("'", "''", $this->encoder->encodePassword(new User(), self::DEFAULT_PASSWORD))."' 
                    )";

            $this->connection->prepare($query)->execute();

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->output->writeln('');

        return 0;
    }

    private function getUserDataToImport()
    {
        $query = "select users.email as email, users.name username, roles.name role, users.created_at created_at
                    from $this->sourceDb.users users
                    left join $this->sourceDb.role_user on users.id = role_user.user_id
                    left join $this->sourceDb.roles on role_user.role_id = roles.id
                    where 1;
                  ";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $oldDbUsers = $stmt->fetchAll();
        $results = [];

        array_walk($oldDbUsers, function (&$item) use (&$results) {
            $item['role'] = self::USER_ROLE_MAPPER[$item['role']];
            $results[] = $item;
        });

        return $results;
    }
}
