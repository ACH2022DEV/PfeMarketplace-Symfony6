<?php

namespace App\Service;


use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class Helpers
{
    /**
     * Convert an array of objects to two-dimensional associative array
     * !!!!!!!!!!!!   NOT RECOMMENDED TO USE THIS. IT WORKS ONLY IF FOR SIMPLE OBJECT ARRAY !!!!!!!!!!!!
     * @param array $tab
     * @return array
     */
    public function convert_ObjectArray_to_2DArray(array $tab_objects) : array{
        $data = array();
        foreach ($tab_objects as $object){
            $array = array();
            $temp = (array) $object;
            foreach ($temp as $k => $v) {
                $k = preg_match('/^\x00(?:.*?)\x00(.+)/', $k, $matches) ? $matches[1] : $k; // cleaning
                if(!is_object($v))
                    $array[$k] = $v;
            }
            $data[]  = $array;
        }
        return $data;
    }

    /**
     * Build tree from a 2D array, recursively
     * @param array $elements
     * @param $parentId
     * @return array
     */
    public function buildTree(array $elements, $parentId = null): array
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[$element['id']] = $element;
                unset($elements[$element['id']]);
            }
        }
        return $branch;
    }

    public function uploadFile($File, $directory, SluggerInterface $slugger): string
    {
        $originalFilename = pathinfo($File->getClientOriginalName(), PATHINFO_FILENAME);
        // this is $File->getClientOriginalName() to safely include the file name as part of the URL
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $File->guessExtension();
        // Move the file to the directory where brochures are stored
        try {
            $File->move(
                $directory,
                $newFilename
            );
        } catch (\Exception $e) {
            throw  new \Exception($e->getMessage());
        }
        return $newFilename;
    }


    //insert file to database
    public  function  insertFile(array $files ,$directory , $entity_data ,  FileDataRepository $fileDataRepository , $slugger ): void
    {
        if ($files) {
            foreach ($files as $key => $file) {
                $fileDataHox = new FileData();
                $fileExtention = $file->guessExtension();
                try {
                    $newFilename = $this->uploadFile($file, $directory, $slugger);
                    $fileDataHox->setAlt(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $fileDataHox->setDirectoryPath($directory);
                    $fileDataHox->setName($newFilename);
                    $fileDataHox->setProvider($entity_data);
                    $fileDataHox->setExtension($fileExtention);
                    if ($key == 0 &&  in_array($fileExtention , ['png','jpeg','gif' , 'svg+xml'] )) {
                        $fileDataHox->setOrdre(1);
                    } else {
                        $fileDataHox->setOrdre(0);
                    }
                    $fileDataRepository->add($fileDataHox, true);

                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            }
        }
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function registeUser($user, EntityManagerInterface $entityManager, $type): void
    {
        $entityManager->getRepository($type)->add($user, true);
        $entityManager->persist($user);
        $entityManager->flush();
        // generate a signed url and email it to the user

    }
    //gestion des carecteristiques
    public function facilityManagement( $checked, $entity, $facility, EntityManagerInterface $em): void
    {
        if (empty($checked)) {
            foreach ($facility as $item) {
                $entity->removeFacility($item);
                try {
                    $em->persist($entity);
                    $em->flush();
                } catch (\Doctrine\ORM\Exception\ORMException $e) {
                    throw new \Exception($e->getMessage());
                }
            }

        } else {
            foreach ($facility as $item) {
                $entity->removeFacility($item);
                try {
                    $em->persist($entity);
                    $em->flush();
                } catch (\Doctrine\ORM\Exception\ORMException $e) {
                    throw new \Exception($e->getMessage());
                }
            }
        }
        foreach ($checked as $item) {
            $item->setIcon('test');
            $entity->addFacility($item);
            try {
                $em->persist($entity);
                $em->flush();
            } catch (\Doctrine\ORM\Exception\ORMException $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }

    function generateRandomPassword($length = 12): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }
}