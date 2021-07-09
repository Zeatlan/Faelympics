<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Berni
        // Okplabs
        // Shinori
        // CXLP
        // ZelphiX
        // Byakuran
        // Cyriod
        // nalo
        $users = [
            ['Zeatlan', '87SB]RWb', 'Zeatlan#0727'],
            ['Berni', '5AA.Wb`6t-W)', 'Berni#8538'],
            ['Okplabs','287SB]R;?jdA', 'Papayapa#8068'],
            ['Shinori', 'UrwWZ>e$92+)', 'shinori#1379'],
            ['CXLP', 'e*3W,:Q5PjD9', 'CXLP#7441'],
            ['Zelphix', 'n-z98u:yCc.7', 'ZelphiX#1781'],
            ['Byakuran', ')Bs$jL_/=}4$', 'Byakuran#3038'],
            ['Cyriod', '*%bTm8-3h%\{', 'Cyriod#2464'],
            ['nalo', 'xG8#9*"]LWVc', 'nalo_#3437']
        ];

        foreach($users as $key => $value){
            $user = new User();
            $user->setUsername($value[0]);
            if($value[0] == 'Zeatlan')
                $user->setRole('ROLE_SUPER_ADMIN');
            else
                $user->setRole('ROLE_ADMIN');
            $user->setPassword(
                $this->encoder->encodePassword($user, $value[1])
            );
            $user->setAvatar('/img/avatar/'. strtolower($value[0]) .".jpg");
            $user->setTag($value[2]);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
